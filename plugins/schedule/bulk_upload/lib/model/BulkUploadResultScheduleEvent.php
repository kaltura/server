<?php
/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package plugins.scheduleBulkUpload
 * @subpackage model
 */ 
class BulkUploadResultScheduleEvent extends BulkUploadResult
{
    const CUSTOM_DATA_REFERENCE_ID = "referenceId";
    
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
	
    public function getReferenceId()	{return $this->getFromCustomData(self::CUSTOM_DATA_REFERENCE_ID, null, parent::getTitle());}
	public function setReferenceId($v)	{$this->putInCustomData(self::CUSTOM_DATA_REFERENCE_ID, $v);}
}