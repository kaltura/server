<?php

/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package lib.model
 */ 
class BulkUploadResult extends BaseBulkUploadResult
{
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
}
