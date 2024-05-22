<?php

/**
 * @package Scheduler
 * @subpackage ExportCsv
 */
require_once(__DIR__.'/../../../../alpha/apps/kaltura/lib/dateUtils.class.php');
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

			case KalturaExportObjectType::ENTRY:
				return new KEntryExportEngine();
			
			case KalturaExportObjectType::CATEGORY:
				return new KCategoryExportEngine();

			default:
				return KalturaPluginManager::loadObject('KObjectExportEngine', $objectType);
		}
	}
	
	abstract public function fillCsv (&$csvFile, &$data);
	
	/**
	 * Generate the first csv row containing the fields
	 */
	abstract protected function addHeaderRowToCsv($csvFile,
	                                              $additionalFields,
	                                              $mappedFields = null);
}
