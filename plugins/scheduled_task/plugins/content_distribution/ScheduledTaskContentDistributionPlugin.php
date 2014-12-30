<?php
/**
 * Extension plugin for scheduled task plugin to add support for distributing content
 *
 * @package plugins.scheduledTaskEventNotification
 */
class ScheduledTaskContentDistributionPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader, IKalturaConfigurator
{
	const PLUGIN_NAME = 'scheduledTaskContentDistribution';
	
	const SCHEDULED_TASK_PLUGIN_NAME = 'scheduledTask';
	
	const CONTENT_DISTRIBUTION_PLUGIN_NAME = 'contentDistribution';
	const CONTENT_DISTRIBUTION_PLUGIN_VERSION_MAJOR = 1;
	const CONTENT_DISTRIBUTION_PLUGIN_VERSION_MINOR = 0;
	const CONTENT_DISTRIBUTION_PLUGIN_VERSION_BUILD = 0;
	
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
		$eventNotificationVersion = new KalturaVersion(self::CONTENT_DISTRIBUTION_PLUGIN_VERSION_MAJOR, self::CONTENT_DISTRIBUTION_PLUGIN_VERSION_MINOR, self::CONTENT_DISTRIBUTION_PLUGIN_VERSION_BUILD);
		
		$scheduledTaskDependency = new KalturaDependency(self::SCHEDULED_TASK_PLUGIN_NAME);
		$eventNotificationDependency = new KalturaDependency(self::CONTENT_DISTRIBUTION_PLUGIN_NAME, $eventNotificationVersion);
		
		return array($scheduledTaskDependency, $eventNotificationDependency);
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('DistributeObjectTaskType');
	
		if($baseEnumName == 'ObjectTaskType')
			return array('DistributeObjectTaskType');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if (class_exists('Kaltura_Client_Client'))
			return null;

		if (class_exists('KalturaClient'))
		{
			if ($baseClass == 'KObjectTaskEntryEngineBase' && $enumValue == KalturaObjectTaskType::DISTRIBUTE)
				return new KObjectTaskDistributeEngine();
		}
		else
		{
			$apiValue = self::getApiValue(DistributeObjectTaskType::DISTRIBUTE);
			$distributeObjectTaskCoreValue = kPluginableEnumsManager::apiToCore('ObjectTaskType', $apiValue);
			if($baseClass == 'KalturaObjectTask' && $enumValue == $distributeObjectTaskCoreValue)
				return new KalturaDistributeObjectTask();

			if ($baseClass == 'KObjectTaskEntryEngineBase' && $enumValue == $apiValue)
				return new KObjectTaskDistributeEngine();
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

	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');

		return null;
	}
}
