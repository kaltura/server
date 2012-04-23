<?php
class BulkUploadResultCategoryKuser extends BulkUploadResult
{
    //categoryUser property names
    const CATEGORY_ID = "category_id";
    const USER_ID = "user_id";
    const PERMISSION_LEVEL = "permission_level";
    const UPDATE_METHOD = "update_method";
    
    public static function getClosedStatuses ()
    {
        return array(
            CategoryKuserStatus::ACTIVE,
            CategoryKuserStatus::PENDING,
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see BulkUploadResult::handleRelatedObjects()
     */
    public function handleRelatedObjects()
    {
        $categoryKuser = $this->getObject();
        $categoryKuser->setBulkUploadId($this->getBulkUploadJobId());
        $categoryKuser->save();
    }
    
    /* (non-PHPdoc)
     * @see BulkUploadResult::updateStatusFromObject()
     */
    public function updateStatusFromObject()
    {
        if ($this->getObjectStatus() == CategoryKuserStatus::ACTIVE)
        {
            $this->setStatus(BulkUploadResultStatus::OK);
            $this->save();
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

    public function getUserId()	{return $this->getFromCustomData(self::USER_ID);}
	public function setUserId($v)	{$this->putInCustomData(self::USER_ID, $v);}
	
    public function getPermissionLevel()	{return $this->getFromCustomData(self::PERMISSION_LEVEL);}
	public function setPermissionLevel($v)	{$this->putInCustomData(self::PERMISSION_LEVEL, $v);}
	
    public function getUpdateMethod()	{return $this->getFromCustomData(self::UPDATE_METHOD);}
	public function setUpdateMethod($v)	{$this->putInCustomData(self::UPDATE_METHOD, $v);}
}