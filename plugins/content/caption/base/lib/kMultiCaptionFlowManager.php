<?php
class kMultiCaptionFlowManager implements kBatchJobStatusEventConsumer
{
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		$parseMultiBatchJobType = CaptionPlugin::getCoreValue('BatchJobType', ParseMultiLanguageCaptionAssetBatchType::PARSE_MULTI_LANGUAGE_CAPTION_ASSET);
		$message = $dbBatchJob->getMessage();
			
		if ($dbBatchJob->getJobType() == $parseMultiBatchJobType && $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			if(strpos($message,CaptionPlugin::NO_CAPTIONS_MESSAGE) !== false)
				return true;
		}		
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{	
		try 
		{
			$dbBatchJob = $this->updatedParseMulti($dbBatchJob, $dbBatchJob->getData());
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process updatedJob - '.$e->getMessage());
		}
		return true;					
	}

	private function updatedParseMulti($dbBatchJob,$data)
	{
		$captionId = $data->getMultiLanaguageCaptionAssetId();
		$captionAsset = assetPeer::retrieveById($captionId);
		$captionAsset->setStatus(asset::ASSET_STATUS_ERROR);
		$captionAsset->save();
	}
}
