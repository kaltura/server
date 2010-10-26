<?php
class MultiCentersPlugin implements KalturaPlugin, KalturaServicesPlugin, KalturaEventConsumersPlugin
{
	const PLUGIN_NAME = 'multiCenters';
	const MULTI_CENTERS_SYNCER_CLASS = 'kMultiCentersSynchronizer';
	const MUTLI_CENTERS_FLOW_MANAGER_CLASS = 'kMultiCentersFlowManager';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	public static function isAllowedPartner($partnerId)
	{
		return true;
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
			'fileSyncImportBatch' => 'FileSyncImportBatchService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return null;
	}
}
