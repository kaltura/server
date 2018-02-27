<?php
/**
 * @package plugins.reach
 * @subpackage lib
 */
class kReachFlowManager implements kGenericEventConsumer
{
	const ARRAY_KEY_VENDOR_PROFILE_ID = "vendorProfileId";
	const ARRAY_KEY_CONDITIONS = "conditions";
	const ARRAY_KEY_CATALOG_ITEM_IDS = "catalogItemIds";
	const ARRAY_KEY_EVENT_OBJECT_TYPE = "eventObjectType";
	
	static protected $allVendorProfiles = null;
	
	static protected $objectTypeAndEventTypeConditions = null;
	
	protected $fullFiledItems = array();
	
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::shouldConsumeEvent()
	 */
	public function shouldConsumeEvent(KalturaEvent $event)
	{
		$scope = $event->getScope();
		
		$e = $scope->getEvent();
		if($e instanceof kObjectChangedEvent)
		{
			$m = $scope->getEvent()->getModifiedColumns();
			if($m)
				$m1 = $m['CUSTOM_DATA']['']['credit_usage_percentage'];
			if($scope->getEvent()->isCustomDataModified('credit_usage_percentage'))
				KalturaLog::debug("Kukui");	
		}
		
		$partnerId = $scope->getPartnerId();
		
		if(!ReachPlugin::isAllowedPartner($partnerId))
			return false;
		
		if(!self::$allVendorProfiles)
			self::$allVendorProfiles = VendorProfilePeer::retrieveByPartnerId($partnerId);
		
		if(!count(self::$allVendorProfiles))
				return false;
		
		if(!self::$objectTypeAndEventTypeConditions)
			$this->buildObjectTypeAndEventTypeConditions();
		
		if(!count(self::$objectTypeAndEventTypeConditions))
			return false;
		
		$eventType = kEventNotificationFlowManager::getEventType($event);
		$eventObjectClassName = kEventNotificationFlowManager::getEventObjectType($event);
		
		if(!isset(self::$objectTypeAndEventTypeConditions[$eventType]))
			return false;
		
		foreach (self::$objectTypeAndEventTypeConditions[$eventType] as $rule)
		{
			$ruleEventObjectType = $rule[self::ARRAY_KEY_EVENT_OBJECT_TYPE];
			if(strcmp($eventObjectClassName, $ruleEventObjectType) && !is_subclass_of($eventObjectClassName, $ruleEventObjectType))
				continue;
			
			if($this->conditionsFulfilled($rule[self::ARRAY_KEY_CONDITIONS], $scope))
			{
				if (!isset($this->fullFiledItems[$rule[self::ARRAY_KEY_VENDOR_PROFILE_ID]]))
					$this->fullFiledItems[$rule[self::ARRAY_KEY_VENDOR_PROFILE_ID]] = array();
				
				$this->fullFiledItems[$rule[self::ARRAY_KEY_VENDOR_PROFILE_ID]] =
						array_merge($this->fullFiledItems[$rule[self::ARRAY_KEY_VENDOR_PROFILE_ID]], explode(",", $rule[self::ARRAY_KEY_CATALOG_ITEM_IDS]));
			}
		}
		
		return count($this->fullFiledItems);
	}
	
	private function buildObjectTypeAndEventTypeConditions()
	{
		$indexArray = array();
		self::$objectTypeAndEventTypeConditions = array();
		
		foreach(self::$allVendorProfiles as $vendorProfile)
		{
			$rules = $vendorProfile->getRulesArray();
			foreach ($rules as $rule)
			{
				/* @var $rule kVendorProfileRule */
				$eventKey = $rule->getEventType();
				$eventObjectType = KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $rule->getEventObjectType());
				
				if(!isset($indexArray[$eventKey]))
					$indexArray[$eventKey] = 0;
				
				$currIndex = $indexArray[$eventKey];
				self::$objectTypeAndEventTypeConditions[$eventKey][$currIndex] = array();
				self::$objectTypeAndEventTypeConditions[$eventKey][$currIndex][self::ARRAY_KEY_EVENT_OBJECT_TYPE] = $eventObjectType;
				self::$objectTypeAndEventTypeConditions[$eventKey][$currIndex][self::ARRAY_KEY_VENDOR_PROFILE_ID] = $vendorProfile->getId();
				self::$objectTypeAndEventTypeConditions[$eventKey][$currIndex][self::ARRAY_KEY_CONDITIONS] = $rule->getEventConditions();
				self::$objectTypeAndEventTypeConditions[$eventKey][$currIndex][self::ARRAY_KEY_CATALOG_ITEM_IDS] = $rule->getCatalogItemIds();
				$indexArray[$eventKey]++;
			}
		}
	}
	
	private function conditionsFulfilled($conditions, $scope)
	{
		foreach($conditions as $condition)
		{
			if (!$condition->fulfilled($scope))
				return false;
		}
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::consumeEvent()
	 */
	public function consumeEvent(KalturaEvent $event)
	{
		$entryId = $this->getEntryId($event->getScope()->getObject());
		if(!$entryId)
		{
			KalturaLog::debug("Entry ID cannot be retrieved form event object, reach consumer will no run");
			return true;
		}
		
		foreach($this->fullFiledItems as $vendorProfileId => $catalogItemIds)
		{
			foreach ($catalogItemIds as $catalogItemId) 
			{
				$c = new Criteria();
				$c->add(EntryVendorTaskPeer::ENTRY_ID, $entryId);
				$c->add(EntryVendorTaskPeer::CATALOG_ITEM_ID, $catalogItemId);
				
				if(EntryVendorTaskPeer::doSelectOne($c))
				{
					KalturaLog::debug("Vendor task for entry [$entryId] and catalog item [$catalogItemId] already exists, skipping!!!");
					continue;
				}
				
				$c = new Criteria();
				$c->add(PartnerCatalogItemPeer::PARTNER_ID, kCurrentContext::getCurrentPartnerId());
				$c->add(PartnerCatalogItemPeer::CATALOG_ITEM_ID, $catalogItemId);
				if(!PartnerCatalogItemPeer::doSelectOne($c))
				{
					KalturaLog::debug("Catalog item with id [$catalogItemId] not enabled on current partner, skipping!!!");
					continue;
				}
				
				kReachManager::addEntryVendorTaskByObjectIds($entryId, $catalogItemId, $vendorProfileId);
			}
		}
		
		$this->fullFiledItems = array();
		return true;
	}
	
	private function getEntryId($object)
	{
		if(is_callable(array($object, "getEntryId")))
			return $object->getEntryId();
		
		return null;
	}
}
