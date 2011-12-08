<?php
/**
 * @package plugins.limeLight
 * @subpackage lib
 */
class kLimeLightLiveFlowManager implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{

		if ($dbBatchJob->getJobSubType() == LimeLightPlugin::getEntrySourceTypeCoreValue(LimeLightLiveEntrySourceType::LIMELIGHT_LIVE)){
		
			if($dbBatchJob->getJobType() == BatchJobType::PROVISION_PROVIDE)
				return true;
			
			if($dbBatchJob->getJobType() == BatchJobType::PROVISION_DELETE)
				return true;
		}
			
		
		return false;
	}
	
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		
		if ($dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_PENDING)
			return true;

		if ($dbBatchJob->getJobType() == BatchJobType::PROVISION_PROVIDE){
			
			$entry = $dbBatchJob->getEntry(false, false);
			
			$partner = $entry->getPartner();
			
			
			$limeLightLiveParamsJSON = $partner->getLiveStreamProvisionParams();
			$limeLightLiveParams = json_decode($limeLightLiveParamsJSON);
//			print_r($limeLightLiveParams);
//			die();
			
			//$limeLightLiveParams = LimeLightPlugin::getLimeLightLiveParams($partner);
			//if (is_null($limeLightLiveParams)){
			if ((!isset($limeLightLiveParams->Limelight))
				|| (!isset($limeLightLiveParams->Limelight->limelightPrimaryPublishUrl)) 
				|| (!isset($limeLightLiveParams->Limelight->limelightSecondaryPublishUrl))
				|| (!isset($limeLightLiveParams->Limelight->limelightStreamUrl))){
				kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FAILED);
				return true;
			}
			
			$data = $dbBatchJob->getData();
			$data->setPrimaryBroadcastingUrl($limeLightLiveParams->Limelight->limelightPrimaryPublishUrl);
			$data->setSecondaryBroadcastingUrl($limeLightLiveParams->Limelight->limelightSecondaryPublishUrl);
			$data->setRtmp($limeLightLiveParams->Limelight->limelightStreamUrl);
			
//			$data->setPrimaryBroadcastingUrl($limeLightLiveParams->getLimelightPrimaryPublishUrl());
//			$data->setSecondaryBroadcastingUrl($limeLightLiveParams->getLimelightSecondaryPublishUrl());
//			$data->setRtmp($limeLightLiveParams->getLimelightStreamUrl());
			$data->setStreamName($entry->getId().'_%i');
			
			$dbBatchJob->setData($data);
			
		}
		
		kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
				
		return true;
	}
}