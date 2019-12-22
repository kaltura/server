<?php

/**
 * @package Scheduler
 * @subpackage ExportCsv
 */
abstract class KObjectExportEngine
{
	/**
	 * @param int $objectType of enum KalturaExportObjectType
	 * @return KObjectExportEngine
	 */
	public static function getInstance($objectType)
	{
		switch($objectType)
		{
			case KalturaExportObjectType::USER:
				return new KUserExportEngine();
			
			
			default:
				return KalturaPluginManager::loadObject('KObjectExportEngine', $objectType);
		}
	}
	
	abstract public function fillCsv (&$csvFile, &$data);
	
	/**
	 * Generate the first csv row containing the fields
	 */
	abstract protected function addHeaderRowToCsv($csvFile, $additionalFields);
}
