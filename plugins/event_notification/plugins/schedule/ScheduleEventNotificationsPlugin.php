<?php
/**
 * Enable event notifications on schedule objects
 * @package plugins.scheduleEventNotifications
 */
class ScheduleEventNotificationsPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'scheduleEventNotifications';
	
	const SCHEDULE_PLUGIN_NAME = 'schedule';
	const SCHEDULE_PLUGIN_VERSION_MAJOR = 1;
	const SCHEDULE_PLUGIN_VERSION_MINOR = 0;
	const SCHEDULE_PLUGIN_VERSION_BUILD = 0;
	
	const EVENT_NOTIFICATION_PLUGIN_NAME = 'eventNotification';
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR = 1;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR = 0;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD = 0;
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$scheduleVersion = new KalturaVersion(self::SCHEDULE_PLUGIN_VERSION_MAJOR, self::SCHEDULE_PLUGIN_VERSION_MINOR, self::SCHEDULE_PLUGIN_VERSION_BUILD);
		$eventNotificationVersion = new KalturaVersion(self::EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR, self::EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR, self::EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD);
		
		$scheduleDependency = new KalturaDependency(self::SCHEDULE_PLUGIN_NAME, $scheduleVersion);
		$eventNotificationDependency = new KalturaDependency(self::EVENT_NOTIFICATION_PLUGIN_NAME, $eventNotificationVersion);
		
		return array($scheduleDependency, $eventNotificationDependency);
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('ScheduleEventNotificationEventObjectType');
	
		if($baseEnumName == 'EventNotificationEventObjectType')
			return array('ScheduleEventNotificationEventObjectType');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		return null;
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'EventNotificationEventObjectType')
		{
			if($enumValue == self::getEventNotificationEventObjectTypeCoreValue(ScheduleEventNotificationEventObjectType::SCHEDULE_EVENT))
			{
				return 'ScheduleEvent';
			}
			if($enumValue == self::getEventNotificationEventObjectTypeCoreValue(ScheduleEventNotificationEventObjectType::SCHEDULE_RESOURCE))
			{
				return 'ScheduleResource';
			}
			if($enumValue == self::getEventNotificationEventObjectTypeCoreValue(ScheduleEventNotificationEventObjectType::SCHEDULE_EVENT_RESOURCE))
			{
				return 'ScheduleEventResource';
			}
		}
					
		return null;
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getEventNotificationEventObjectTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('EventNotificationEventObjectType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
