<?php

/**
 * ScheduleResource service lets you create and manage schedule events
 * @service scheduleResource
 * @package plugins.schedule
 * @subpackage api.services
 */
class ScheduleResourceService extends KalturaBaseService
{
	/* (non-PHPdoc)
	 * @see KalturaBaseService::initService()
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$this->applyPartnerFilterForClass('ScheduleEvent');
		$this->applyPartnerFilterForClass('ScheduleResource');
		$this->applyPartnerFilterForClass('ScheduleEventResource');
	}
	
	/**
	 * Allows you to add a new KalturaScheduleResource object
	 * 
	 * @action add
	 * @param KalturaScheduleResource $scheduleResource
	 * @return KalturaScheduleResource
	 */
	public function addAction(KalturaScheduleResource $scheduleResource)
	{
		// save in database
		$dbScheduleResource = $scheduleResource->toInsertableObject();
		$dbScheduleResource->save();
		
		// return the saved object
		$scheduleResource = KalturaScheduleResource::getInstance($dbScheduleResource, $this->getResponseProfile());
		$scheduleResource->fromObject($dbScheduleResource, $this->getResponseProfile());
		return $scheduleResource;
	
	}
	
	/**
	 * Retrieve a KalturaScheduleResource object by ID
	 * 
	 * @action get
	 * @param int $scheduleResourceId 
	 * @return KalturaScheduleResource
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	public function getAction($scheduleResourceId)
	{
		$dbScheduleResource = ScheduleResourcePeer::retrieveByPK($scheduleResourceId);
		if(!$dbScheduleResource)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduleResourceId);
		}
		
		$scheduleResource = KalturaScheduleResource::getInstance($dbScheduleResource, $this->getResponseProfile());
		$scheduleResource->fromObject($dbScheduleResource, $this->getResponseProfile());
		
		return $scheduleResource;
	}
	
	/**
	 * Update an existing KalturaScheduleResource object
	 * 
	 * @action update
	 * @param int $scheduleResourceId
	 * @param KalturaScheduleResource $scheduleResource
	 * @return KalturaScheduleResource
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	public function updateAction($scheduleResourceId, KalturaScheduleResource $scheduleResource)
	{
		$dbScheduleResource = ScheduleResourcePeer::retrieveByPK($scheduleResourceId);
		if(!$dbScheduleResource)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduleResourceId);
		}
		
		$dbScheduleResource = $scheduleResource->toUpdatableObject($dbScheduleResource);
		$dbScheduleResource->save();
		
		$scheduleResource = KalturaScheduleResource::getInstance($dbScheduleResource, $this->getResponseProfile());
		$scheduleResource->fromObject($dbScheduleResource, $this->getResponseProfile());
		
		return $scheduleResource;
	}
	
	/**
	 * Mark the KalturaScheduleResource object as deleted
	 * 
	 * @action delete
	 * @param int $scheduleResourceId 
	 * @return KalturaScheduleResource
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	public function deleteAction($scheduleResourceId)
	{
		$dbScheduleResource = ScheduleResourcePeer::retrieveByPK($scheduleResourceId);
		if(!$dbScheduleResource)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $scheduleResourceId);
		}
		
		$dbScheduleResource->setStatus(ScheduleResourceStatus::DELETED);
		$dbScheduleResource->save();
		
		$scheduleResource = KalturaScheduleResource::getInstance($dbScheduleResource, $this->getResponseProfile());
		$scheduleResource->fromObject($dbScheduleResource, $this->getResponseProfile());
		
		return $scheduleResource;
	}
	
	/**
	 * List KalturaScheduleResource objects
	 * 
	 * @action list
	 * @param KalturaScheduleResourceFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaScheduleResourceListResponse
	 */
	public function listAction(KalturaScheduleResourceFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaScheduleResourceFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
}
