<?php
/**
 * @package plugins.reach
 * @subpackage lib
 */
class kReachFlowManager implements kGenericEventConsumer
{
	
	static protected $allVendorProfiles = null;
	
	protected $catalogItemIds = array();
	
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::consumeEvent()
	 */
	public function consumeEvent(KalturaEvent $event)
	{
		foreach($this->catalogItemIds as $catalogItemId)
		{
			// TODO check if job does not exist and if so create it
		}
		return true;
	}
	
	/**
	 * Return single integer value that represents the event type
	 * @param KalturaEvent $event
	 * @return int
	 */
	protected function getEventType(KalturaEvent $event)
	{
		$matches = null;
		if(!preg_match('/k(\w+)Event/', get_class($event), $matches))
			return null;
		
		$typeName = $matches[1];
		$constName = strtoupper(preg_replace('/(?!^)[[:upper:]]/','_\0', $typeName));
		if(defined("EventNotificationEventType::{$constName}"))
		{
			$type = constant("EventNotificationEventType::{$constName}");
			if($type)
				return $type;
		}
		
		return DynamicEnumPeer::retrieveValueByEnumValueName('EventNotificationEventType', $constName);
	}
	
	/**
	 * @param EventNotificationEventObjectType $objectType
	 * @param string $objectId
	 * @return BaseObject
	 */
	public static function getObject($objectType, $objectId)
	{
		$objectClass = KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $objectType);
		$peerClass = $objectClass . 'Peer';
		$peer = null;
		
		if(class_exists($peerClass))
		{
			$peer = new $peerClass();
		}
		else
		{
			$objectInstance = new $objectClass();
			$peer = $objectInstance->getPeer();
		}
		
		return $peer->retrieveByPK($objectId);
	}
	
	/**
	 * Return single integer value that represents the event object type
	 * @param KalturaEvent $event
	 * @return string class name
	 */
	protected function getEventObjectType(KalturaEvent $event)
	{
		if($event instanceof kBatchJobStatusEvent)
			return 'BatchJob';
		
		if(!method_exists($event, 'getObject'))
			return null;
		
		$object = $event->getObject();
		return get_class($object);
	}
	
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::shouldConsumeEvent()
	 */
	public function shouldConsumeEvent(KalturaEvent $event)
	{
		$scope = $event->getScope();
		$partnerId = $scope->getPartnerId();
		
		if(!self::$allVendorProfiles)
			self::$allVendorProfiles = VendorProfilePeer::retrieveByPartnerId($partnerId);
			
			
		if(!count(self::$allVendorProfiles))
				return false;
		
		$eventType = self::getEventType($event);
		$eventObjectClassName = self::getEventObjectType($event);
		
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
					$this->catalogItemIds = array_merge($this->catalogItemIds, explode(",", $rule->getCatalogItemIds));
			}
		}
		
		return count($this->catalogItemIds);
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
}
