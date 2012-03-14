<?php


/**
 * Skeleton subclass for representing a row from the 'event_notification_template' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.eventNotification
 * @subpackage model
 */
abstract class EventNotificationTemplate extends BaseEventNotificationTemplate 
{
	const CUSTOM_DATA_EVENT_CONDITIONS = 'eventConditions';

	public function getEventConditions()									{return $this->getFromCustomData(self::CUSTOM_DATA_EVENT_CONDITIONS);}
	
	public function setEventConditions(array $v)							{return $this->putInCustomData(self::CUSTOM_DATA_EVENT_CONDITIONS, $v);}
	
} // EventNotificationTemplate
