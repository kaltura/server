<?php
/**
 * @package plugins.eventNotification
 * @subpackage lib
 */
class kEventNotificationFlowManager implements kGenericEventConsumer
{
	static protected $allNotificationTemplates = null;
	
	/**
	 * @var array<EventNotificationTemplate>
	 */
	protected $notificationTemplates;
	
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::consumeEvent()
	 */
	public function consumeEvent(KalturaEvent $event) 
	{
		foreach($this->notificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplate EventNotificationTemplate */
			$scope = $event->getScope();
			$notificationTemplate->dispatch($scope);
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

	/**
	 * @param int $eventType
	 * @param string $eventObjectClassName core class name
	 * @param int $partnerId
	 * @return array<EventNotificationTemplate>
	 */
	public static function getNotificationTemplates($eventType, $eventObjectClassName, $partnerId)
	{
		if(is_null(self::$allNotificationTemplates))
		{
			self::$allNotificationTemplates = EventNotificationTemplatePeer::retrieveByPartnerId($partnerId);
			KalturaLog::info("Found [" . count(self::$allNotificationTemplates) . "] templates");
		}
		
		$notificationTemplates = array();
		foreach(self::$allNotificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplate EventNotificationTemplate */
			if(!$notificationTemplate->getAutomaticDispatchEnabled())
				continue;				
		
			if($notificationTemplate->getEventType() != $eventType)
				continue;				
			
			$templateObjectClassName = KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $notificationTemplate->getObjectType());
			if(strcmp($eventObjectClassName, $templateObjectClassName) && !is_subclass_of($eventObjectClassName, $templateObjectClassName))
				continue;				
			
			$notificationTemplates[] = $notificationTemplate;
		}
		return $notificationTemplates;
	}
		
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::shouldConsumeEvent()
	 */
	public function shouldConsumeEvent(KalturaEvent $event) 
	{
		$this->notificationTemplates = array();
		
		$scope = $event->getScope();
		
		$partnerId = $scope->getPartnerId();
		$ksPartnerId = kCurrentContext::$ks_partner_id;

		if ( (($ksPartnerId && $ksPartnerId == Partner::MEDIA_SERVER_PARTNER_ID) || $partnerId <= 0 || !EventNotificationPlugin::isAllowedPartner($partnerId))
				&& !in_array($partnerId, kConf::get('media_server_allowed_notifications','local', array())) )
			return false;
			
		$eventType = self::getEventType($event);
		$eventObjectClassName = self::getEventObjectType($event);
		
		$notificationTemplates = self::getNotificationTemplates($eventType, $eventObjectClassName, $scope->getPartnerId());
		if(!count($notificationTemplates))
			return false;
			
		foreach($notificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplate EventNotificationTemplate */
			$scope->resetDynamicValues();
			$notificationTemplate->applyDynamicValues($scope);
			if ($notificationTemplate->fulfilled($scope))
				$this->notificationTemplates[] = $notificationTemplate;
		}
		
		return count($this->notificationTemplates);
	}
}
