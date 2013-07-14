<?php

/**
 * 
 * Represents the Bulk upload job data for xml bulk upload
 * @package plugins.bulkUploadXml
 * @subpackage api.objects
 *
 */
class KalturaBulkUploadXmlJobData extends KalturaBulkUploadJobData
{
	/**
	 * 
	 * Maps between objects and the properties
	 * @var array
	 */
	private static $map_between_objects = array
	();

	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kBulkUploadXmlJobData();
			
		return parent::toObject($dbData);
	}
	
    public function toInsertableObject($object_to_fill = null , $props_to_skip = array())
	{
	    $dbObj = parent::toInsertableObject($object_to_fill, $props_to_skip);
	    
	    $this->setType();
	    
	    return $dbObj;
	}
	
	public function setType ()
	{
	    $this->type = kPluginableEnumsManager::coreToApi("KalturaBulkUploadType", BulkUploadXmlPlugin::getApiValue(BulkUploadXmlType::XML));
	}
}