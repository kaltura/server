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
	const CUSTOM_DATA_APPROVED_BY = 		'approved_by';
	
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
	
	public function setApprovedBy($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_APPROVED_BY, $v);
	}
	
	public function setUserId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_USER_ID, $v);
	}
	
	//getters
	
	public function getNotes($v)
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_NOTES, null, null);
	}
	
	public function getAccessKey($v)
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ACCESS_KEY, null, null);
	}
	
	public function getErrDescription($v)
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ERR_DESCRIPTION, null, null);
	}
	
	public function getApprovedBy($v)
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_APPROVED_BY, null, null);
	}
	
	public function getUserId($v)
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
	
} // EntryVendorTask
