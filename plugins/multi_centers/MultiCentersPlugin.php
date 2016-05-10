<?php
/**
 * @package plugins.multiCenters
 */
class MultiCentersPlugin extends KalturaPlugin implements IKalturaServices
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
}
