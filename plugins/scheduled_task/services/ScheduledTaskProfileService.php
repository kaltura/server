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
		$scheduledTaskProfile->fromObject($dbScheduledTaskProfile);
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
		$scheduledTaskProfile->fromObject($dbScheduledTaskProfile);
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
		$scheduledTaskProfile->fromObject($dbScheduledTaskProfile);
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
		$response->objects = KalturaScheduledTaskProfileArray::fromDbArray($list);
		$response->totalCount = $count;

		return $response;
	}

	/**
	 * Execute object filter for a given scheduled task profile id
	 *
	 * @action query
	 * @param int $id
	 * @param KalturaFilterPager $pager
	 * @return KalturaObjectListResponse
	 *
	 * @throws KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_FOUND
	 */
	public function queryAction($id, KalturaFilterPager $pager = null)
	{
		// get the object
		$dbScheduledTaskProfile = ScheduledTaskProfilePeer::retrieveByPK($id);
		if (!$dbScheduledTaskProfile)
			throw new KalturaAPIException(KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_FOUND, $id);

		if ($dbScheduledTaskProfile->getStatus() != KalturaScheduledTaskProfileStatus::ACTIVE)
			throw new KalturaAPIException(KalturaScheduledTaskErrors::SCHEDULED_TASK_PROFILE_NOT_ACTIVE, $id);

		if (is_null($pager))
			$pager = new KalturaFilterPager();

		$scheduledTaskProfile = new KalturaScheduledTaskProfile();
		$scheduledTaskProfile->fromObject($dbScheduledTaskProfile);

		$objectFilterEngineType = $scheduledTaskProfile->objectFilterEngineType;
		$objectFilterEngine = KObjectFilterEngineFactory::getInstanceByType($objectFilterEngineType);
		$objectFilterEngine->setPageSize($pager->pageSize);
		$objectFilterEngine->setPageIndex($pager->pageIndex);
		$response = $objectFilterEngine->query($scheduledTaskProfile->objectFilter);

		$genericResponse = new KalturaObjectListResponse();
		$genericResponse->totalCount = $response->totalCount;
		foreach($response->objects as $object)
		{
			$genericResponse->objects[] = $object;
		}

		return $genericResponse;
	}
}