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
    const CUSTOM_DATA_REFERENCE_ID = 'referenceId';
    
    const CUSTOM_DATA_TEMPLATE_ENTRY_ID = 'templateEntryId';

	const CUSTOM_DATA_EVENT_TYPE = 'eventType';

	const CUSTOM_DATA_TITLE = 'title';
	
	const CUSTOM_DATA_DESCRIPTION = 'description';

	const CUSTOM_DATA_TAGS = 'tags';
	
	const CUSTOM_DATA_CATEGORY_IDS = 'categoryIds';

	const CUSTOM_DATA_RESOURCE_ID = 'resourceId';

	const CUSTOM_DATA_START_TIME = 'startTime';

	const CUSTOM_DATA_DURATION = 'duration';

	const CUSTOM_DATA_END_TIME = 'endTime';

	const CUSTOM_DATA_RECURRENCE = 'recurrence';

	const CUSTOM_DATA_CO_EDITORS = 'coEditors';
	
	const CUSTOM_DATA_CO_PUBLISHERS = 'coPublishers';

	const CUSTOM_DATA_EVENT_ORGANIZER_ID = 'eventOrganizerId';
	
	const CUSTOM_DATA_CONTENT_OWNER_ID = 'contentOwnerId';
	
	const CUSTOM_DATA_TEMPLATE_ENTRY_TYPE = 'templateEntryType';
    
    
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
	
	public function getTemplateEntryId()	{return $this->getFromCustomData(self::CUSTOM_DATA_TEMPLATE_ENTRY_ID, null, parent::getTitle());}
	public function setTemplateEntryId($v)	{$this->putInCustomData(self::CUSTOM_DATA_TEMPLATE_ENTRY_ID, $v);}
	
	public function getEventType()	{return $this->getFromCustomData(self::CUSTOM_DATA_EVENT_TYPE, null, parent::getTitle());}
	public function setEventType($v)	{$this->putInCustomData(self::CUSTOM_DATA_EVENT_TYPE, $v);}
	
	public function getTitle()	{return $this->getFromCustomData(self::CUSTOM_DATA_TITLE, null, parent::getTitle());}
	public function setTitle($v)	{$this->putInCustomData(self::CUSTOM_DATA_TITLE, $v);}
	
	public function getDescription()	{return $this->getFromCustomData(self::CUSTOM_DATA_DESCRIPTION, null, parent::getTitle());}
	public function setDescription($v)	{$this->putInCustomData(self::CUSTOM_DATA_DESCRIPTION, $v);}
	
	public function getTags()	{return $this->getFromCustomData(self::CUSTOM_DATA_TAGS, null, parent::getTitle());}
	public function setTags($v)	{$this->putInCustomData(self::CUSTOM_DATA_TAGS, $v);}
	
	public function getCategoryIds()	{return $this->getFromCustomData(self::CUSTOM_DATA_CATEGORY_IDS, null, parent::getTitle());}
	public function setCategoryIds($v)	{$this->putInCustomData(self::CUSTOM_DATA_CATEGORY_IDS, $v);}
	
	public function getResourceId()	{return $this->getFromCustomData(self::CUSTOM_DATA_RESOURCE_ID, null, parent::getTitle());}
	public function setResourceId($v)	{$this->putInCustomData(self::CUSTOM_DATA_RESOURCE_ID, $v);}
	
	public function getStartTime()	{return $this->getFromCustomData(self::CUSTOM_DATA_START_TIME, null, parent::getTitle());}
	public function setStartTime($v)	{$this->putInCustomData(self::CUSTOM_DATA_START_TIME, $v);}
	
	public function getDuration()	{return $this->getFromCustomData(self::CUSTOM_DATA_DURATION, null, parent::getTitle());}
	public function setDuration($v)	{$this->putInCustomData(self::CUSTOM_DATA_DURATION, $v);}
	
	public function getEndTime()	{return $this->getFromCustomData(self::CUSTOM_DATA_END_TIME, null, parent::getTitle());}
	public function setEndTime($v)	{$this->putInCustomData(self::CUSTOM_DATA_END_TIME, $v);}
	
	public function getRecurrence()	{return $this->getFromCustomData(self::CUSTOM_DATA_RECURRENCE, null, parent::getTitle());}
	public function setRecurrence($v)	{$this->putInCustomData(self::CUSTOM_DATA_RECURRENCE, $v);}
	
	public function getCoEditors()	{return $this->getFromCustomData(self::CUSTOM_DATA_CO_EDITORS, null, parent::getTitle());}
	public function setCoEditors($v)	{$this->putInCustomData(self::CUSTOM_DATA_CO_EDITORS, $v);}
	
	public function getCoPublishers()	{return $this->getFromCustomData(self::CUSTOM_DATA_CO_PUBLISHERS, null, parent::getTitle());}
	public function setCoPublishers($v)	{$this->putInCustomData(self::CUSTOM_DATA_CO_PUBLISHERS, $v);}
	
	public function getContentOwnerId()	{return $this->getFromCustomData(self::CUSTOM_DATA_CONTENT_OWNER_ID, null, parent::getTitle());}
	public function setContentOwnerId($v)	{$this->putInCustomData(self::CUSTOM_DATA_CONTENT_OWNER_ID, $v);}
	
	public function getEventOrganizerId()	{return $this->getFromCustomData(self::CUSTOM_DATA_EVENT_ORGANIZER_ID, null, parent::getTitle());}
	public function setEventOrganizerId($v)	{$this->putInCustomData(self::CUSTOM_DATA_EVENT_ORGANIZER_ID, $v);}
	
	public function getTemplateEntryType()	{return $this->getFromCustomData(self::CUSTOM_DATA_TEMPLATE_ENTRY_TYPE, null, parent::getTitle());}
	public function setTemplateEntryType($v)	{$this->putInCustomData(self::CUSTOM_DATA_TEMPLATE_ENTRY_TYPE, $v);}
	
}