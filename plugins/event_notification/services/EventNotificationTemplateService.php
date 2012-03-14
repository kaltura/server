<?php

/**
 * Event notification template service lets you create and manage event notification templates
 * @service eventNotificationTemplate
 * @package plugins.eventNotification
 * @subpackage api.services
 */
class EventNotificationTemplateService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if (!EventNotificationPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, "{$this->serviceName}->{$this->actionName}");
			
		myPartnerUtils::addPartnerToCriteria(new EventNotificationTemplatePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
	}
		
	/**
	 * Allows you to add a new event notification template object
	 * 
	 * @action add
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate
	 * @return KalturaEventNotificationTemplate
	 */
	public function addAction(KalturaEventNotificationTemplate $eventNotificationTemplate)
	{
		$dbEventNotificationTemplate = $eventNotificationTemplate->toInsertableObject();
		$dbEventNotificationTemplate->setPartnerId($this->getPartnerId());
		$dbEventNotificationTemplate->save();
		
		// return the saved object
		$eventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($dbEventNotificationTemplate->getType());
		$eventNotificationTemplate->fromObject($dbEventNotificationTemplate);
		return $eventNotificationTemplate;
		
	}
		
	/**
	 * Allows you to clone exiting event notification template object and create a new one with similar configuration
	 * 
	 * @action clone
	 * @param int $id source template to clone
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate overwrite configuration object
	 * @return KalturaEventNotificationTemplate
	 */
	public function cloneAction($id, KalturaEventNotificationTemplate $eventNotificationTemplate)
	{
		// get the source object
		$dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveByPK($id);
		if (!$dbEventNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $id);
			
		// copy into new db object
		$newDbEventNotificationTemplate = $dbEventNotificationTemplate->copy();
		
		// init new Kaltura object
		$newEventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($newDbEventNotificationTemplate->getType());
		$newEventNotificationTemplate->fromObject($newDbEventNotificationTemplate);
		
		// update new db object with the overwrite configuration
		$newDbEventNotificationTemplate = $newEventNotificationTemplate->toInsertableObject($newDbEventNotificationTemplate);
		
		// save the new db object
		$newDbEventNotificationTemplate->setPartnerId($this->getPartnerId());
		$newDbEventNotificationTemplate->save();
		
		// return the saved object
		$newEventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($newDbEventNotificationTemplate->getType());
		$newEventNotificationTemplate->fromObject($newDbEventNotificationTemplate);
		return $newEventNotificationTemplate;
		
	}
	
	/**
	 * Retrieve an event notification template object by id
	 * 
	 * @action get
	 * @param int $id 
	 * @return KalturaEventNotificationTemplate
	 * 
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND
	 */		
	public function getAction($id)
	{
		// get the object
		$dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveByPK($id);
		if (!$dbEventNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $id);
			
		// return the found object
		$eventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($dbEventNotificationTemplate->getType());
		$eventNotificationTemplate->fromObject($dbEventNotificationTemplate);
		return $eventNotificationTemplate;
	}
	

	/**
	 * Update an existing event notification template object
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate
	 * @return KalturaEventNotificationTemplate
	 *
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND
	 */	
	public function updateAction($id, KalturaEventNotificationTemplate $eventNotificationTemplate)
	{
		// get the object
		$dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveByPK($id);
		if (!$dbEventNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $id);
		
		// save the object
		$dbEventNotificationTemplate = $eventNotificationTemplate->toUpdatableObject($dbEventNotificationTemplate);
		$dbEventNotificationTemplate->save();
	
		// return the saved object
		$eventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($dbEventNotificationTemplate->getType());
		$eventNotificationTemplate->fromObject($dbEventNotificationTemplate);
		return $eventNotificationTemplate;
	}

	/**
	 * Delete an event notification template object
	 * 
	 * @action delete
	 * @param int $id 
	 * @return KalturaEventNotificationTemplate
	 *
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND
	 */		
	public function deleteAction($id)
	{
		// get the object
		$dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveByPK($id);
		if (!$dbEventNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $id);

		// set the object status to deleted
		$dbEventNotificationTemplate->setStatus(EventNotificationTemplateStatus::DELETED);
		$dbEventNotificationTemplate->save();
			
		// return the saved object
		$eventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($dbEventNotificationTemplate->getType());
		$eventNotificationTemplate->fromObject($dbEventNotificationTemplate);
		return $eventNotificationTemplate;
	}
	
//	/**
//	 * list event notification template objects
//	 * 
//	 * @action list
//	 * @param KalturaEventNotificationTemplateFilter $filter
//	 * @param KalturaFilterPager $pager
//	 * @return KalturaEventNotificationTemplateListResponse
//	 */
//	public function listAction(KalturaEventNotificationTemplateFilter  $filter = null, KalturaFilterPager $pager = null)
//	{
//		if (!$filter)
//			$filter = new KalturaEventNotificationTemplateFilter();
//			
//		if (! $pager)
//			$pager = new KalturaFilterPager ();
//			
//		$eventNotificationTemplateFilter = $filter->toObject();
//
//		$c = new Criteria();
//		$eventNotificationTemplateFilter->attachToCriteria($c);
//		$count = EventNotificationTemplatePeer::doCount($c);
//		
//		$pager->attachToCriteria ( $c );
//		$list = EventNotificationTemplatePeer::doSelect($c);
//		
//		$response = new KalturaEventNotificationTemplateListResponse();
//		$response->objects = KalturaEventNotificationTemplateArray::fromDbArray($list);
//		$response->totalCount = $count;
//		
//		return $response;
//	}
}
