<?php
/**
 * @package plugins.eventNotification
 * @subpackage lib
 */
class kEventNotificationFlowManager implements kGenericEventConsumer
{
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::consumeEvent()
	 */
	public function consumeEvent(KalturaEvent $event) 
	{
		// TODO implement $event->getType(), $event->getObjectType() in all event objects? or make them strings?
		
		$notificationTemplates = EventNotificationTemplatePeer::retrieveByEventType($event->getType(), $event->getObjectType());
		foreach($notificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplates EventNotificationTemplate */
			
			$eventConditions = $notificationTemplates->getEventConditions();
			if(!$eventConditions)
				return true;
				
			$fulfilled = true;
			foreach($eventConditions as $eventCondition)
			{
				// TODO - how to implement kEventCondition?
				
				/* @var $eventCondition kEventCondition */
				if(!$eventCondition->fulfilled($event))
				{
					$fulfilled = false;
					break;
				}
			}
			
			if($fulfilled)
				return true;
		}
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::shouldConsumeEvent()
	 */
	public function shouldConsumeEvent(KalturaEvent $event) 
	{
		// TODO Auto-generated method stub
	}
}
