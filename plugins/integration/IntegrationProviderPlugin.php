<?php
/**
 * @package plugins.integration
 */
abstract class IntegrationProviderPlugin extends KalturaPlugin implements IIntegrationProviderPlugin, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader
{
	const INTEGRATION_PLUGIN_NAME = 'integration';
	
	abstract function getProvider();

	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$class = get_called_class();
		$integrationVersion = $class::getRequiredIntegrationPluginVersion();
		$dependency = new KalturaDependency(IntegrationPlugin::getPluginName(), $integrationVersion);
		
		return array($dependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		$class = get_called_class();
		$integrationProviderClassName = $class::getIntegrationProviderClassName();
		if(is_null($baseEnumName))
			return array($integrationProviderClassName);
	
		if($baseEnumName == 'IntegrationProviderType')
			return array($integrationProviderClassName);
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{			
		$class = get_called_class();
		$objectClass = $class::getObjectClass($baseClass, $enumValue);
		if (is_null($objectClass)) 
		{
			return null;
		}
		
		if (!is_null($constructorArgs))
		{
			$reflect = new ReflectionClass($objectClass);
			return $reflect->newInstanceArgs($constructorArgs);
		}
		else
		{
			return new $objectClass();
		}
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getIntegrationProviderCoreValue($valueName)
	{
		$class = get_called_class();
		$value = $class::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('IntegrationProviderType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		$class = get_called_class();
		return $class::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
