<?php
/**
 * @package plugins.caption
 * @subpackage Scheduler
 */
class KAsyncConvertCaptionAsset extends KJobHandlerWorker
{
	/*
	 * @var KalturaCaptionClientPlugin
	 */
	private $captionClientPlugin = null;

    private $formatToName = array(CaptionType::SRT => 'srt' , CaptionType::DFXP => 'dfxp', CaptionType::WEBVTT => 'webvtt', CaptionType::SCC =>'scc');

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

	protected function convertCaption(KalturaBatchJob $job, KalturaConvertCaptionAssetJobData $data)
	{
		$this->updateJob($job, "Start parsing caption asset [$data->captionAssetId]", KalturaBatchJobStatus::QUEUED);

		$this->captionClientPlugin = KalturaCaptionClientPlugin::get(self::$kClient);

		$captionAssetId = $data->captionAssetId;
		$fileLoc = $data->fileLocation;
		if (!array_key_exists($data->fromType,$this->formatToName) || !(array_key_exists($data->toType ,$this->formatToName)))
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNSUPPORTED_FORMAT_TYPES', "Error: " . 'UNSUPPORTED_FORMAT_TYPES', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$fromTypeName = $this->formatToName[$data->fromType];
		$toTypeName = $this->formatToName[$data->toType];

		$content = kEncryptFileUtils::getEncryptedFileContent($fileLoc, $data->fileEncryptionKey, kConf::get("encryption_iv"));
		if (!$content)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_GET_FILE', "Error: " . 'UNABLE_TO_GET_FILE', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$output = null;
		$return_var = 0;
		$tempFile = tempnam(sys_get_temp_dir(), 'captionTranslation.');

		kFileBase::filePutContents($tempFile, $content);
		$script = realpath(dirname(__FILE__) . '/../../') . '/scripts/convertcaption.py';
		$cmd = self::$taskConfig->params->pythonCmd ." $script -i $tempFile -f $fromTypeName -t $toTypeName";
		KalturaLog::debug("Running caption conversion command: $cmd");
		exec($cmd, $output, $return_var);

		if ($return_var)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_PARSE_ASSET', "Error: " . 'UNABLE_TO_PARSE_ASSET', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$parsedContent =  implode("\n",$output);

		self::impersonate($job->partnerId);
		$captionAsset = $this->captionClientPlugin->captionAsset->get($captionAssetId);
		if (!$captionAsset)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_GET_ORIGINAL_ASSET', "Error: " . 'UNABLE_TO_GET_ORIGINAL_ASSET', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$res = $this->deleteCaptionAsset($captionAssetId);
		if (!$res)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_DELETE_ORIGINAL_ASSET', "Error: " . 'UNABLE_TO_DELETE_ORIGINAL_ASSET', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$captionsCreated = $this->cloneCaptionAssetToSrtAndSetContent($captionAsset->entryId, $captionAsset, $parsedContent,$data->toType, $toTypeName);
		self::unimpersonate();
		if ($captionsCreated)
		{
			$this->closeJob($job, null, null, "Finished parsing $fromTypeName captions to $toTypeName", KalturaBatchJobStatus::FINISHED);
			return $job;
		}
		else
			throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS, "UNABLE_TO_CREATE_ASSET_WITH_CAPTION");
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
		$captionAsset->id = null;
		$captionAsset->entryId = null;
		$captionAsset->languageCode = null;
		$captionAsset->partnerId = null;
		$captionAsset->createdAt = null;
		$captionAsset->updatedAt = null;
		$captionAsset->version = null;
		$captionAsset->size = null;
		$captionAsset->description = null;
		$captionAsset->status = null;

		$captionAsset->fileExt = $fileExt;
		$captionAsset->format = $format;

		try
		{
			$newCaptionAsset = $this->captionClientPlugin->captionAsset->add($entryId, $captionAsset);
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