<?php
/**
 * Enable event notifications on content distribution objects
 * @package plugins.integrationEventNotifications
 */
class IntegrationEventNotificationsPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator
{
	const PLUGIN_NAME = 'integrationEventNotifications';
	
	const INTEGRATION_PLUGIN_NAME = 'integration';
	
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
		
		$integrationDependency = new KalturaDependency(self::INTEGRATION_PLUGIN_NAME);
		$eventNotificationDependency = new KalturaDependency(self::EVENT_NOTIFICATION_PLUGIN_NAME, $eventNotificationVersion);
		
		return array($integrationDependency, $eventNotificationDependency);
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('IntegrationEventNotificationEventType');
	
		if($baseEnumName == 'EventNotificationEventType')
			return array('IntegrationEventNotificationEventType');
			
		return array();
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
