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
   public $categoryReferenceId;
   
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
   
   /**
    * @var int
    */
   public $requiredObjectStatus;
    
    private static $mapBetweenObjects = array
	(
	    "categoryId",
	    "categoryReferenceId",
		"userId",
	    "permissionLevel",
	    "updateMethod",
		"requiredObjectStatus",
	);
	
    public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
    public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$dbObject = parent::toInsertableObject(new BulkUploadResultCategoryKuser(), $props_to_skip);
		
		$pluginsData = $this->createPluginDataMap();
		$dbObject->setPluginsData($pluginsData);
		
		return $dbObject;
	}
}