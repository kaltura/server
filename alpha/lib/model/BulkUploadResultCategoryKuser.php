<?php
/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class BulkUploadResultCategoryKuser extends BulkUploadResult
{
    //categoryUser property names
    const CATEGORY_ID = "category_id";
    const CATEGORY_REFERENCE_ID = "category_reference_id";
    const USER_ID = "user_id";
    const PERMISSION_LEVEL = "permission_level";
    const UPDATE_METHOD = "update_method";
    const REQUIRED_OBJECT_STATUS = "required_object_status";
    
   
    /**
     * (non-PHPdoc)
     * @see BulkUploadResult::handleRelatedObjects()
     */
    public function handleRelatedObjects()
    {
        $categoryKuser = $this->getObject();
        if ($categoryKuser)
        {
            $categoryKuser->setBulkUploadId($this->getBulkUploadJobId());
            $categoryKuser->save();
        }
    }
    
    /* (non-PHPdoc)
     * @see BulkUploadResult::getObject()
     */
    public function getObject()
    {
        return categoryKuserPeer::retrieveByPK($this->getObjectId());
    }
    
    //Set properties for category users
	
    public function getCategoryId()	{return $this->getFromCustomData(self::CATEGORY_ID);}
	public function setCategoryId($v)	{$this->putInCustomData(self::CATEGORY_ID, $v);}
	
    public function getCategoryReferenceId()	{return $this->getFromCustomData(self::CATEGORY_REFERENCE_ID);}
	public function setCategoryReferenceId($v)	{$this->putInCustomData(self::CATEGORY_REFERENCE_ID, $v);}

    public function getUserId()	{return $this->getFromCustomData(self::USER_ID);}
	public function setUserId($v)	{$this->putInCustomData(self::USER_ID, $v);}
	
    public function getPermissionLevel()	{return $this->getFromCustomData(self::PERMISSION_LEVEL);}
	public function setPermissionLevel($v)	{$this->putInCustomData(self::PERMISSION_LEVEL, $v);}
	
    public function getUpdateMethod()	{return $this->getFromCustomData(self::UPDATE_METHOD);}
	public function setUpdateMethod($v)	{$this->putInCustomData(self::UPDATE_METHOD, $v);}
	
	public function getRequiredStatus(){return $this->getFromCustomData(self::REQUIRED_OBJECT_STATUS);}
	public function setRequiredStatus($v){$this->putInCustomData(self::REQUIRED_OBJECT_STATUS, $v);}
}