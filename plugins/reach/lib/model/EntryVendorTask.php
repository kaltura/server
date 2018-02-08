<?php


/**
 * Skeleton subclass for representing a row from the 'entry_vendor_task' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.reach
 * @subpackage model
 */
class EntryVendorTask extends BaseEntryVendorTask implements IRelatedObject
{
	const CUSTOM_DATA_NOTES = 				'notes';
	const CUSTOM_DATA_ACCESS_KEY = 			'access_key';
	const CUSTOM_DATA_ERR_DESCRIPTION = 	'err_description';
	const CUSTOM_DATA_USER_ID = 			'user_id';
	const CUSTOM_DATA_MODERATING_USER = 	'moderating_user';
	
	//setters
	
	public function setNotes($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_NOTES, $v);
	}
	
	public function setAccessKey($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ACCESS_KEY, $v);
	}
	
	public function setErrDescription($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ERR_DESCRIPTION, $v);
	}
	
	public function setModeratingUser($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_MODERATING_USER, $v);
	}
	
	public function setUserId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_USER_ID, $v);
	}
	
	//getters
	
	public function getNotes()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_NOTES, null, null);
	}
	
	public function getAccessKey()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ACCESS_KEY, null, null);
	}
	
	public function getErrDescription()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ERR_DESCRIPTION, null, null);
	}
	
	public function getModeratingUser()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_MODERATING_USER, null, null);
	}
	
	public function getUserId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_USER_ID, null, null);
	}
	
	public function getVendorProfile()
	{
		return VendorProfilePeer::retrieveByPK($this->getVendorProfileId());
		
	}
	
	public function getEntry()
	{
		return entryPeer::retrieveByPK($this->getEntryId());
	}
	
	public function getCatalogItem()
	{
		return VendorCatalogItemPeer::retrieveByPK($this->getCatalogItemId());
	}
	
	/* (non-PHPdoc)
 	 * @see BaseEntryVendorTask::preUpdate()
 	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if ($this->isColumnModified(EntryVendorTaskPeer::STATUS) && $this->getStatus() == EntryVendorTaskStatus::PROCESSING)
		{
			$this->setQueueTime(time());
		}
		
		if ($this->isColumnModified(EntryVendorTaskPeer::STATUS) && in_array($this->getStatus(), array(EntryVendorTaskStatus::READY, EntryVendorTaskStatus::ERROR)))
		{
			$this->setFinishTime(time());
		}
		
		return parent::preUpdate($con);
	}
	
	
} // EntryVendorTask
