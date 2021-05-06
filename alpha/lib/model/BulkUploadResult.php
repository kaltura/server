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
	const ROW_DATA_EXTENSION = 'row_data_extension';
	const ROW_DATA_MAX_LENGTH = 1023;

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

	public function setRowData($v)
	{
		$this->setRowDataExtension( substr($v, self::ROW_DATA_MAX_LENGTH) );
		return parent::setRowData($v);
	}

	public function getRowData()
	{
		return parent::getRowData() . $this->getRowDataExtension();
	}

	protected function getRowDataExtension()    {return $this->getFromCustomData(self::ROW_DATA_EXTENSION, null, '');}
	protected function setRowDataExtension($v)  {$this->putInCustomData(self::ROW_DATA_EXTENSION, ($v === false) ? '' : $v);}
}
