<?php
/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class BulkUploadResultCategory extends BulkUploadResult
{
    //Category property names
    const RELATIVE_PATH = "relative_path";
    const NAME = "name";
    const TAGS = "tags";
    const DESCRIPTION = "description";
    const REFERENCE_ID = "reference_id";
    const APPEAR_IN_LIST = "appear_in_list";
    const PRIVACY = "privacy";
    const INHERITANCE = "inheritanceType";
    const USER_JOIN_POLICY = "user_join_policy";
    const DEFAULT_PERMISSION_LEVEL = "default_permission_level";
    const OWNER = "owner";
    const CONTRIBUTION_POLICY = "contribution_policy";
    const PARTNER_SORT_VALUE = "partner_sort_value";
    const MODERATION = "moderation";
    
    /* (non-PHPdoc)
     * @see BulkUploadResult::handleRelatedObjects()
     */
    public function handleRelatedObjects()
    {
        $category = $this->getObject();
        if ($category)
        {
            $category->setBulkUploadId($this->getBulkUploadJobId());
            $category->save();
        }
    }
    
    /* (non-PHPdoc)
     * @see BulkUploadResult::getObject()
     */
    public function getObject()
    {
        //Return deleted categories as well.
        return categoryPeer::retrieveByPKNoFilter($this->getObjectId());
    }
    
    //Set properties for categories
	
	public function getRelativePath()	{return $this->getFromCustomData(self::RELATIVE_PATH);}
	public function setRelativePath($v)	{$this->putInCustomData(self::RELATIVE_PATH, $v);}
	
	public function getName()	{return $this->getFromCustomData(self::NAME);}
	public function setName($v)	{$this->putInCustomData(self::NAME, $v);}
	
	public function getReferenceId()	{return $this->getFromCustomData(self::REFERENCE_ID);}
	public function setReferenceId($v)	{$this->putInCustomData(self::REFERENCE_ID, $v);}
	
    public function getDescription()	{return $this->getFromCustomData(self::DESCRIPTION);}
	public function setDescription($v)	{$this->putInCustomData(self::DESCRIPTION, $v);}
	
    public function getTags()	{return $this->getFromCustomData(self::TAGS);}
	public function setTags($v)	{$this->putInCustomData(self::TAGS, $v);}
	
	public function getAppearInList()	{return $this->getFromCustomData(self::APPEAR_IN_LIST);}
	public function setAppearInList($v)	{$this->putInCustomData(self::APPEAR_IN_LIST, $v);}
	
    public function getPrivacy()	{return $this->getFromCustomData(self::PRIVACY);}
	public function setPrivacy($v)	{$this->putInCustomData(self::PRIVACY, $v);}
	
    public function getInheritance()	{return $this->getFromCustomData(self::INHERITANCE);}
	public function setInheritance($v)	{$this->putInCustomData(self::INHERITANCE, $v);}
	
    public function getUserJoinPolicy()	{return $this->getFromCustomData(self::USER_JOIN_POLICY);}
	public function setUserJoinPolicy($v)	{$this->putInCustomData(self::USER_JOIN_POLICY, $v);}
	
    public function getDefaultPermissionLevel()	{return $this->getFromCustomData(self::DEFAULT_PERMISSION_LEVEL);}
	public function setDefaultPermissionLevel($v)	{$this->putInCustomData(self::DEFAULT_PERMISSION_LEVEL, $v);}
	
    public function getOwner()	{return $this->getFromCustomData(self::OWNER);}
	public function setOwner($v)	{$this->putInCustomData(self::OWNER, $v);}
	
    public function getContributionPolicy()	{return $this->getFromCustomData(self::CONTRIBUTION_POLICY);}
	public function setContributionPolicy($v)	{$this->putInCustomData(self::CONTRIBUTION_POLICY, $v);}

	public function getPartnerSortValue()	{return $this->getFromCustomData(self::PARTNER_SORT_VALUE);}
	public function setPartnerSortValue($v)	{$this->putInCustomData(self::PARTNER_SORT_VALUE, $v);}
	
    public function getModeration()	{return $this->getFromCustomData(self::MODERATION);}
	public function setModeration($v)	{$this->putInCustomData(self::MODERATION, $v);}
	
}