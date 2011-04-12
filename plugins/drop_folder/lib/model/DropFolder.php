<?php


/**
 * Skeleton subclass for representing a row from the 'drop_folder' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.dropFolder
 * @subpackage model
 */
class DropFolder extends BaseDropFolder
{
	
	const AUTO_FILE_DELETE_DAYS_DEFAULT_VALUE = 3;
	const FILE_SIZE_CHECK_INTERNAL_DEFAULT_VALUE = '600'; // 600 seconds = 10 minutes
	
	
	// -------------------------------------
	// -- Default values -------------------
	// -------------------------------------
	
	/**
	 * Code to be run before inserting to database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preInsert(PropelPDO $con = null)
	{
    	$ret = parent::preInsert($con);
    	
		// set default values where null		
		if (is_null($this->getFileSizeCheckInterval())) {
			$this->setFileSizeCheckInterval(DropFolder::FILE_SIZE_CHECK_INTERNAL_DEFAULT_VALUE);
		}
		
		if (is_null($this->getUnmatchedFilePolicy())) {
			$this->setUnmatchedFilePolicy(DropFolderUnmatchedFilesPolicy::ADD_AS_ENTRY);
		}
		
		if (is_null($this->getFileDeletePolicy())) {
			$this->setFileDeletePolicy(DropFolderFileDeletePolicy::MANUAL_DELETE);
		}
		
		if (is_null($this->getAutoFileDeleteDays())) {
			$this->setAutoFileDeleteDays(DropFolder::AUTO_FILE_DELETE_DAYS_DEFAULT_VALUE);
		}    	
    	
		return $ret;
	}
	
	
	
	
	
	
	
	
	// ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
	
	const CUSTOM_DATA_FILE_SIZE_CHECK_INTERVAL = 'file_size_check_interval';
	const CUSTOM_DATA_FILE_HANDLERS_CONFIG     = 'file_handlers_config';
	const CUSTOM_DATA_AUTO_FILE_DELETE_DAYS  = 'auto_file_delete_days';
	
	
	// File size check interval - value in seconds
	
	/**
	 * @return int
	 */
	public function getFileSizeCheckInterval()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FILE_SIZE_CHECK_INTERVAL);
	}
	
	public function setFileSizeCheckInterval(int $interval)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FILE_SIZE_CHECK_INTERVAL, $interval);
	}
	
	
	// File handlers configuration
	
	/**
	 * @return array of FileHandlerConfig objects
	 */
	public function getFileHandlersConfig()
	{
		$serializedConfig = $this->getFromCustomData(self::CUSTOM_DATA_FILE_HANDLERS_CONFIG);
		try {
			$configArray = unserialize($serializedConfig);
		}
		catch (Exception $e) {
			$configArray = array();
		}
		return $configArray;
	}
	
	/**
	 * @param array $fileHandlersConfig array of FileHandlersConfig objects
	 */
	public function setFileHandlersConfig(array $fileHandlersConfig)
	{
		$serializedConfig = serialize($fileHandlersConfig);
		$this->putInCustomData(self::CUSTOM_DATA_FILE_HANDLERS_CONFIG, $serializedConfig);
	}	
	
	
	// Automatic file delete days
		
	/**
	 * @return int
	 */
	public function getAutoFileDeleteDays()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_AUTO_FILE_DELETE_DAYS);
	}
	
	public function setAutoFileDeleteDays(int $days)
	{
		$this->putInCustomData(self::CUSTOM_DATA_AUTO_FILE_DELETE_DAYS, $days);
	}
	
	
	
} // DropFolder
