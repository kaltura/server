<?php
/**
 * Integration service lets you dispatch integration tasks
 * @service integration
 * @package plugins.integration
 * @subpackage api.services
 */
class IntegrationService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$partnerId = $this->getPartnerId();
		if (!EventNotificationPlugin::isAllowedPartner($partnerId))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, EventNotificationPlugin::PLUGIN_NAME);
			
		$this->applyPartnerFilterForClass('EventNotificationTemplate');
	}
		
	/**
	 * Dispatch integration task
	 * 
	 * @action dispatch
	 * @param KalturaIntegrationJobData $data
	 * @param KalturaBatchJobObjectType $objectType
	 * @param string $objectId
	 * @throws KalturaIntegrationErrors::INTEGRATION_DISPATCH_FAILED
	 * @return int
	 */		
	public function dispatchAction(KalturaIntegrationJobData $data, $objectType, $objectId)
	{
		$jobData = $data->toObject();
		$coreObjectType = kPluginableEnumsManager::apiToCore('BatchJobObjectType', $objectType);
		$job = kIntegrationFlowManager::addintegrationJob($coreObjectType, $objectId, $jobData);
		if(!$job)
			throw new KalturaAPIException(KalturaIntegrationErrors::INTEGRATION_DISPATCH_FAILED, $objectType);
			
		return $job->getId();
	}

	/**
	 * @action notify
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $id integration job id
	 */
	public function notifyAction($id) 
	{
		$coreType = IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION);
		$batchJob = BatchJobPeer::retrieveByPK($id);
		$invalid = false;
		if(!$batchJob)
		{
			$invalid = true;
			KalturaLog::err("Job [$id] not found");
		}
		elseif($batchJob->getJobType() != $coreType)
		{
			$invalid = true;
			KalturaLog::err("Job [$id] wrong type [" . $batchJob->getJobType() . "] expected [" . $coreType . "]");
		}
		elseif($batchJob->getStatus() != KalturaBatchJobStatus::ALMOST_DONE)
		{
			$invalid = true;
			KalturaLog::err("Job [$id] wrong status [" . $batchJob->getStatus() . "] expected [" . KalturaBatchJobStatus::ALMOST_DONE . "]");
		}
		if($invalid)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_BATCHJOB_ID, $id);
		}
			
		kJobsManager::updateBatchJob($batchJob, KalturaBatchJobStatus::FINISHED);
	}
}
