<?php
/**
 * @package plugins.ws
 */
class WsPlugin extends KalturaPlugin implements IKalturaServices, IKalturaConfigurator
{
	const PLUGIN_NAME = 'ws';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'soap' => 'SoapService',
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
}
