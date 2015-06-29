<?php
class kMultiCaptionFlowManager implements kBatchJobStatusEventConsumer
{
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED)
		{
			$parseMultiBatchJobType = CaptionPlugin::getBatchJobTypeCoreValue(ParseMultiLanguageCaptionAssetBatchType::PARSE_MULTI_LANGUAGE_CAPTION_ASSET);
			if ($dbBatchJob->getJobType() == $parseMultiBatchJobType && $dbBatchJob->getErrNumber() == KalturaBatchJobAppErrors::MISSING_ASSETS)
				return true;
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{	
		$dbBatchJob = $this->updatedParseMulti($dbBatchJob, $dbBatchJob->getData());
		return true;					
	}

	private function updatedParseMulti($dbBatchJob,$data)
	{
		$captionId = $data->getParentCaptionAssetId();
		$captionAsset = assetPeer::retrieveById($captionId);
		$captionAsset->setStatus(asset::ASSET_STATUS_ERROR);
		$captionAsset->save();
	}
}
