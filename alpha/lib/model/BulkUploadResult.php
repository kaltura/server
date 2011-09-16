<?php

/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class BulkUploadResult extends BaseBulkUploadResult
{
    const CUSTOM_DATA_SSH_PRIVATE_KEY = 'sshPrivateKey';
    const CUSTOM_DATA_SSH_PUBLIC_KEY = 'sshPublicKey';
    const CUSTOM_DATA_SSH_KEY_PASSPHRASE = 'sshKeyPassphrase';
        
	private $aEntry = null;
	
	public function getEntry()
	{
		if($this->aEntry == null && $this->getEntryId())
		{
			$this->aEntry = entryPeer::retrieveByPKNoFilter($this->getEntryId());
		}
		return $this->aEntry;
	}
	
	public function updateStatusFromEntry()
	{
		$entry = $this->getEntry();
		if(!$entry)
			return;
			
		$this->setEntryStatus($entry->getStatus());
		$this->save();
		
		return $this->getEntryStatus();
	}
	
	public function getEntryId()
	{
		if($this->getObjectType() == BulkUploadResultObjectType::ENTRY)
			return $this->getObjectId();
			
		return null;
	}
	
	public function setEntryId($v)
	{
		$this->setObjectType(BulkUploadResultObjectType::ENTRY);
		return $this->setObjectId($v);
	}
	

	/**
	 * Get the [plugins_data] column value.
	 * 
	 * @return     string
	 */
	public function getPluginsData()
	{
		$plugins_data = parent::getPluginsData();
		if(!$plugins_data)
			return array();
			
		return json_decode($plugins_data);
	}

	/**
	 * Set the value of [plugins_data] column.
	 * 
	 * @param      string $v new value
	 * @return     BulkUploadResult The current object (for fluent API support)
	 */
	public function setPluginsData($v)
	{
		if(!is_array($v))
			$v = array();
			
		return parent::setPluginsData(json_encode($v));
	} // setPluginsData()
	
	
	public function getSshPrivateKey()		{return $this->getFromCustomData(self::CUSTOM_DATA_SSH_PRIVATE_KEY);}
	public function setSshPrivateKey($v)	{$this->putInCustomData(self::CUSTOM_DATA_SSH_PRIVATE_KEY, $v);}
	
    public function getSshPublicKey()		{return $this->getFromCustomData(self::CUSTOM_DATA_SSH_PUBLIC_KEY);}
	public function setSshPublicKey($v)	    {$this->putInCustomData(self::CUSTOM_DATA_SSH_PUBLIC_KEY, $v);}
	
    public function getSshKeyPassphrase()	{return $this->getFromCustomData(self::CUSTOM_DATA_SSH_KEY_PASSPHRASE);}
	public function setSshKeyPassphrase($v)	{$this->putInCustomData(self::CUSTOM_DATA_SSH_KEY_PASSPHRASE, $v);}
	
}
