<?php
/**
 * Extension plugin for scheduled task plugin to add support to dispatch event notification object task
 *
 * @package plugins.scheduledTaskEventNotification
 */
class ScheduledTaskEventNotificationPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'scheduledTaskEventNotification';
	
	const SCHEDULED_TASK_PLUGIN_NAME = 'scheduledTask';
	
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
		$eventNotificationVersion = new KalturaVersion(self::EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR, self::EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR, self::EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD);
		
		$scheduledTaskDependency = new KalturaDependency(self::SCHEDULED_TASK_PLUGIN_NAME);
		$eventNotificationDependency = new KalturaDependency(self::EVENT_NOTIFICATION_PLUGIN_NAME, $eventNotificationVersion);
		
		return array($scheduledTaskDependency, $eventNotificationDependency);
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('DispatchEventNotificationObjectTaskType');
	
		if($baseEnumName == 'ObjectTaskType')
			return array('DispatchEventNotificationObjectTaskType');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		$apiValue = self::getApiValue(DispatchEventNotificationObjectTaskType::DISPATCH_EVENT_NOTIFICATION);
		$dispatchEventNotificationObjectTaskCoreValue = kPluginableEnumsManager::apiToCore('ObjectTaskType', $apiValue);
		if($baseClass == 'KalturaObjectTask' && $enumValue == $dispatchEventNotificationObjectTaskCoreValue)
		{
			return new KalturaDispatchEventNotificationObjectTask();
		}

		return null;
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		return null;
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
