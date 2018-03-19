<?php

/**
 * Schedule task service lets you create and manage scheduled task profiles
 *
 * @service scheduledTaskProfile
 * @package plugins.scheduledTask
 * @subpackage api.services
 */
class ScheduledTaskProfileService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		$partnerId = $this->getPartnerId();
		if (!ScheduledTaskPlugin::isAllowedPartner($partnerId))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, "{$this->serviceName}->{$this->actionName}");

		$this->applyPartnerFilterForClass('ScheduledTaskProfile');
	}

	/**
	 * Add a new scheduled task profile
	 *
	 * @action add
	 * @param KalturaScheduledTaskProfile $scheduledTaskProfile
	 * @return KalturaScheduledTaskProfile
	 *
	 * @disableRelativeTime $scheduledTaskProfile
	 */
	public function addAction(KalturaScheduledTaskProfile $scheduledTaskProfile)
	{
		/* @var $dbScheduledTaskProfile ScheduledTaskProfile */
		$dbScheduledTaskProfile = $scheduledTaskProfile->toInsertableObject();
		$dbScheduledTaskProfile->setPartnerId(kCurrentContext::getCurrentPartnerId());
		$dbScheduledTaskProfile->save();

		// return the saved object
		$scheduledTaskProfile = new KalturaScheduledTaskProfile();
		$scheduledTaskProfile->fromObject($dbScheduledTaskProfile, $this->getResponseProfile());
		return $scheduledTaskProfile;
	}

	/**
	 * Retrieve a scheduled task profile by id
	 *
	 * @action get
	 * @param int $id
	 * @return KalturaScheduledTaskProfile
	 *
	 * @throws KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_FOUND
	 */
	public function getAction($id)
	{
		// get the object
		$dbScheduledTaskProfile = ScheduledTaskProfilePeer::retrieveByPK($id);
		if (!$dbScheduledTaskProfile)
			throw new KalturaAPIException(KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_FOUND, $id);

		// return the found object
		$scheduledTaskProfile = new KalturaScheduledTaskProfile();
		$scheduledTaskProfile->fromObject($dbScheduledTaskProfile, $this->getResponseProfile());
		return $scheduledTaskProfile;
	}

	/**
	 * Update an existing scheduled task profile
	 *
	 * @action update
	 * @param int $id
	 * @param KalturaScheduledTaskProfile $scheduledTaskProfile
	 * @return KalturaScheduledTaskProfile
	 *
	 * @throws KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_FOUND
	 * @disableRelativeTime $scheduledTaskProfile
	 */
	public function updateAction($id, KalturaScheduledTaskProfile $scheduledTaskProfile)
	{
		// get the object
		$dbScheduledTaskProfile = ScheduledTaskProfilePeer::retrieveByPK($id);
		if (!$dbScheduledTaskProfile)
			throw new KalturaAPIException(KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_FOUND, $id);

		// save the object
		/** @var ScheduledTaskProfile $dbScheduledTaskProfile */
		$dbScheduledTaskProfile = $scheduledTaskProfile->toUpdatableObject($dbScheduledTaskProfile);
		$dbScheduledTaskProfile->save();

		// return the saved object
		$scheduledTaskProfile = new KalturaScheduledTaskProfile();
		$scheduledTaskProfile->fromObject($dbScheduledTaskProfile, $this->getResponseProfile());
		return $scheduledTaskProfile;
	}

	/**
	 * Delete a scheduled task profile
	 *
	 * @action delete
	 * @param int $id
	 *
	 * @throws KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_FOUND
	 */
	public function deleteAction($id)
	{
		// get the object
		$dbScheduledTaskProfile = ScheduledTaskProfilePeer::retrieveByPK($id);
		if (!$dbScheduledTaskProfile)
			throw new KalturaAPIException(KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_FOUND, $id);

		// set the object status to deleted
		$dbScheduledTaskProfile->setStatus(ScheduledTaskProfileStatus::DELETED);
		$dbScheduledTaskProfile->save();
	}

	/**
	 * List scheduled task profiles
	 *
	 * @action list
	 * @param KalturaScheduledTaskProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaScheduledTaskProfileListResponse
	 */
	public function listAction(KalturaScheduledTaskProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaScheduledTaskProfileFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();

		$scheduledTaskFilter = new ScheduledTaskProfileFilter();
		$filter->toObject($scheduledTaskFilter);

		$c = new Criteria();
		$scheduledTaskFilter->attachToCriteria($c);
		$count = ScheduledTaskProfilePeer::doCount($c);

		$pager->attachToCriteria($c);
		$list = ScheduledTaskProfilePeer::doSelect($c);

		$response = new KalturaScheduledTaskProfileListResponse();
		$response->objects = KalturaScheduledTaskProfileArray::fromDbArray($list, $this->getResponseProfile());
		$response->totalCount = $count;

		return $response;
	}

	/**
	 * @action requestDryRun
	 * @param int $scheduledTaskProfileId
	 * @param int $maxResults
	 * @return int
	 * @throws KalturaAPIException
	 */
	public function requestDryRunAction($scheduledTaskProfileId, $maxResults = 500)
	{
		// get the object
		$dbScheduledTaskProfile = ScheduledTaskProfilePeer::retrieveByPK($scheduledTaskProfileId);
		if (!$dbScheduledTaskProfile)
			throw new KalturaAPIException(KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_FOUND, $scheduledTaskProfileId);

		if (!in_array($dbScheduledTaskProfile->getStatus(), array(KalturaScheduledTaskProfileStatus::ACTIVE, KalturaScheduledTaskProfileStatus::DRY_RUN_ONLY)))
			throw new KalturaAPIException(KalturaScheduledTaskErrors::SCHEDULED_TASK_DRY_RUN_NOT_ALLOWED, $scheduledTaskProfileId);

		$jobData = new kScheduledTaskJobData();
		$jobData->setMaxResults($maxResults);
		$referenceTime = kCurrentContext::$ks_object->getPrivilegeValue(ks::PRIVILEGE_REFERENCE_TIME);
		if ($referenceTime)
			$jobData->setReferenceTime($referenceTime);
		$batchJob = $this->createScheduledTaskJob($dbScheduledTaskProfile, $jobData);

		return $batchJob->getId();
	}

	/**
	 * @action getDryRunResults
	 * @param int $requestId
	 * @return KalturaObjectListResponse
	 * @throws KalturaAPIException
	 */
	public function getDryRunResultsAction($requestId)
	{
		$batchJob = $this->getScheduledTaskBatchJob($requestId);
		/* @var $jobData kScheduledTaskJobData */
		$jobData = $batchJob->getData();
		$syncKey = $batchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD);
		if($jobData->getFileFormat() == DryRunFileType::CSV)
		{
			throw new KalturaAPIException(KalturaScheduledTaskErrors::DRY_RUN_RESULT_IS_TOO_BIG.$this->getDryRunResultUrl($requestId));
		}

		$data = kFileSyncUtils::file_get_contents($syncKey, true);
		return unserialize($data);
	}
	
	/**
	 * Serves dry run results by its request id
	 * @action serveDryRunResults
	 * @param int $requestId
	 * @return file
	 * @throws KalturaAPIException
	 */
	public function serveDryRunResultsAction($requestId)
	{
		$batchJob = $this->getScheduledTaskBatchJob($requestId);
		return $this->serveFile($batchJob, BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD);
	}

	/**
	 * Get a url to serve dry run result action
	 * @param int $requestId
	 * @return string
	 */
	private function getDryRunResultUrl($requestId)
	{
		$finalPath ='/api_v3/service/scheduledtask_scheduledtaskprofile/action/serveDryRunResults/requestId/';
		$finalPath .="$requestId";
		$ksObj = $this->getKs();
		$ksStr = ($ksObj) ? $ksObj->getOriginalString() : null;
		$finalPath .= "/ks/".$ksStr;
		$partnerId = $this->getPartnerId();
		$downloadUrl = myPartnerUtils::getCdnHost($partnerId) . $finalPath;

		return $downloadUrl;
	}
	
	/**
	 * @action getDryRunResults
	 * @param int $requestId
	 * @return BatchJob
	 * @throws KalturaAPIException
	 */
	private function getScheduledTaskBatchJob($requestId)
	{
		$this->applyPartnerFilterForClass('BatchJob');
		$batchJob = BatchJobPeer::retrieveByPK($requestId);
		$batchJobType = ScheduledTaskPlugin::getBatchJobTypeCoreValue(ScheduledTaskBatchType::SCHEDULED_TASK);
		if (is_null($batchJob) || $batchJob->getJobType() != $batchJobType)
			throw new KalturaAPIException(KalturaScheduledTaskErrors::OBJECT_NOT_FOUND);

		if (in_array($batchJob->getStatus(), array(KalturaBatchJobStatus::FAILED, KalturaBatchJobStatus::FATAL)))
			throw new KalturaAPIException(KalturaScheduledTaskErrors::DRY_RUN_FAILED);

		if ($batchJob->getStatus() != KalturaBatchJobStatus::FINISHED)
			throw new KalturaAPIException(KalturaScheduledTaskErrors::DRY_RUN_NOT_READY);

		return $batchJob;
	}

	/**
	 * @param ScheduledTaskProfile $scheduledTaskProfile
	 * @param kScheduledTaskJobData $jobData
	 * @return BatchJob
	 */
	protected function createScheduledTaskJob(ScheduledTaskProfile $scheduledTaskProfile, kScheduledTaskJobData $jobData)
	{
		$scheduledTaskProfileId = $scheduledTaskProfile->getId();
		$jobType = ScheduledTaskPlugin::getBatchJobTypeCoreValue(ScheduledTaskBatchType::SCHEDULED_TASK);
		$objectType = ScheduledTaskPlugin::getBatchJobObjectTypeCoreValue(ScheduledTaskBatchJobObjectType::SCHEDULED_TASK_PROFILE);

		KalturaLog::log("Creating scheduled task dry run job for profile [".$scheduledTaskProfileId."]");
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($scheduledTaskProfile->getPartnerId());
		$batchJob->setObjectId($scheduledTaskProfileId);
		$batchJob->setObjectType($objectType);
		$batchJob->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
		$batchJob = kJobsManager::addJob($batchJob, $jobData, $jobType);

		return $batchJob;
	}
}