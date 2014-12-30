<?php
/**
 * Unicorn Service
 *
 * @service unicorn
 * @package plugins.unicornDistribution
 * @subpackage api.services
 */
class UnicornService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('BatchJob');
		
		if(!ContentDistributionPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ContentDistributionPlugin::PLUGIN_NAME);
	}
	
	/**
	 * @action notify
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $id distribution job id
	 */
	public function notifyAction($id) {
		$validJobTypes = array(
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE),
		);
		$validJobStatuses = BatchJobPeer::getUnClosedStatusList();
		
		$batchJob = BatchJobPeer::retrieveByPK($id);
		if(		!$batchJob 
			||	!in_array($batchJob->getJobType(), $validJobTypes)
			||	!in_array($batchJob->getStatus(), $validJobStatuses)
			||	$batchJob->getJobSubType() != UnicornDistributionProvider::get()->getType()
		)
			throw new KalturaAPIException(KalturaErrors::INVALID_BATCHJOB_ID, $id);
			
		kJobsManager::updateBatchJob($batchJob, KalturaBatchJobStatus::FINISHED);
	}
}
