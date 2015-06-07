<?php
/**
 * @package plugins.caption
 * @subpackage Scheduler
 */
class KAsyncParseMultiLanguageCaptionAsset extends KJobHandlerWorker
{
	const NUMBER_OF_LANGUAGES_LIMIT = 500;

	/*
	 * @var KalturaCaptionSearchClientPlugin
	 */
	private $captionClientPlugin = null;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::PARSE_MULTI_LANGUAGE_CAPTION_ASSET;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->parseMultiLanguage($job, $job->data);
	}
	
	protected function parseMultiLanguage(KalturaBatchJob $job, KalturaParseMultiLanguageCaptionAssetJobData $data)
	{
		KalturaLog::debug("parse Multi Language job id - ($job->id)");

		try
		{
			$this->updateJob($job, "Start parsing multi-language caption asset [$data->multiLanaguageCaptionAssetId]", KalturaBatchJobStatus::QUEUED);
		}
		catch(Exception $e)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), "Error: " . $e->getMessage(), KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$this->impersonate($job->partnerId);
		$this->captionClientPlugin = KalturaCaptionClientPlugin::get(self::$kClient);
		$this->unimpersonate();

		$parentId = $data->multiLanaguageCaptionAssetId;
		$entryId = $data->entryId;
		$fileLoc = $data->fileLocation;

		$xmlString = file_get_contents($fileLoc);
		if (!$xmlString)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_GET_FILE' , "Error: " . 'UNABLE_TO_GET_FILE', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$xml = simplexml_load_string($xmlString);
		if (!$xml)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'INVALID_XML' , "Error: " . 'INVALID_XML', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $entryId;
		$pager = null;

		$bodyNode = $xml->body;
		if (count($bodyNode->div) > self::NUMBER_OF_LANGUAGES_LIMIT)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'EXCEEDED_NUMBER_OF_LANGUAGES' , "Error: " . "exceeded number of languages - ".self::NUMBER_OF_LANGUAGES_LIMIT, KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		try
		{
			$this->impersonate($job->partnerId);
			$result = $this->captionClientPlugin->captionAsset->listAction($filter, $pager);
			$this->unimpersonate();
		}
		catch(Exception $e)
		{   
			$this->unimpersonate();
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), "Error: " . $e->getMessage(), KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$captionChildernIds = array();
		foreach($result->objects as $caption)
		{
			if($caption->parentId == $parentId)
				$captionChildernIds[$caption->languageCode] = $caption->id;
		}

		$indexStart = strpos($xmlString,'<div');
		$indexEnd = strrpos($xmlString, '</div>', -1);

		$subXMLStart = substr($xmlString, 0, $indexStart);
		$subXMLEnd = substr($xmlString, $indexEnd + 6);

		foreach ($bodyNode->div as $divNode)
		{
			$onlyUpdate = false;
			$xmlDivNode = $divNode->asXml();
			$langPos = strpos($xmlDivNode, "xml:lang=");
			$languageShort = substr($xmlDivNode, $langPos + 10, 2);
			if(isset($captionChildernIds[$languageShort]))
			{
				$id = $captionChildernIds[$languageShort];
				KalturaLog::debug("language $languageShort exists as a child of asset $parentId");
				$onlyUpdate = true;
				unset($captionChildernIds[$languageShort]);
			}

			$completeXML = $subXMLStart . $xmlDivNode . $subXMLEnd;
			$languageShort = strtoupper($languageShort);
			$languageLong = constant('KalturaLanguage::' . $languageShort);

			$captionAsset = new KalturaCaptionAsset();
			$captionAsset->fileExt = 'xml';
			$captionAsset->language = $languageLong;
			$captionAsset->format = KalturaCaptionType::DFXP;
			$captionAsset->parentId = $parentId;

			$contentResource = new KalturaStringResource();
			$contentResource->content = $completeXML;

			if (!$onlyUpdate)
				$this->addCaption($entryId,$captionAsset, $contentResource);
			else
				$this->setCaptionContent($id, $contentResource);				
	
		}
		self::deleteCaptions($captionChildernIds);

		$this->closeJob($job, null, null, "Finished parsing", KalturaBatchJobStatus::FINISHED);

		return $job;
	}

	private function addCaption($entryId, $captionAsset, $contentResource)
	{
		try
		{
			$this->impersonate($job->partnerId);
			$captionCreated = $this->captionClientPlugin->captionAsset->add($entryId , $captionAsset);
			$this->unimpersonate();
		}
		catch(Exception $e)
		{
			$this->unimpersonate();
			$languageCode = $captionAsset->languageCode;
			KalturaLog::debug("problem with caption creation - language $languageCode - " . $e->getMessage());
			return;
		}
		$this->unimpersonate();
		$id = $captionCreated->id;
		$this->setCaptionContent($id, $contentResource);
	}

	private function setCaptionContent($id, $contentResource)
	{
		try
		{
			$this->impersonate($job->partnerId);
			$captionCreated = $this->captionClientPlugin->captionAsset->setContent($id , $contentResource);
			$this->unimpersonate();
		}
		catch(Exception $e)
		{
			$this->unimpersonate();
			KalturaLog::debug("problem with caption content-setting id - $id - language $languageLong - " . $e->getMessage());
		}
	}

	private function deleteCaptions(array $captions)
	{
		foreach ($captions as $language => $captionId)
		{
			if (isset($captions[$language]))
			{
				try
				{
					$this->impersonate($job->partnerId);
					$this->captionClientPlugin->captionAsset->delete($captionId);
					$this->unimpersonate();
				}
				catch(Exception $e)
				{
					$this->unimpersonate();
					KalturaLog::debug("problem with deleting caption id - $captionId - language $language - " . $e->getMessage());
				}
			}
		}
	}
}

