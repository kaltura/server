<?php
/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class BulkUploadResultCategoryEntry extends BulkUploadResult
{
    //Category property names
    const CATEGORY_ID = "category_id";
    const ENTRY_ID = "entry_id";
     
    /* (non-PHPdoc)
     * @see BulkUploadResult::handleRelatedObjects()
     */
    public function handleRelatedObjects()
    {
        $categoryEntry = $this->getObject();
        if ($categoryEntry)
        {
            $categoryEntry->setBulkUploadId($this->getBulkUploadJobId());
            $categoryEntry->save();
        }
    }
    
    /* (non-PHPdoc)
     * @see BulkUploadResult::getObject()
     */
    public function getObject()
    {
        //TODO: check how to get deleted
        return categoryEntryPeer::retrieveByPK($this->getObjectId());
    }
    
    //Set properties for category entries
	
	public function getCategoryId()	{return $this->getFromCustomData(self::CATEGORY_ID);}
	public function setCategoryId($v)	{$this->putInCustomData(self::CATEGORY_ID, $v);}
	
	public function getEntryId()	{return $this->getFromCustomData(self::ENTRY_ID);}
	public function setEntryId($v)	{$this->putInCustomData(self::ENTRY_ID, $v);}
}