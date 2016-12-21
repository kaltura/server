<?php
/**
 * batch service lets you handle different batch process from remote machines.
 * As opposed to other objects in the system, locking mechanism is critical in this case.
 * For this reason the GetExclusiveXX, UpdateExclusiveXX and FreeExclusiveXX actions are important for the system's intergity.
 * In general - updating batch object should be done only using the UpdateExclusiveXX which in turn can be called only after
 * acuiring a batch objet properly (using  GetExclusiveXX).
 * If an object was aquired and should be returned to the pool in it's initial state - use the FreeExclusiveXX action
 *
 *	Terminology:
 *		LocationId
 *		ServerID
 *		ParternGroups
 *
 * @service batch
 * @package api
 * @subpackage services
 */
class KalturaBatchService extends KalturaBaseService
{
	/* (non-PHPdoc)
	 * @see KalturaBaseService::initService()
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if($this->getPartnerId() != Partner::BATCH_PARTNER_ID)
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);

		myPartnerUtils::resetAllFilters();
	}

	protected function getExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, 
			KalturaBatchJobFilter $filter = null, $jobType, $maxJobToPullForCache = 0)
	{
		$dbJobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);

		if (!is_null($filter))
			$jobsFilter = $filter->toFilter($dbJobType);
		
		return kBatchExclusiveLock::getExclusiveJobs($lockKey->toObject(), $maxExecutionTime, $numberOfJobs, $dbJobType, $jobsFilter, $maxJobToPullForCache);
	}
}
