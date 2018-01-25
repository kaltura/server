<?php
/**
 * Entry Vendor Task Service
 *
 * @service entryVendorTask
 * @package plugins.reach
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */

class EntryVendorTaskService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if (!ReachPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);

		if (!in_array($actionName, array('getJob')))
			$this->applyPartnerFilterForClass('entryVendorTask');
	}

	/**
	 * Allows you to add a entry vendor task
	 *
	 * @action add
	 * @param KalturaEntryVendorTask $entryVendorTask
	 * @return KalturaEntryVendorTask
	 * @throws KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 */
	public function addAction(KalturaEntryVendorTask $entryVendorTask)
	{
		$dbEntryVendorTask = $entryVendorTask->toInsertableObject();
		$dbEntryVendorTask->save();

		// return the saved object
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}

	/**
	 * Retrieve specific entry vendor task by id
	 *
	 * @action get
	 * @param int $id
	 * @return KalturaEntryVendorTask
	 * @throws KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbEntryVendorTask = EntryVendorTaskPeer::retrieveByPK($id);
		if (!$dbEntryVendorTask)
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $id);

		$entryVendorTask = new KalturaEntryVendorTask();
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}

	/**
	 * List KalturaEntryVendorTask objects
	 *
	 * @action list
	 * @param KalturaEntryVendorTaskFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEntryVendorTaskListResponse
	 */
	public function listAction(KalturaEntryVendorTaskFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaEntryVendorTaskFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * get KalturaEntryVendorTask objects for specific vendor partner
	 *
	 * @action getJobs
	 * @param KalturaEntryVendorTaskFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEntryVendorTaskListResponse
	 */
	public function getJobAction(KalturaEntryVendorTaskFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (PermissionPeer::isValidForPartner(PermissionName::REACH_VENDOR_PARTNER_PERMISSION, kCurrentContext::getCurrentPartnerId()))
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_SERVICE_GET_JOB_NOT_ALLOWED, kCurrentContext::$partner_id);

		if (!$filter)
			$filter = new KalturaEntryVendorTaskFilter();

		$filter->vendorPartnerIdEqual = kCurrentContext::getCurrentPartnerId();
		if (!$pager)
			$pager = new KalturaFilterPager();

		return $filter->getListResponse($pager, $this->getResponseProfile(true));
	}

	/***
	 * Update entry vendor task. Only the properties that were set will be updated.
	 *
	 * @action update
	 * @param string $vendortaskId vendor task id to update
	 * @param KalturaEntryVendorTask $entryVendorTask evntry vendor task to update
	 *
	 * @throws KalturaAPIException
	 */
	function updateAction($vendorTaskId, KalturaEntryVendorTask $entryVendorTask)
	{
		$dbVendorTask = entryPeer::retrieveByPK($vendorTaskId);
		if (!$dbVendorTask)
			throw new KalturaAPIException(KalturaErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $vendorTaskId);

		$dbVendorTask = $entryVendorTask->toUpdatableObject($dbVendorTask);
		/* @var $dbVendorTask EntryVendorTask */
		$dbVendorTask->save();

		// return the saved object
		$entryVendorTask = KalturaEntryVendorTask::getInstance($dbVendorTask, $this->getResponseProfile());
		$entryVendorTask->fromObject($dbVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}

	/**
	 * @param $vendorTaskId
	 * @throws KalturaAPIException
	 */
	public function approveAction($vendorTaskId)
	{
		$dbVendorTask = EntryVendorTaskPeer::retrieveByPK($vendorTaskId);
		if (!$dbVendorTask )
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $vendorTaskId);

		$dbVendorTask->setStatus(KalturaEntryVendorTaskStatus::PENDING);
		$dbVendorTask->save();
	}

	/**
	 * @param $vendorTaskId
	 * @throws KalturaAPIException
	 */
	public function rejectAction($vendorTaskId)
	{
		$dbVendorTask = EntryVendorTaskPeer::retrieveByPK($vendorTaskId);
		if (!$dbVendorTask )
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $vendorTaskId);

		$dbVendorTask->setStatus(KalturaEntryVendorTaskStatus::REJECTED);
		$dbVendorTask->save();
	}
}