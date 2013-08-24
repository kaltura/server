<?php
/**
 * @package plugins.httpNotification
 */
class HttpNotificationPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaPending, IKalturaObjectLoader, IKalturaEnumerator, IKalturaApplicationPartialView, IKalturaConfigurator
{
	const PLUGIN_NAME = 'httpNotification';
	
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
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if ($partner)
			return $partner->getPluginEnabled(self::PLUGIN_NAME);		
			
		return false;
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('HttpNotificationTemplateType', 'HttpNotificationFileSyncObjectType');
	
		if($baseEnumName == 'EventNotificationTemplateType')
			return array('HttpNotificationTemplateType');
			
		if($baseEnumName == 'FileSyncObjectType')
			return array('HttpNotificationFileSyncObjectType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaApplicationPartialView::getApplicationPartialViews()
	 */
	public static function getApplicationPartialViews($controller, $action)
	{
		if($controller == 'plugin' && $action == 'EventNotificationTemplateConfigureAction')
		{
			return array(
				new Kaltura_View_Helper_HttpNotificationTemplateConfigure(),
			);
		}
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'ISyncableFile' && $enumValue == self::getHttpNotificationFileSyncObjectTypeCoreValue(HttpNotificationFileSyncObjectType::HTTP_NOTIFICATION_TEMPLATE) && isset($constructorArgs['objectId']))
			return EventNotificationTemplatePeer::retrieveTypeByPK(self::getHttpNotificationTemplateTypeCoreValue(HttpNotificationTemplateType::HTTP), $constructorArgs['objectId']);
	
		$class = self::getObjectClass($baseClass, $enumValue);
		if($class)
		{
			if(is_array($constructorArgs))
			{
				$reflect = new ReflectionClass($class);
				return $reflect->newInstanceArgs($constructorArgs);
			}
			
			return new $class();
		}
			
		return null;
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'KalturaEventNotificationDispatchJobData' && $enumValue == self::getHttpNotificationTemplateTypeCoreValue(HttpNotificationTemplateType::HTTP))
			return 'KalturaHttpNotificationDispatchJobData';
	
		if($baseClass == 'EventNotificationTemplate' && $enumValue == self::getHttpNotificationTemplateTypeCoreValue(HttpNotificationTemplateType::HTTP))
			return 'HttpNotificationTemplate';
	
		if($baseClass == 'KalturaEventNotificationTemplate' && $enumValue == self::getHttpNotificationTemplateTypeCoreValue(HttpNotificationTemplateType::HTTP))
			return 'KalturaHttpNotificationTemplate';
	
		if($baseClass == 'Form_EventNotificationTemplateConfiguration' && $enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::HTTP)
			return 'Form_HttpNotificationTemplateConfiguration';
	
		if($baseClass == 'Kaltura_Client_EventNotification_Type_EventNotificationTemplate' && $enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::HTTP)
			return 'Kaltura_Client_HttpNotification_Type_HttpNotificationTemplate';
	
		if($baseClass == 'KDispatchEventNotificationEngine' && $enumValue == KalturaEventNotificationTemplateType::HTTP)
			return 'KDispatchHttpNotificationEngine';
			
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn() 
	{
		$minVersion = new KalturaVersion(
			self::EVENT_NOTIFICATION_PLUGIN_VERSION_MAJOR,
			self::EVENT_NOTIFICATION_PLUGIN_VERSION_MINOR,
			self::EVENT_NOTIFICATION_PLUGIN_VERSION_BUILD
		);
		$dependency = new KalturaDependency(self::EVENT_NOTIFICATION_PLUGIN_NAME, $minVersion);
		
		return array($dependency);
	}

	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName) 
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(__DIR__ . '/config/generator.ini');
			
		return null;
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getHttpNotificationFileSyncObjectTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('FileSyncObjectType', $value);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getHttpNotificationTemplateTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('EventNotificationTemplateType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
