<?php
/**
 * Extension plugin for scheduled task plugin to add support metadata related object tasks
 *
 * @package plugins.scheduledTaskMetadata
 */
class ScheduledTaskMetadataPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'scheduledTaskMetadata';
	
	const SCHEDULED_TASK_PLUGIN_NAME = 'scheduledTask';
	
	const METADATA_PLUGIN_NAME = 'metadata';
	const METADATA_PLUGIN_VERSION_MAJOR = 1;
	const METADATA_PLUGIN_VERSION_MINOR = 0;
	const METADATA_PLUGIN_VERSION_BUILD = 0;
	
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
		$metadataVersion = new KalturaVersion(self::METADATA_PLUGIN_VERSION_MAJOR, self::METADATA_PLUGIN_VERSION_MINOR, self::METADATA_PLUGIN_VERSION_BUILD);
		
		$scheduledTaskDependency = new KalturaDependency(self::SCHEDULED_TASK_PLUGIN_NAME);
		$metadataDependency = new KalturaDependency(self::METADATA_PLUGIN_NAME, $metadataVersion);
		
		return array($scheduledTaskDependency, $metadataDependency);
	}
			
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('ExecuteMetadataXsltObjectTaskType');
	
		if($baseEnumName == 'ObjectTaskType')
			return array('ExecuteMetadataXsltObjectTaskType');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if (class_exists('KalturaClient'))
		{
			if ($baseClass == 'KObjectTaskEntryEngineBase' && $enumValue == KalturaObjectTaskType::EXECUTE_METADATA_XSLT)
				return new KObjectTaskExecuteMetadataXsltEngine();
		}
		else
		{
			$apiValue = self::getApiValue(ExecuteMetadataXsltObjectTaskType::EXECUTE_METADATA_XSLT);
			$executeMetadataXsltObjectTaskCoreValue = kPluginableEnumsManager::apiToCore('ObjectTaskType', $apiValue);
			if($baseClass == 'KalturaObjectTask' && $enumValue == $executeMetadataXsltObjectTaskCoreValue)
				return new KalturaExecuteMetadataXsltObjectTask();

			if ($baseClass == 'KObjectTaskEntryEngineBase' && $enumValue == $apiValue)
				return new KObjectTaskExecuteMetadataXsltEngine();
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
