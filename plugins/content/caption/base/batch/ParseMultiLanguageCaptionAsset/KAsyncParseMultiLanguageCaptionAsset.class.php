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
		$this->updateJob($job, "Start parsing multi-language caption asset [$data->multiLanaguageCaptionAssetId]", KalturaBatchJobStatus::QUEUED);

		$this->captionClientPlugin = KalturaCaptionClientPlugin::get(self::$kClient);

		$parentId = $data->multiLanaguageCaptionAssetId;
		$entryId = $data->entryId;
		$fileLoc = $data->fileLocation;

		$xmlString = kEncryptFileUtils::getEncryptedFileContent($fileLoc, $data->fileEncryptionKey,kConf::get("encryption_iv"));
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

		self::impersonate($job->partnerId);
		$result = $this->captionClientPlugin->captionAsset->listAction($filter, $pager);

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

		$headerLanguageLong = null;
		if ($xml[0])
		{
			$headerLanguageShort = $xml[0]->attributes('xml',true);
			if($headerLanguageShort)
				$headerLanguageLong = constant('KalturaLanguage::' . strtoupper($headerLanguageShort));
		}	

		$captionsCreated = false;
		$divCounter = 0;
		foreach ($bodyNode->div as $divNode)
		{
			$divCounter++;
			$onlyUpdate = false;
			$xmlDivNode = $divNode->asXml();
			$languageShort = $divNode[0]->attributes('xml',true)->lang;
			$languageLong = null;
			if($languageShort)
				$languageLong = constant('KalturaLanguage::' . strtoupper($languageShort));

			if(is_null($languageLong))
			{
				if(is_null($headerLanguageLong))
				{
					KalturaLog::info("failed to find language in div number $divCounter");
					continue;
				}
				$languageShort = $headerLanguageShort;
				$languageLong = $headerLanguageLong;
			}

			if(isset($captionChildernIds[$languageShort]))
			{
				$id = $captionChildernIds[$languageShort];
				KalturaLog::info("language $languageShort exists as a child of asset $parentId");
				$onlyUpdate = true;
				unset($captionChildernIds[$languageShort]);
			}

			$completeXML = $subXMLStart . $xmlDivNode . $subXMLEnd;

			$captionAsset = new KalturaCaptionAsset();
			$captionAsset->fileExt = 'xml';
			$captionAsset->language = $languageLong;
			$captionAsset->format = KalturaCaptionType::DFXP;
			$captionAsset->parentId = $parentId;

			$contentResource = new KalturaStringResource();
			$contentResource->content = $completeXML;

			if (!$onlyUpdate)
				$currentCaptionCreated = $this->addCaption($entryId,$captionAsset, $contentResource);
			else
				$currentCaptionCreated = $this->setCaptionContent($id, $contentResource);				

			$captionsCreated = $captionsCreated || $currentCaptionCreated;
	
		}

		//deleting captions of languages that weren't in uploaded file
		self::deleteCaptions($captionChildernIds);
		self::unimpersonate();

		if ($captionsCreated)
		{
			$this->closeJob($job, null, null, "Finished parsing", KalturaBatchJobStatus::FINISHED);
			return $job;
		}
		else
			throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS ,"no captions created");
	}

	private function addCaption($entryId, $captionAsset, $contentResource)
	{
		try
		{
			$captionCreated = $this->captionClientPlugin->captionAsset->add($entryId , $captionAsset);
		}
		catch(Exception $e)
		{
			$languageCode = $captionAsset->languageCode;
			KalturaLog::info("problem with caption creation - language $languageCode - " . $e->getMessage());
			return false;
		}
		return $this->setCaptionContent($captionCreated->id, $contentResource);
	}

	private function setCaptionContent($id, $contentResource)
	{
		try
		{
			$this->captionClientPlugin->captionAsset->setContent($id , $contentResource);
			return true;
		}
		catch(Exception $e)
		{
			KalturaLog::info("problem with caption content-setting id - $id - " . $e->getMessage());
			return false;
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
					$this->captionClientPlugin->captionAsset->delete($captionId);
				}
				catch(Exception $e)
				{
					KalturaLog::info("problem with deleting caption id - $captionId - language $language - " . $e->getMessage());
				}
			}
		}
	}
}

