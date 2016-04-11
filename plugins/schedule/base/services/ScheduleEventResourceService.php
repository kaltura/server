<?php

/**
 * ScheduleEventResource service lets you create and manage connections between events and resources
 * @service scheduleEventResource
 * @package plugins.schedule
 * @subpackage api.services
 */
class ScheduleEventResourceService extends KalturaBaseService
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
	 * Allows you to add a new KalturaScheduleEventResource object
	 * 
	 * @action add
	 * @param KalturaScheduleEventResource $scheduleEventResource
	 * @return KalturaScheduleEventResource
	 */
	public function addAction(KalturaScheduleEventResource $scheduleEventResource)
	{
		// save in database
		$dbScheduleEventResource = $scheduleEventResource->toInsertableObject();
		$dbScheduleEventResource->save();
		
		// return the saved object
		$scheduleEventResource = new KalturaScheduleEventResource();
		$scheduleEventResource->fromObject($dbScheduleEventResource, $this->getResponseProfile());
		return $scheduleEventResource;
	
	}
	
	/**
	 * Retrieve a KalturaScheduleEventResource object by ID
	 * 
	 * @action get
	 * @param int $scheduleEventId
	 * @param int $scheduleResourceId 
	 * @return KalturaScheduleEventResource
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	public function getAction($scheduleEventId, $scheduleResourceId)
	{
		$dbScheduleEventResource = ScheduleEventResourcePeer::retrieveByEventAndResource($scheduleEventId, $scheduleResourceId);
		if(!$dbScheduleEventResource)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, "$scheduleEventId,$scheduleResourceId");
		}
		
		$scheduleEventResource = new KalturaScheduleEventResource();
		$scheduleEventResource->fromObject($dbScheduleEventResource, $this->getResponseProfile());
		
		return $scheduleEventResource;
	}
	
	/**
	 * Update an existing KalturaScheduleEventResource object
	 * 
	 * @action update
	 * @param int $scheduleEventId
	 * @param int $scheduleResourceId 
	 * @param KalturaScheduleEventResource $scheduleEventResource
	 * @return KalturaScheduleEventResource
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	public function updateAction($scheduleEventId, $scheduleResourceId, KalturaScheduleEventResource $scheduleEventResource)
	{
		$dbScheduleEventResource = ScheduleEventResourcePeer::retrieveByEventAndResource($scheduleEventId, $scheduleResourceId);
		if(!$dbScheduleEventResource)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, "$scheduleEventId,$scheduleResourceId");
		}
		
		$dbScheduleEventResource = $scheduleEventResource->toUpdatableObject($dbScheduleEventResource);
		$dbScheduleEventResource->save();
		
		$scheduleEventResource = new KalturaScheduleEventResource();
		$scheduleEventResource->fromObject($dbScheduleEventResource, $this->getResponseProfile());
		
		return $scheduleEventResource;
	}
	
	/**
	 * Mark the KalturaScheduleEventResource object as deleted
	 * 
	 * @action delete
	 * @param int $scheduleEventId
	 * @param int $scheduleResourceId 
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	public function deleteAction($scheduleEventId, $scheduleResourceId)
	{
		$dbScheduleEventResource = ScheduleEventResourcePeer::retrieveByEventAndResource($scheduleEventId, $scheduleResourceId);
		if(!$dbScheduleEventResource)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, "$scheduleEventId,$scheduleResourceId");
		}
		
		$dbScheduleEventResource->delete();
	}
	
	/**
	 * List KalturaScheduleEventResource objects
	 * 
	 * @action list
	 * @param KalturaScheduleEventResourceFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaScheduleEventResourceListResponse
	 */
	public function listAction(KalturaScheduleEventResourceFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaScheduleEventResourceFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
}
