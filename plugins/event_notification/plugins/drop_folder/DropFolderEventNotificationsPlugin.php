<?php
/**
 * Enable event notifications on drop folder and drop folder file objects
 * @package plugins.dropFolderEventNotifications
 */
class DropFolderEventNotificationsPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'dropFolderEventNotifications';
	
	const DROP_FOLDER_PLUGIN_NAME = 'dropFolder';
	
	const EVENT_NOTIFICATION_PLUGIN_NAME = 'eventNotification';
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR = 1;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR = 0;
	const EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD = 0;
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null) {
		return null;		
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue) {
		if($baseClass == 'EventNotificationEventObjectType')
		{
			if($baseClass == 'EventNotificationEventObjectType' && $enumValue == self::getEventNotificationEventObjectTypeCoreValue(DropFolderEventNotificationEventObjectType::DROP_FOLDER))
				return DropFolderPeer::OM_CLASS;
				
			if($baseClass == 'EventNotificationEventObjectType' && $enumValue == self::getEventNotificationEventObjectTypeCoreValue(DropFolderEventNotificationEventObjectType::DROP_FOLDER_FILE))
				return DropFolderFilePeer::OM_CLASS;
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

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null) {
		if(is_null($baseEnumName))
			return array('DropFolderEventNotificationEventObjectType');
	
		if($baseEnumName == 'EventNotificationEventObjectType')
			return array('DropFolderEventNotificationEventObjectType');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn() {
		$eventNotificationVersion = new KalturaVersion(self::EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR, self::EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR, self::EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD);
		
		$dropFolderDependency = new KalturaDependency(self::DROP_FOLDER_PLUGIN_NAME);
		$eventNotificationDependency = new KalturaDependency(self::EVENT_NOTIFICATION_PLUGIN_NAME, $eventNotificationVersion);
		
		return array($dropFolderDependency, $eventNotificationDependency);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName() {
		return self::getPluginName();
	}

	
}