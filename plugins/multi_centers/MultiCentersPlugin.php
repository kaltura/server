<?php
/**
 * @package plugins.multiCenters
 */
class MultiCentersPlugin extends KalturaPlugin implements IKalturaServices, IKalturaEventConsumers, IKalturaObjectLoader, IKalturaConfigurator
{
	const PLUGIN_NAME = 'multiCenters';
	const MULTI_CENTERS_SYNCER_CLASS = 'kMultiCentersSynchronizer';
	const MUTLI_CENTERS_FLOW_MANAGER_CLASS = 'kMultiCentersFlowManager';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::MULTI_CENTERS_SYNCER_CLASS,
			self::MUTLI_CENTERS_FLOW_MANAGER_CLASS
		);
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
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'kJobData')
		{
			switch($enumValue)
			{
				case KalturaBatchJobType::FILESYNC_IMPORT:
					return new kFileSyncImportJobData();
			}
		}
	
		if($baseClass == 'KalturaJobData')
		{
			switch($enumValue)
			{
				case KalturaBatchJobType::FILESYNC_IMPORT:
					return new KalturaFileSyncImportJobData();
			}
		}
		
		return null;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'kJobData')
		{
			switch($enumValue)
			{
				case KalturaBatchJobType::FILESYNC_IMPORT:
					return 'kFileSyncImportJobData';
			}
		}
	
		if($baseClass == 'KalturaJobData')
		{
			switch($enumValue)
			{
				case KalturaBatchJobType::FILESYNC_IMPORT:
					return 'KalturaFileSyncImportJobData';
			}
		}
		
		return null;
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
