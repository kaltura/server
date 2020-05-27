<?php
/**
 * @package plugins.caption
 * @subpackage Scheduler
 */
class KAsyncConvertCaptionAsset extends KJobHandlerWorker
{
	// time regex is: xx:dd:dd:dd  while xx is 2 digits except from 00
	const TIME_REGEX = '/(0[1-9]|[1-9]0|[1-9][1-9])(:\d\d){3}/';

	//time regex is: 00:dd:dd:dd
	const TIME_REGEX_STARTS_WITH_00 = '/00(:\d\d){3}/';

	/*
	 * @var KalturaCaptionClientPlugin
	 */
	private $captionClientPlugin = null;

	private $formatToName = array(CaptionType::SRT => 'srt' , CaptionType::DFXP => 'dfxp', CaptionType::WEBVTT => 'webvtt', CaptionType::SCC =>'scc');
	private $formatToExtensionName = array(CaptionType::SRT => 'srt' , CaptionType::DFXP => 'dfxp', CaptionType::WEBVTT => 'vtt', CaptionType::SCC =>'scc');

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CONVERT_CAPTION_ASSET;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->convertCaption($job, $job->data);
	}

	/***
	 * @param KalturaBatchJob $job
	 * @param KalturaConvertCaptionAssetJobData $data
	 * @return KalturaBatchJob|mixed
	 * @throws Exception
	 * @throws kApplicativeException
	 */
	protected function convertCaption(KalturaBatchJob $job, KalturaConvertCaptionAssetJobData $data)
	{
		$this->updateJob($job, "Start parsing caption asset [$data->captionAssetId]", KalturaBatchJobStatus::QUEUED);
		$this->captionClientPlugin = KalturaCaptionClientPlugin::get(self::$kClient);

		if (!array_key_exists($data->fromType, $this->formatToName) || !(array_key_exists($data->toType, $this->formatToName)))
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNSUPPORTED_FORMAT_TYPES', "Error: " . 'UNSUPPORTED_FORMAT_TYPES', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$content = kEncryptFileUtils::getEncryptedFileContent($data->fileLocation, $data->fileEncryptionKey, self::getConfigParam('encryption_iv'));
		if (!$content)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_GET_FILE', "Error: " . 'UNABLE_TO_GET_FILE', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}
		if ($data->fromType == CaptionType::SCC && $data->toType == CaptionType::SRT)
		{
			$this->convertSccTimeStamp($content);
		}
		$convertedContent = $this->convertContent($content, $this->formatToName[$data->fromType], $this->formatToName[$data->toType]);
		if ($convertedContent === false)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_PARSE_ASSET', "Error: " . 'UNABLE_TO_PARSE_ASSET', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		return $this->handleConvertedContent($job, $data, $convertedContent);
	}

	//converting the time stamps - will start at 00:dd:dd:dd instead of 01:dd:dd:dd
	protected function convertSccTimeStamp(&$content)
	{
		if (preg_match(self::TIME_REGEX_STARTS_WITH_00, $content, $matches))
		{
			return;
		}
		if(preg_match_all(self::TIME_REGEX, $content, $matches))
		{
			$replacingMatches = array();
			foreach ($matches[0] as $match)
			{
				$colonFirstPos = strpos($match, ':');
				$newHour = '0'. intval(substr($match, 0, $colonFirstPos)) - 1;
				$replacingMatches[] = $newHour . substr($match, $colonFirstPos);
			}
			$content = str_replace($matches[0], $replacingMatches, $content);
		}
	}

	/***
	 * @param $job
	 * @param $data
	 * @param $content
	 * @return mixed
	 * @throws kApplicativeException
	 */
	private function handleConvertedContent($job, $data, $content)
	{
		self::impersonate($job->partnerId);
		$captionAsset = $this->captionClientPlugin->captionAsset->get($data->captionAssetId);
		if (!$captionAsset)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_GET_ORIGINAL_ASSET', "Error: " . 'UNABLE_TO_GET_ORIGINAL_ASSET', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$res = $this->deleteCaptionAsset($data->captionAssetId);
		if (!$res)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_DELETE_ORIGINAL_ASSET', "Error: " . 'UNABLE_TO_DELETE_ORIGINAL_ASSET', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$captionsCreated = $this->cloneCaptionAssetToSrtAndSetContent($captionAsset->entryId, $captionAsset, $content,$data->toType, $this->formatToExtensionName[$data->toType]);
		self::unimpersonate();
		if ($captionsCreated)
		{
			$this->closeJob($job, null, null, "Finished parsing " . $this->formatToName[$data->fromType]. " captions to " . $this->formatToName[$data->toType], KalturaBatchJobStatus::FINISHED);
			return $job;
		}
		else
			throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS, "UNABLE_TO_CREATE_ASSET_WITH_CAPTION");
	}

	/***
	 * @param $content
	 * @param $fromType
	 * @param $toType
	 * @return bool|string
	 */
	private function convertContent($content, $fromType, $toType)
	{
		$output = null;
		$return_var = 0;
		$tempFile = tempnam(sys_get_temp_dir(), 'captionTranslation.');

		kFileBase::filePutContents($tempFile, $content);
		$script = realpath(dirname(__FILE__) . '/../../') . '/scripts/convertcaption.py';
		$cmd = self::$taskConfig->params->pythonCmd ." $script -i $tempFile -f $fromType -t $toType";
		KalturaLog::debug("Running caption conversion command: $cmd");
		exec($cmd, $output, $return_var);

		return $return_var ? false : implode("\n",$output);

	}

	/**
	 * @param $id
	 * @return bool
	 */
	private function deleteCaptionAsset($id)
	{
		try
		{
			$this->captionClientPlugin->captionAsset->delete($id);
			return true;
		}
		catch(Exception $e)
		{
			KalturaLog::info("problem with caption content-setting id - $id - " . $e->getMessage());
			return false;
		}
	}

	/***
	 * @param $entryId
	 * @param $captionAsset
	 * @param $content
	 * @param $format
	 * @param $fileExt
	 * @return bool
	 */
	private function cloneCaptionAssetToSrtAndSetContent($entryId, $captionAsset, $content, $format, $fileExt)
	{
		$newCaptionAsset = new KalturaCaptionAsset();
		$newCaptionAsset->fileExt = $fileExt;
		$newCaptionAsset->format = $format;
		$newCaptionAsset->tags = $captionAsset->tags;
		$newCaptionAsset->partnerData = $captionAsset->partnerData;
		$newCaptionAsset->partnerDescription = $captionAsset->partnerDescription;
		$newCaptionAsset->actualSourceAssetParamsIds = $captionAsset->actualSourceAssetParamsIds;
		$newCaptionAsset->language = $captionAsset->language;
		$newCaptionAsset->isDefault = $captionAsset->isDefault;
		$newCaptionAsset->label = $captionAsset->label;
		$newCaptionAsset->parentId = $captionAsset->parentId;
		$newCaptionAsset->accuracy = $captionAsset->accuracy;
		$newCaptionAsset->displayOnPlayer = $captionAsset->displayOnPlayer;

		try
		{
			$newCaptionAsset = $this->captionClientPlugin->captionAsset->add($entryId, $newCaptionAsset);
		}
		catch (Exception $e)
		{
			KalturaLog::info("problem adding srt caption asset - " . $e->getMessage());
			return false;
		}
		$contentResource = new KalturaStringResource();
		$contentResource->content = $content;
		return $this->captionClientPlugin->captionAsset->setContent($newCaptionAsset->id, $contentResource);
	}

}