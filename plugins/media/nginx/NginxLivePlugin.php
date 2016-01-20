<?php
/**
 * Enable serving live facilities to the NGINX RTMP server
 * @package plugins.nginxLive
 */
class NginxLivePlugin extends KalturaPlugin implements IKalturaVersion, IKalturaServices, IKalturaConfigurator, IKalturaObjectLoader, IKalturaEnumerator
{
	const PLUGIN_NAME = 'nginxLive';
	
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'liveInternal' => 'LiveInternalService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('NginxLiveMediaServerNodeType');
	
		if($baseEnumName == 'serverNodeType')
			return array('NginxLiveMediaServerNodeType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaServerNode' && $enumValue == self::getNginxLiveMediaServerTypeCoreValue(NginxLiveMediaServerNodeType::NGINX_LIVE_MEDIA_SERVER))
			return new KalturaNginxLiveMediaServerNode();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'ServerNode' && $enumValue == self::getNginxLiveMediaServerTypeCoreValue(NginxLiveMediaServerNodeType::NGINX_LIVE_MEDIA_SERVER))
			return 'NginxLiveMediaServerNode';
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePoint::getCuePointTypeCoreValue()
	 */
	public static function getNginxLiveMediaServerTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('serverNodeType', $value);
	}
}
