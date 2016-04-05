<?php
/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class BulkUploadResultEntry extends BulkUploadResult
{
    const CUSTOM_DATA_SSH_PRIVATE_KEY = 'sshPrivateKey';
    const CUSTOM_DATA_SSH_PUBLIC_KEY = 'sshPublicKey';
    const CUSTOM_DATA_SSH_KEY_PASSPHRASE = 'sshKeyPassphrase';
    
    //Entry property names
    const TITLE = "title";
    const DESCRIPTION = "description";
    const TAGS = "tags";
    const URL = "url";
    const CONTENT_TYPE = "content_type";
    const CONVERSION_PROFILE_ID = "conversion_profile_id";
    const ACCESS_CONSTROL_PROFILE_ID= "access_control_profile_id";
    const CATEGORY = "category";
    const SCHEDULE_START_DATE = "schedule_start_date";
    const SCHEDULE_END_DATE = "schedule_end_date";
    const THUMBNAIL_URL = "thumbnail_url";
    const THUMBNAIL_SAVED = "thumbnail_saved";
    const ENTRY_STATUS = "entry_status";
    const CREATOR_ID = "creator_id";
    const OWNER_ID = "owner_id";
    const ENTITLED_USERS_PUBLISH = "entitled_users_publish";
    const ENTITLED_USERS_EDIT = "entitled_users_edit";
    const REFERENCE_ID = "reference_id";
    const TEMPLATE_ENTRY_ID = "template_entry_id";
    
    
    
	
	/* (non-PHPdoc)
	 * @see BulkUploadResult::updateStatusFromObject()
	 */
	public function updateStatusFromObject()
	{
		$entry = entryPeer::retrieveByPKNoFilter($this->getObjectId());
		if(!$entry)
			return $this->getStatus();
			
		$this->setEntryStatus($entry->getStatus());
		$this->save();
		
    	$closedStatuses = array (
			entryStatus::READY,
			entryStatus::DELETED,
			entryStatus::PENDING,
			entryStatus::NO_CONTENT,
    	);
    	
    	$errorStatuses = array (
    	    entryStatus::ERROR_IMPORTING,
			entryStatus::ERROR_CONVERTING,
	    );

		if(in_array($this->getObjectStatus(), $closedStatuses))
		{
			$this->updateEntryThumbnail();
		    $this->setStatus(BulkUploadResultStatus::OK);
		    $this->save();
		}
		else if (in_array($this->getObjectStatus(), $errorStatuses))
		{
		    $this->setStatus(BulkUploadResultStatus::ERROR);
		    $this->save();
		}
			
		return $this->getStatus();
	}
	
    protected function updateEntryThumbnail()
	{
		if(		$this->getEntryStatus() != entryStatus::READY 
			||	!strlen($this->getThumbnailUrl()) 
			||	$this->getThumbnailSaved()
		)
			return;
			
		try 
		{
		    $entry = entryPeer::retrieveByPK($this->getObjectId());
		    if ($entry){
				myEntryUtils::createThumbnailAssetFromFile($entry, $this->getThumbnailUrl());
				$this->setThumbnailSaved(true);
		    }
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return;
		}
		
		
	}
	
	/* (non-PHPdoc)
	 * @see BulkUploadResult::handleRelatedObjects()
	 */
	public function handleRelatedObjects()
	{
	    $entry = $this->getObject(); 
		if(!$entry)
			throw new kCoreException("Entry not found");
			
		if($this->getThumbnailUrl())
			$entry->setCreateThumb(false);
			
		$entry->setBulkUploadId($this->getBulkUploadJobId());
		$entry->save();
	}
	
	
	/* (non-PHPdoc)
	 * @see BulkUploadResult::getObject()
	 */
	public function getObject()
	{
	    //Return deleted entries as well.
	    return entryPeer::retrieveByPKNoFilter($this->getObjectId());   
	}
	
	public function getEntryId()
	{
		if($this->getObjectType() == BulkUploadObjectType::ENTRY)
			return $this->getObjectId();
			
		return null;
	}

	
	public function setEntryId($v)
	{
		$this->setObjectType(BulkUploadObjectType::ENTRY);
		return $this->setObjectId($v);
	}
    
    public function getSshPrivateKey()		{return $this->getFromCustomData(self::CUSTOM_DATA_SSH_PRIVATE_KEY);}
	public function setSshPrivateKey($v)	{$this->putInCustomData(self::CUSTOM_DATA_SSH_PRIVATE_KEY, $v);}
	
    public function getSshPublicKey()		{return $this->getFromCustomData(self::CUSTOM_DATA_SSH_PUBLIC_KEY);}
	public function setSshPublicKey($v)	    {$this->putInCustomData(self::CUSTOM_DATA_SSH_PUBLIC_KEY, $v);}
	
    public function getSshKeyPassphrase()	{return $this->getFromCustomData(self::CUSTOM_DATA_SSH_KEY_PASSPHRASE);}
	public function setSshKeyPassphrase($v)	{$this->putInCustomData(self::CUSTOM_DATA_SSH_KEY_PASSPHRASE, $v);}
	
	//Set properties for entries
    public function getTitle()	{return $this->getFromCustomData(self::TITLE, null, parent::getTitle());}
	public function setTitle($v)	{$this->putInCustomData(self::TITLE, $v);}
	
    public function getDescription()	{return $this->getFromCustomData(self::DESCRIPTION, null, parent::getDescription());}
	public function setDescription($v)	{$this->putInCustomData(self::DESCRIPTION, $v);}

	public function getTags()	{return $this->getFromCustomData(self::TAGS, null, parent::getTags());}
	public function setTags($v)	{$this->putInCustomData(self::TAGS, $v);}
	
	public function getUrl()	{return $this->getFromCustomData(self::URL, null, parent::getUrl());}
	public function setUrl($v)	{$this->putInCustomData(self::URL, $v);}
	
	public function getContentType()	{return $this->getFromCustomData(self::CONTENT_TYPE, null, parent::getContentType());}
	public function setContentType($v)	{$this->putInCustomData(self::CONTENT_TYPE, $v);}
	
	public function getConversionProfileId()	{return $this->getFromCustomData(self::CONVERSION_PROFILE_ID, null, parent::getConversionProfileId());}
	public function setConversionProfileId($v)	{$this->putInCustomData(self::CONVERSION_PROFILE_ID, $v);}
	
	public function getAcessControlProfileId()	{return $this->getFromCustomData(self::ACCESS_CONSTROL_PROFILE_ID, null, parent::getAccessControlProfileId());}
	public function setAccessControlProfileId($v)	{$this->putInCustomData(self::ACCESS_CONSTROL_PROFILE_ID, $v);}
	
	public function getCategory()	{return $this->getFromCustomData(self::CATEGORY, null, parent::getCategory());}
	public function setCategory($v)	{$this->putInCustomData(self::CATEGORY, $v);}
	
	public function getScheduleStartDate($format = 'Y-m-d H:i:s')	{return $this->getFromCustomData(self::SCHEDULE_START_DATE, null, parent::getScheduleStartDate());}
	public function setScheduleStartDate($v)	{$this->putInCustomData(self::SCHEDULE_START_DATE, $v);}
	
	public function getScheduleEndDate($format = 'Y-m-d H:i:s')	{return $this->getFromCustomData(self::SCHEDULE_END_DATE, null, parent::getScheduleEndDate());}
	public function setScheduleEndDate($v)	{$this->putInCustomData(self::SCHEDULE_END_DATE, $v);}
	
	public function getThumbnailUrl()	{return $this->getFromCustomData(self::THUMBNAIL_URL, null, parent::getThumbnailUrl());}
	public function setThumbnailUrl($v)	{$this->putInCustomData(self::THUMBNAIL_URL, $v);}
	
	public function getThumbnailSaved()	{return $this->getFromCustomData(self::THUMBNAIL_SAVED, null, parent::getThumbnailSaved());}
	public function setThumbnailSaved($v)	{$this->putInCustomData(self::THUMBNAIL_SAVED, $v);}
	
	public function getCreatorId()	{return $this->getFromCustomData(self::CREATOR_ID, null);}
	public function setCreatorId($v)	{$this->putInCustomData(self::CREATOR_ID, $v);}
	
	public function getEntitledUsersEdit()	{return $this->getFromCustomData(self::ENTITLED_USERS_EDIT, null);}
	public function setEntitledUsersEdit($v)	{$this->putInCustomData(self::ENTITLED_USERS_EDIT, $v);}
	
	public function getEntitledUsersPublish()	{return $this->getFromCustomData(self::ENTITLED_USERS_PUBLISH, null);}
	public function setEntitledUsersPublish($v)	{$this->putInCustomData(self::ENTITLED_USERS_PUBLISH, $v);}

	public function getOwnerId()	{return $this->getFromCustomData(self::OWNER_ID, null);}
	public function setOwnerId($v)	{$this->putInCustomData(self::OWNER_ID, $v);}

	public function getReferenceId()	{return $this->getFromCustomData(self::REFERENCE_ID, null);}
	public function setReferenceId($v)	{$this->putInCustomData(self::REFERENCE_ID, $v);}

	public function getTemplateEntryId()	{return $this->getFromCustomData(self::TEMPLATE_ENTRY_ID, null);}
	public function setTemplateEntryId($v)	{$this->putInCustomData(self::TEMPLATE_ENTRY_ID, $v);}
	
    public function getEntryStatus()	
    {
        return $this->getObjectStatus();
    }
    
	public function setEntryStatus($v)	
	{
	    $this->setObjectStatus($v);
	}
	
	
}