<?php
/**
 * Entry Vendor Task Service
 *
 * @service entryVendorTask
 * @package plugins.reach
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */

class entryVendorTaskService extends KalturaBaseService
{
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if(!ReachPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);
		
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
		if(!$dbEntryVendorTask)
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
		
		if(!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
}