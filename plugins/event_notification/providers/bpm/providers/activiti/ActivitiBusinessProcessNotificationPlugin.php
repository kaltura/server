<?php
/**
 * @package plugins.activitiBusinessProcessNotification
 */
class ActivitiBusinessProcessNotificationPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaPending, IKalturaObjectLoader, IKalturaEnumerator
{
	const PLUGIN_NAME = 'activitiBusinessProcessNotification';
	
	const BPM_NOTIFICATION_PLUGIN_NAME = 'businessProcessNotification';
	const BPM_NOTIFICATION_PLUGIN_VERSION_MAJOR = 1;
	const BPM_NOTIFICATION_PLUGIN_VERSION_MINOR = 0;
	const BPM_NOTIFICATION_PLUGIN_VERSION_BUILD = 0;
	
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
			return array('ActivitiBusinessProcessProvider');
	
		if($baseEnumName == 'BusinessProcessProvider')
			return array('ActivitiBusinessProcessProvider');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
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
		if($baseClass == 'kBusinessProcessProvider')
		{
			if(class_exists('Kaltura_Client_BusinessProcessNotification_Enum_BusinessProcessProvider') && $enumValue == Kaltura_Client_BusinessProcessNotification_Enum_BusinessProcessProvider::ACTIVITI)
				return 'kActivitiBusinessProcessProvider';
				
			if(class_exists('KalturaBusinessProcessProvider') && $enumValue == KalturaBusinessProcessProvider::ACTIVITI)
				return 'kActivitiBusinessProcessProvider';
		}
			
		if($baseClass == 'BusinessProcessServer' && $enumValue == self::getActivitiBusinessProcessProviderCoreValue(ActivitiBusinessProcessProvider::ACTIVITI))
			return 'ActivitiBusinessProcessServer';
			
		if($baseClass == 'KalturaBusinessProcessServer' && $enumValue == self::getActivitiBusinessProcessProviderCoreValue(ActivitiBusinessProcessProvider::ACTIVITI))
			return 'KalturaActivitiBusinessProcessServer';
					
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn() 
	{
		$minVersion = new KalturaVersion(
			self::BPM_NOTIFICATION_PLUGIN_VERSION_MAJOR,
			self::BPM_NOTIFICATION_PLUGIN_VERSION_MINOR,
			self::BPM_NOTIFICATION_PLUGIN_VERSION_BUILD
		);
		$dependency = new KalturaDependency(self::BPM_NOTIFICATION_PLUGIN_NAME, $minVersion);
		
		return array($dependency);
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getActivitiBusinessProcessProviderCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BusinessProcessProvider', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
