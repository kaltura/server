<?php
/**
 * @package plugins.eventNotification
 * @subpackage lib
 */
class kEventNotificationObjectCopiedHandler implements kObjectCopiedEventConsumer, kObjectChangedEventConsumer, kObjectCreatedEventConsumer
{
	private static $partnerLevelPermissionTypes = array(
		PermissionType::PLUGIN,
		PermissionType::SPECIAL_FEATURE,
	);
	
	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::shouldConsumeCopiedEvent()
	 */
	public function shouldConsumeCopiedEvent(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof Partner)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::objectCopied()
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject)
	{
		$this->copyEventNotificationTemplates($fromObject, $toObject);
			
		return true;
	}
	
	/**
	 * @param Partner $fromPartner
	 * @param Partner $toPartner
	 */
	protected function copyEventNotificationTemplates(Partner $fromPartner, Partner $toPartner, $permissionRequiredOnly = false)
	{
		$fromPartnerId = $fromPartner->getId();
		$toPartnerId = $toPartner->getId();
		
		KalturaLog::info("Copy event-notification templates from [$fromPartnerId] to [$toPartnerId]");
		
 		$c = new Criteria();
 		$c->add(EventNotificationTemplatePeer::PARTNER_ID, $fromPartnerId);
 		
		$systemNameCriteria = new Criteria();
		$systemNameCriteria->add(EventNotificationTemplatePeer::PARTNER_ID, $toPartnerId);
		$systemNameCriteria->add(EventNotificationTemplatePeer::STATUS, EventNotificationTemplateStatus::ACTIVE);
		
 		$eventNotificationTemplates = EventNotificationTemplatePeer::doSelect($c);

		$copiedEventNotificationTemplate = false;
 		foreach($eventNotificationTemplates as $eventNotificationTemplate)
 		{
 			/* @var $eventNotificationTemplate EventNotificationTemplate */
 			
 			if ($permissionRequiredOnly && !count($eventNotificationTemplate->getRequiredCopyTemplatePermissions()))
 				continue;
 			
 			if (!myPartnerUtils::isPartnerPermittedForCopy ($toPartner, $eventNotificationTemplate->getRequiredCopyTemplatePermissions()))
 				continue;
 				
 			if($eventNotificationTemplate->getSystemName())
 			{
				$c = clone $systemNameCriteria;
				$c->add(EventNotificationTemplatePeer::SYSTEM_NAME, $eventNotificationTemplate->getSystemName());
				if(EventNotificationTemplatePeer::doCount($c))
					continue;
 			}
				
 			$newEventNotificationTemplate = $eventNotificationTemplate->copy();
 			$newEventNotificationTemplate->setPartnerId($toPartnerId);
 			$newEventNotificationTemplate->save();
 			$copiedEventNotificationTemplate = true;
 		}

		if($copiedEventNotificationTemplate)
		{
			KalturaLog::info("Copied event-notification templates to partner [$toPartnerId]. Calling reset");
			kEventNotificationFlowManager::resetNotificationTemplates();
		}
	}
	
	protected function partnerPermissionEnabled(Partner $partner)
	{
		$templatePartner = PartnerPeer::retrieveByPK($partner->getI18nTemplatePartnerId() ? $partner->getI18nTemplatePartnerId() : kConf::get('template_partner_id'));
		if($templatePartner)
			$this->copyEventNotificationTemplates($templatePartner, $partner, true);
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object)
	{
		/* @var $object Permission */
		$partner = PartnerPeer::retrieveByPK($object->getPartnerId());
		$this->partnerPermissionEnabled($partner);
			
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		/* @var $object Permission */
		$partner = PartnerPeer::retrieveByPK($object->getPartnerId());
		$this->partnerPermissionEnabled($partner);
			
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if($object instanceof Permission && $object->getPartnerId() && in_array($object->getType(), self::$partnerLevelPermissionTypes) && $object->getStatus() == PermissionStatus::ACTIVE)
		{
			return true;
		}
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof Permission && $object->getPartnerId() && in_array($object->getType(), self::$partnerLevelPermissionTypes) && in_array(PermissionPeer::STATUS, $modifiedColumns) && $object->getStatus() == PermissionStatus::ACTIVE)
		{
			return true;
		}
		
		return false;
	}

}