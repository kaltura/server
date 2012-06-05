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
	    //No need to add objectId to result with status ERROR
	    if ($this->status == KalturaBulkUploadResultStatus::ERROR)
	        return parent::toInsertableObject(new BulkUploadResultCategoryKuser(), $props_to_skip);
	        
	    $kuser = kuserPeer::getKuserByPartnerAndUid($this->partnerId, $this->userId);
	    if (!$kuser)
	    {
	        throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
	    }
	    $categoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($this->categoryId, $kuser->getId());
	    if ($categoryKuser)
	        $this->objectId = $categoryKuser->getId();
	        
		return parent::toInsertableObject(new BulkUploadResultCategoryKuser(), $props_to_skip);
	}
}