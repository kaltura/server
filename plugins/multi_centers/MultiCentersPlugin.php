<?php
class MultiCentersPlugin extends KalturaPlugin
{
	const MULTI_CENTERS_SYNCER_CLASS = 'kMultiCentersSynchronizer';
	const MUTLI_CENTERS_FLOW_MANAGER_CLASS = 'kMultiCentersFlowManager';
	
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
}
