<?php
/**
 * @package plugins.reach
 * @subpackage lib
 */
class kReachFlowManager implements kGenericEventConsumer
{
	static protected $allVendorProfiles = null;
	
	protected $fullFiledItems = array();
	
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::shouldConsumeEvent()
	 */
	public function shouldConsumeEvent(KalturaEvent $event)
	{
		$scope = $event->getScope();
		$partnerId = $scope->getPartnerId();
		
		if(!ReachPlugin::isAllowedPartner($partnerId))
			return false;
		
		if(!self::$allVendorProfiles)
			self::$allVendorProfiles = VendorProfilePeer::retrieveByPartnerId($partnerId);
		
		if(!count(self::$allVendorProfiles))
				return false;
		
		$eventType = kEventNotificationFlowManager::getEventType($event);
		$eventObjectClassName = kEventNotificationFlowManager::getEventObjectType($event);
		
		foreach(self::$allVendorProfiles as $vendorProfile)
		{
			/* @var $vendorProfile VendorProfile */
			$rules = $vendorProfile->getRulesArray();
			foreach ($rules as $rule) 
			{
				
				/* @var $rule kVendorProfileRule */
				$ruleEventObjectType = KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $rule->getEventObjectType());
				if($rule->getEventType() != $eventType || strcmp($eventObjectClassName, $ruleEventObjectType) && !is_subclass_of($eventObjectClassName, $ruleEventObjectType))
					continue;
				
				if($this->conditionsFulfilled($rule->getEventConditions(), $scope)) 
				{
					if (!isset($this->fullFiledItems[$vendorProfile->getId()]))
						$this->fullFiledItems[$vendorProfile->getId()] = array();
					
					$this->fullFiledItems[$vendorProfile->getId()] = array_merge($this->fullFiledItems[$vendorProfile->getId()], explode(",", $rule->getCatalogItemIds()));
				}
			}
		}
		
		return count($this->fullFiledItems);
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
