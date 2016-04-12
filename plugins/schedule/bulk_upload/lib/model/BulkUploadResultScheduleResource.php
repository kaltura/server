<?php
/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package plugins.scheduleBulkUpload
 * @subpackage model
 */ 
class BulkUploadResultScheduleResource extends BulkUploadResult
{
    const CUSTOM_DATA_RESOURCE_ID = "resourceId";
    const CUSTOM_DATA_TYPE = "type";
    const CUSTOM_DATA_SYSTEM_NAME = "systemName";
    const CUSTOM_DATA_PARENT_TYPE = "parentType";
    const CUSTOM_DATA_PARENT_SYSTEM_NAME = "parentSystemName";
    
	/* (non-PHPdoc)
	 * @see BulkUploadResult::updateStatusFromObject()
	 */
	public function updateStatusFromObject()
	{
		$scheduleEvent = $this->getObject();
		if(!$scheduleEvent)
			return $this->getStatus();
			
		$this->setObjectStatus($scheduleEvent->getStatus());
		$this->setStatus(BulkUploadResultStatus::OK);
		$this->save();
		
		return $this->getStatus();
	}
	
	/* (non-PHPdoc)
	 * @see BulkUploadResult::getObject()
	 */
	public function getObject()
	{
	    return ScheduleEventPeer::retrieveByPKNoFilter($this->getObjectId());   
	}
	
    public function getResourceId()	{return $this->getFromCustomData(self::CUSTOM_DATA_RESOURCE_ID, null, parent::getTitle());}
	public function setResourceId($v)	{$this->putInCustomData(self::CUSTOM_DATA_RESOURCE_ID, $v);}

	public function getType()	{return $this->getFromCustomData(self::CUSTOM_DATA_TYPE, null, parent::getTitle());}
	public function setType($v)	{$this->putInCustomData(self::CUSTOM_DATA_TYPE, $v);}

	public function getSystemName()	{return $this->getFromCustomData(self::CUSTOM_DATA_SYSTEM_NAME, null, parent::getTitle());}
	public function setSystemName($v)	{$this->putInCustomData(self::CUSTOM_DATA_SYSTEM_NAME, $v);}

	public function getParentType()	{return $this->getFromCustomData(self::CUSTOM_DATA_PARENT_TYPE, null, parent::getTitle());}
	public function setParentType($v)	{$this->putInCustomData(self::CUSTOM_DATA_PARENT_TYPE, $v);}

	public function getParentSystemName()	{return $this->getFromCustomData(self::CUSTOM_DATA_PARENT_SYSTEM_NAME, null, parent::getTitle());}
	public function setParentSystemName($v)	{$this->putInCustomData(self::CUSTOM_DATA_PARENT_SYSTEM_NAME, $v);}
}