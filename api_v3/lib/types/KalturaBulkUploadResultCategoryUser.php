<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadResultCategoryUser extends KalturaBulkUploadResult
{
   /**
    * @var int
    */
   public $categoryId;
   
   /**
    * @var string
    */
   public $userId;
   
   /**
    * @var KalturaCategoryUserPermissionLevel
    */
   public $permissionLevel;
   
   /**
    * @var KalturaUpdateMethodType
    */
   public $updateMethod;
    
    private static $mapBetweenObjects = array
	(
	    "categoryId",
		"userId",
	    "permissionLevel",
	    "updateMethod",
	);
	
    public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$dbObject = parent::toInsertableObject(new BulkUploadResultCategoryKuser(), $props_to_skip);
		
		$pluginsData = $this->addPluginData();
		$dbObject->setPluginsData($pluginsData);
		
		return $dbObject;
	}
}