<?php
/**
 * @package plugins.rabbitMQ
 */
class RabbitMQPlugin extends KalturaPlugin implements IKalturaPending, IKalturaObjectLoader, IKalturaQueuePlugin
{
	const PLUGIN_NAME = 'rabbitMQ';
	
	const QUEUE_PLUGIN_NAME = 'queue';
	const QUEUE_PLUGIN_VERSION_MAJOR = 1;
	const QUEUE_PLUGIN_VERSION_MINOR = 0;
	const QUEUE_PLUGIN_VERSION_BUILD = 0;

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
        if ($baseClass == 'QueueProvider' && (is_null($enumValue) || $enumValue == self::getRabbitMQProviderTypeCoreValue(RabbitMQProviderType::RABBITMQ)))
            return 'RabbitMQProvider';
    
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
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getPushNotificationTemplateTypeCoreValue($valueName)
	{
	    $value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	    return kPluginableEnumsManager::apiToCore('QueueProviderType', $value);
	}	
	
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
	    $minVersion = new KalturaVersion(
	        self::QUEUE_PLUGIN_VERSION_MAJOR,
	        self::QUEUE_PLUGIN_VERSION_MINOR,
	        self::QUEUE_PLUGIN_VERSION_BUILD
	    );
	    $dependency = new KalturaDependency(self::QUEUE_PLUGIN_NAME, $minVersion);
	
	    return array($dependency);
	}
}
