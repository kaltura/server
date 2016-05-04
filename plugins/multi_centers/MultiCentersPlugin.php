<?php
/**
 * @package plugins.multiCenters
 */
class MultiCentersPlugin extends KalturaPlugin implements IKalturaServices, IKalturaConfigurator
{
	const PLUGIN_NAME = 'multiCenters';
	
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
			'filesyncImportBatch' => 'FileSyncImportBatchService',
		);
		return $map;
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		return null;
	}
}
