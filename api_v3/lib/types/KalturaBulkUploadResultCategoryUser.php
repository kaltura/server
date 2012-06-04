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
		return parent::toInsertableObject(new BulkUploadResultCategoryKuser(), $props_to_skip);
	}
	
	public function toObject($object_to_fill = null , $props_to_skip = array() )
	{
	    if (is_null($object_to_fill))
	    {
	        return null;
	    }
	    
	    $kuser = kuserPeer::getKuserByPartnerAndUid($this->partnerId, $this->userId);
	    if (!$kuser)
	    {
	        throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
	    }
	    $categoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($this->categoryId, $kuser->getId());
	    if ($categoryKuser)
	        $this->objectId = $categoryKuser->getId();
	    
	    return parent::toObject($object_to_fill, $props_to_skip);
	}
}