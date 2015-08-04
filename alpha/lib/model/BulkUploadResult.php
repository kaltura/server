<?php

/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class BulkUploadResult extends BaseBulkUploadResult implements IBaseObject
{   
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
	
	
	/**
	 * Function to update the status of the current bulk upload result
	 * with dependence on its related object's status.
	 */
	public function updateStatusFromObject ()
	{
	    if ($this->getStatus() != BulkUploadResultStatus::ERROR)
	    {
    	    $this->setStatus(BulkUploadResultStatus::OK);
    	    $this->save();
	    }
	    
	    return $this->getStatus();
	}
	
	
	/**
	 * Function called after the bulk upload result is added
	 * to the database, in case the object it concerns needs to be updated also.
	 */
	public function handleRelatedObjects ()
	{
	    
	}
	
	
	/**
	 * Function to return the object related to the current bulk upload result.
	 */
	public function getObject ()
	{
	    
	}
	
}
