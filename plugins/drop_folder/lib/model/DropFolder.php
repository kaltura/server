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
	
	// ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
	
	const CUSTOM_DATA_FILE_SIZE_CHECK_INTERVAL = 'file_size_check_interval';
	const CUSTOM_DATA_FILE_HANDLERS_CONFIG     = 'file_handlers_config';
	const CUSTOM_DATA_AUTO_FILE_DELETE_DAYS  = 'auto_file_delete_days';
	
	
	// File size check interval - value in seconds
	
	public function getFileSizeCheckInterval()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FILE_SIZE_CHECK_INTERVAL);
	}
	
	public function setFileSizeCheckInterval($interval)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FILE_SIZE_CHECK_INTERVAL, $interval);
	}
	
	
	// File handlers configuration
	// The configuration is a key-value array where the key is from IngestionFileHandlerType and the value is the file handler configuration array
	
	public function getFileHandlersConfig()
	{
		$serializedConfig = $this->getFromCustomData(self::CUSTOM_DATA_FILE_HANDLERS_CONFIG);
		$configArray = unserialize($serializedConfig);
		return $configArray;
	}
	
	public function setFileHandlersConfig($fileHandlersConfig)
	{
		$serializedConfig = serialize($fileHandlersConfig);
		$this->putInCustomData(self::CUSTOM_DATA_FILE_HANDLERS_CONFIG, $serializedConfig);
	}	
	
	
	// Automatic file delete days
		
	public function getAutoFileDeleteDays()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_AUTO_FILE_DELETE_DAYS);
	}
	
	public function setAutoFileDeleteDays($days)
	{
		$this->putInCustomData(self::CUSTOM_DATA_AUTO_FILE_DELETE_DAYS, $days);
	}
	
	
} // DropFolder
