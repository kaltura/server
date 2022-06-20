<?php

/**
 * @package plugins.kafkaNotification
 */
class KafkaNotificationPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaPending, IKalturaObjectLoader, IKalturaEnumerator, IKalturaApplicationTranslations
{
	const PLUGIN_NAME = 'kafkaNotification';
	
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
		{
			// check that both the Kafka plugin and the event notification plugin are enabled
			return $partner->getPluginEnabled(self::PLUGIN_NAME) && EventNotificationPlugin::isAllowedPartner($partnerId);
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
			return array('KafkaNotificationTemplateType');
		
		if ($baseEnumName == 'EventNotificationTemplateType')
			return array('KafkaNotificationTemplateType');
		
		if ($baseEnumName == 'messageFormat')
			return array('KalturaKafkaNotificationFormat');
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		
		$class = self::getObjectClass($baseClass, $enumValue);
		if ($class)
		{
			if (is_array($constructorArgs))
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
		if ($baseClass == 'EventNotificationTemplate' && $enumValue == self::getKafkaNotificationTemplateTypeCoreValue(KafkaNotificationTemplateType::KAFKA))
			return 'KafkaNotificationTemplate';
		
		if ($baseClass == 'KalturaEventNotificationTemplate' && $enumValue == self::getKafkaNotificationTemplateTypeCoreValue(KafkaNotificationTemplateType::KAFKA))
			return 'KalturaKafkaNotificationTemplate';
		
		if ($baseClass == 'Kaltura_Client_EventNotification_Type_EventNotificationTemplate' && $enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::KAFKA)
			return 'Kaltura_Client_KafkaNotification_Type_KafkaNotificationTemplate';
		
		if ($baseClass == 'Form_EventNotificationTemplateConfiguration' && $enumValue == Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType::KAFKA)
			return 'Form_KafkaNotificationTemplateConfiguration';
		
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
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getKafkaNotificationTemplateTypeCoreValue($valueName)
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
	
	/* (non-PHPdoc)
	 * @see IKalturaApplicationTranslations::getTranslations()
	 */
	public static function getTranslations($locale)
	{
		$array = array();
		
		$langFilePath = __DIR__ . "/config/lang/$locale.php";
		if (!file_exists($langFilePath))
		{
			$default = 'en';
			$langFilePath = __DIR__ . "/config/lang/$default.php";
		}
		
		$array = include($langFilePath);
		
		return array($locale => $array);
	}
	
}