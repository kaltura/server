<?php
/**
 * @package plugins.caption
 * @subpackage Scheduler
 */
class KAsyncParseSccCaptionAsset extends KJobHandlerWorker
{
	/*
	 * @var KalturaCaptionClientPlugin
	 */
	private $captionClientPlugin = null;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::PARSE_SCC_CAPTION_ASSET;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->parseSccCaption($job, $job->data);
	}

	protected function parseSccCaption(KalturaBatchJob $job, KalturaParseSccCaptionAssetJobData $data)
	{
		$this->updateJob($job, "Start parsing scc caption asset [$data->sccCaptionAssetId]", KalturaBatchJobStatus::QUEUED);

		$this->captionClientPlugin = KalturaCaptionClientPlugin::get(self::$kClient);

		$captionAssetId = $data->sccCaptionAssetId;
		$fileLoc = $data->fileLocation;

		$content = kEncryptFileUtils::getEncryptedFileContent($fileLoc, $data->fileEncryptionKey, kConf::get("encryption_iv"));
		if (!$content)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_GET_FILE', "Error: " . 'UNABLE_TO_GET_FILE', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		self::impersonate($job->partnerId);
		$parsedContent = kSCCParser::parseToSrt($content);

		$sccCaptionAsset = $this->captionClientPlugin->captionAsset->get($captionAssetId);
		if (!$sccCaptionAsset)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_GET_ORIGINAL_SCC_ASSET', "Error: " . 'UNABLE_TO_GET_ORIGINAL_SCC_ASSET', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$res = $this->deleteCaptionAsset($captionAssetId);
		if (!$res)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_DELETE_ORIGINAL_SCC_ASSET', "Error: " . 'UNABLE_TO_DELETE_ORIGINAL_SCC_ASSET', KalturaBatchJobStatus::FAILED, $data);
			return $job;
		}

		$captionsCreated = $this->cloneCaptionAssetToSrtAndSetContent($sccCaptionAsset->entryId, $sccCaptionAsset, $parsedContent);
		self::unimpersonate();
		if ($captionsCreated)
		{
			$this->closeJob($job, null, null, "Finished parsing scc captions to srt", KalturaBatchJobStatus::FINISHED);
			return $job;
		}
		else
			throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS, "NABLE_TO_CREATE_ASSET_WITH_SRT_CAPTION");
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

	/**
	 * @param $entryId
	 * @param $captionAsset
	 * @param $content
	 * @return bool
	 */
	private function cloneCaptionAssetToSrtAndSetContent($entryId, $captionAsset, $content)
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

		$captionAsset->fileExt = 'srt';
		$captionAsset->format = KalturaCaptionType::SRT;

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