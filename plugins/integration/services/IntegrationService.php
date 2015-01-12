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
		$job = kIntegrationFlowManager::addintegrationJob($coreObjectType, $objectId, $data);
		if(!$job)
			throw new KalturaAPIException(KalturaIntegrationErrors::INTEGRATION_DISPATCH_FAILED, $objectType);
			
		return $job->getId();
	}
}
