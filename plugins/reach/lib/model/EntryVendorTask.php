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
class EntryVendorTask extends BaseEntryVendorTask implements IRelatedObject, IIndexable
{
	const CUSTOM_DATA_NOTES = 				'notes';
	const CUSTOM_DATA_ACCESS_KEY = 			'access_key';
	const CUSTOM_DATA_ERR_DESCRIPTION = 	'err_description';
	const CUSTOM_DATA_USER_ID = 			'user_id';
	const CUSTOM_DATA_MODERATING_USER = 	'moderating_user';
	const CUSTOM_DATA_ACCURACY 	= 			'accuracy';
	const CUSTOM_DATA_OUTPUT_OBJECT_ID = 	'output_object_id';
	const CUSTOM_DATA_DICTIONARY =          'dictionary';
	const CUSTOM_DATA_PARTNER_DATA =          'partner_data';
	
	//setters
	
	public function setNotes($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_NOTES, $v);
	}

	public function setDictionary($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DICTIONARY, $v);
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
	
	public function setAccuracy($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ACCURACY, $v);
	}
	
	public function setOutputObjectId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_OUTPUT_OBJECT_ID, $v);
	}
	
	public function setPartnerData($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PARTNER_DATA, $v);
	}
	
	//getters

	public function getDictionary()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DICTIONARY, null, null);
	}

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
	
	public function getAccuracy()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ACCURACY, null, null);
	}
	
	public function getOutputObjectId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_OUTPUT_OBJECT_ID, null, null);
	}
	
	public function getPartnerData($v)
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PARTNER_DATA, null, null);
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
	
	public function getKuser()
	{
		return kuserPeer::retrieveByPk($this->kuser_id);
	}
	
	// IIndexable interface implantation 
	
	/**
	 * Is the id as used and know by the indexing server
	 * @return int
	 */
	public function getIntId()
	{
		return $this->getId();
	}
	
	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return parent::getEntryId();
	}
	
	/**
	 * This function returns the index object name (the one responsible for the sphinx mapping)
	 */
	public function getIndexObjectName() 
	{
		return "EntryVendorTaskIndex";
	}
	
	/**
	 * @param int $time
	 * @return IIndexable
	 */
	public function setUpdatedAt($time)
	{
		parent::setUpdatedAt($time);
		if(!in_array(entryPeer::UPDATED_AT, $this->modifiedColumns, false))
			$this->modifiedColumns[] = entryPeer::UPDATED_AT;
		
		return $this;
	}
	
	/**
	 * Index the object in the search engine
	 */
	public function indexToSearchIndex()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}
	
	public function postSave(PropelPDO $con = null)
	{
		parent::postSave($con);
		$this->indexToSearchIndex();
	}
	
} // EntryVendorTask
