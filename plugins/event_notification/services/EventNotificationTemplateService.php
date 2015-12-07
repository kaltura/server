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
		
		$partnerId = $this->getPartnerId();
		if (!EventNotificationPlugin::isAllowedPartner($partnerId))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, EventNotificationPlugin::PLUGIN_NAME);
			
		$this->applyPartnerFilterForClass('EventNotificationTemplate');
	}
		
	/**
	 * This action allows for the creation of new backend event types in the system. This action requires access to the Kaltura server Admin Console. If you're looking to register to existing event types, please use the clone action instead.
	 * 
	 * @action add
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate
	 * @return KalturaEventNotificationTemplate
	 */
	public function addAction(KalturaEventNotificationTemplate $eventNotificationTemplate)
	{
		$dbEventNotificationTemplate = $eventNotificationTemplate->toInsertableObject();
		/* @var $dbEventNotificationTemplate EventNotificationTemplate */
		$dbEventNotificationTemplate->setStatus(EventNotificationTemplateStatus::ACTIVE);
		//Partner 0 cannot be impersonated, the reasong this work is because null equals to 0.
		$dbEventNotificationTemplate->setPartnerId($this->impersonatedPartnerId);
		$dbEventNotificationTemplate->save();
		
		// return the saved object
		$eventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($dbEventNotificationTemplate->getType());
		$eventNotificationTemplate->fromObject($dbEventNotificationTemplate, $this->getResponseProfile());
		return $eventNotificationTemplate;
		
	}
		
	/**
	 * This action allows registering to various backend event. Use this action to create notifications that will react to events such as new video was uploaded or metadata field was updated. To see the list of available event types, call the listTemplates action.
	 * 
	 * @action clone
	 * @param int $id source template to clone
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate overwrite configuration object
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_WRONG_TYPE
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_DUPLICATE_SYSTEM_NAME
	 * @return KalturaEventNotificationTemplate
	 */
	public function cloneAction($id, KalturaEventNotificationTemplate $eventNotificationTemplate = null)
	{
		// get the source object
		$dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveByPK($id);
		if (!$dbEventNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $id);
			
		// copy into new db object
		$newDbEventNotificationTemplate = $dbEventNotificationTemplate->copy();
		
		// init new Kaltura object
		$newEventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($newDbEventNotificationTemplate->getType());
		$templateClass = get_class($newEventNotificationTemplate);
		if($eventNotificationTemplate && get_class($eventNotificationTemplate) != $templateClass && !is_subclass_of($eventNotificationTemplate, $templateClass))
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_WRONG_TYPE, $id, kPluginableEnumsManager::coreToApi('EventNotificationTemplateType', $dbEventNotificationTemplate->getType()));
		
		if ($eventNotificationTemplate)
		{
			// update new db object with the overwrite configuration
			$newDbEventNotificationTemplate = $eventNotificationTemplate->toUpdatableObject($newDbEventNotificationTemplate);
		}
		//Check uniqueness of new object's system name
		$systemNameTemplates = EventNotificationTemplatePeer::retrieveBySystemName($newDbEventNotificationTemplate->getSystemName());
		if (count($systemNameTemplates))
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_DUPLICATE_SYSTEM_NAME, $newDbEventNotificationTemplate->getSystemName());
		
		// save the new db object
		$newDbEventNotificationTemplate->setPartnerId($this->getPartnerId());
		$newDbEventNotificationTemplate->save();
	
		// return the saved object
		$newEventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($newDbEventNotificationTemplate->getType());
		$newEventNotificationTemplate->fromObject($newDbEventNotificationTemplate, $this->getResponseProfile());
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
		$eventNotificationTemplate->fromObject($dbEventNotificationTemplate, $this->getResponseProfile());
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
		$eventNotificationTemplate->fromObject($dbEventNotificationTemplate, $this->getResponseProfile());
		return $eventNotificationTemplate;
	}

	/**
	 * Update event notification template status by id
	 * 
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaEventNotificationTemplateStatus $status
	 * @return KalturaEventNotificationTemplate
	 * 
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND
	 */
	function updateStatusAction($id, $status)
	{
		// get the object
		$dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveByPK($id);
		if (!$dbEventNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $id);

		if($status == EventNotificationTemplateStatus::ACTIVE)
		{
			//Check uniqueness of new object's system name
			$systemNameTemplates = EventNotificationTemplatePeer::retrieveBySystemName($dbEventNotificationTemplate->getSystemName());
			if (count($systemNameTemplates))
				throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_DUPLICATE_SYSTEM_NAME, $dbEventNotificationTemplate->getSystemName());
		}	
		
		// save the object
		$dbEventNotificationTemplate->setStatus($status);
		$dbEventNotificationTemplate->save();
	
		// return the saved object
		$eventNotificationTemplate = KalturaEventNotificationTemplate::getInstanceByType($dbEventNotificationTemplate->getType());
		$eventNotificationTemplate->fromObject($dbEventNotificationTemplate, $this->getResponseProfile());
		return $eventNotificationTemplate;
	}

	/**
	 * Delete an event notification template object
	 * 
	 * @action delete
	 * @param int $id 
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
	}
	
	/**
	 * list event notification template objects
	 * 
	 * @action list
	 * @param KalturaEventNotificationTemplateFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEventNotificationTemplateListResponse
	 */
	public function listAction(KalturaEventNotificationTemplateFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaEventNotificationTemplateFilter();
			
		if (!$pager)
			$pager = new KalturaFilterPager ();

		$eventNotificationTemplateFilter = new EventNotificationTemplateFilter();
		$filter->toObject($eventNotificationTemplateFilter);

		$c = new Criteria();
		$eventNotificationTemplateFilter->attachToCriteria($c);
		$count = EventNotificationTemplatePeer::doCount($c);
		
		$pager->attachToCriteria ( $c );
		$list = EventNotificationTemplatePeer::doSelect($c);
		
		$response = new KalturaEventNotificationTemplateListResponse();
		$response->objects = KalturaEventNotificationTemplateArray::fromDbArray($list, $this->getResponseProfile());
		$response->totalCount = $count;
		
		return $response;
	}

	/**
	 * @action listByPartner
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEventNotificationTemplateListResponse
	 */
	public function listByPartnerAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$c = new Criteria();
		
		if (!is_null($filter))
		{
			
			$partnerFilter = new partnerFilter();
			$filter->toObject($partnerFilter);
			$partnerFilter->set('_gt_id', -1);
			
			$partnerCriteria = new Criteria();
			$partnerFilter->attachToCriteria($partnerCriteria);
			$partnerCriteria->setLimit(1000);
			$partnerCriteria->clearSelectColumns();
			$partnerCriteria->addSelectColumn(PartnerPeer::ID);
			$stmt = PartnerPeer::doSelectStmt($partnerCriteria);
			
			if($stmt->rowCount() < 1000) // otherwise, it's probably all partners
			{
				$partnerIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
				$c->add(EventNotificationTemplatePeer::PARTNER_ID, $partnerIds, Criteria::IN);
			}
		}
			
		if (is_null($pager))
			$pager = new KalturaFilterPager();
			
		$c->addDescendingOrderByColumn(EventNotificationTemplatePeer::CREATED_AT);
		
		$totalCount = EventNotificationTemplatePeer::doCount($c);
		$pager->attachToCriteria($c);
		$list = EventNotificationTemplatePeer::doSelect($c);
		$newList = KalturaEventNotificationTemplateArray::fromDbArray($list, $this->getResponseProfile());
		
		$response = new KalturaEventNotificationTemplateListResponse();
		$response->totalCount = $totalCount;
		$response->objects = $newList;
		return $response;
	}
	
	/**
	 * Dispatch event notification object by id
	 * 
	 * @action dispatch
	 * @param int $id 
	 * @param KalturaEventNotificationScope $scope
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_DISPATCH_DISABLED
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_DISPATCH_FAILED
	 * @return int
	 */		
	public function dispatchAction($id, KalturaEventNotificationScope $scope)
	{
		// get the object
		$dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveByPK($id);
		if (!$dbEventNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $id);
			
		if(!$dbEventNotificationTemplate->getManualDispatchEnabled())
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_DISPATCH_DISABLED, $id);

		$jobId = $dbEventNotificationTemplate->dispatch($scope->toObject());
		if(!$jobId)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_DISPATCH_FAILED, $id);
			
		return $jobId;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::partnerGroup()
	 */
	protected function partnerGroup($peer = null)
	{
		
		switch ($this->actionName)
		{
			case 'clone':
				return $this->partnerGroup . ',0';
			case 'listTemplates':
				return '0';
		}
			
		return $this->partnerGroup;
	}
	
	/**
	 * Action lists the template partner event notification templates.
	 * @action listTemplates
	 * 
	 * @param KalturaEventNotificationTemplateFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEventNotificationTemplateListResponse
	 */
	public function listTemplatesAction (KalturaEventNotificationTemplateFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaEventNotificationTemplateFilter();
			
		if (!$pager)
			$pager = new KalturaFilterPager();
		
		$coreFilter = new EventNotificationTemplateFilter();
		$filter->toObject($coreFilter);
		
		$criteria = new Criteria();
		$coreFilter->attachToCriteria($criteria);
		$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
		$count = EventNotificationTemplatePeer::doCount($criteria);
		
		$pager->attachToCriteria($criteria);
		$results = EventNotificationTemplatePeer::doSelect($criteria);
		
		$response = new KalturaEventNotificationTemplateListResponse();
		$response->objects = KalturaEventNotificationTemplateArray::fromDbArray($results, $this->getResponseProfile());
		$response->totalCount = $count;
		
		return $response;
	}
}
