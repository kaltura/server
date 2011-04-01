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
 * @package Core
 * @subpackage model
 */
class DropFolder extends BaseDropFolder
{
	
	// ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
	
	const FILE_SIZE_CHECK_INTERVAL_COLUMN = 'file_size_check_interval';
	const FILE_HANDLERS_CONFIG_COLUMN     = 'file_handlers_config';
	const UNMATCHED_FILE_POLICY_COLUMN    = 'unmatched_file_policy';
	const FILE_DELETE_POLICY_COLUMN       = 'file_delete_policy';
	const AUTO_FILE_DELETE_DAYS_COLUMN  = 'auto_file_delete_days';
	
	
	// File size check interval - value in seconds
	
	public function getFileSizeCheckInterval()
	{
		return $this->getFromCustomData(self::FILE_SIZE_CHECK_INTERVAL_COLUMN);
	}
	
	public function setFileSizeCheckInterval($interval)
	{
		$this->putInCustomData(self::FILE_SIZE_CHECK_INTERVAL_COLUMN, $interval);
	}
	
	
	// File handlers configuration
	// The configuration is a key-value array where the key is from IngestionFileHandlerType and the value is the file handler configuration array
	
	public function getFileHandlersConfig()
	{
		$serializedConfig = $this->getFromCustomData(self::FILE_HANDLERS_CONFIG_COLUMN);
		$configArray = unserialize($serializedConfig);
		return $configArray;
	}
	
	public function setFileHandlersConfig($fileHandlersConfig)
	{
		$serializedConfig = serialize($fileHandlersConfig);
		$this->putInCustomData(self::FILE_HANDLERS_CONFIG_COLUMN, $serializedConfig);
	}
	
	
	// Unmatched file policy
	
	/**
	 * @return IngestionUnmatchedFilesPolicy
	 */
	public function getUnmatchedFilePolicy()
	{
		return $this->getFromCustomData(self::UNMATCHED_FILE_POLICY_COLUMN);
	}
	
	/**
	 * @param IngestionUnmatchedFilesPolicy $policy
	 */
	public function setUnmatchedFilePolicy($policy)
	{
		$this->putInCustomData(self::UNMATCHED_FILE_POLICY_COLUMN, $policy);
	}
	
	
	// File delete policy
	
	/**
	 * @return DropFolderFileDeletePolicy
	 */
	public function getFileDeletePolicy()
	{
		return $this->getFromCustomData(self::FILE_DELETE_POLICY_COLUMN);
	}
	
	/**
	 * @param DropFolderFileDeletePolicy $policy
	 */
	public function setFileDeletePolicy($policy)
	{
		$this->putInCustomData(self::FILE_DELETE_POLICY_COLUMN, $policy);
	}
	
	
	// Automatic file delete days
		
	public function getAutoFileDeleteDays()
	{
		return $this->getFromCustomData(self::AUTO_FILE_DELETE_DAYS_COLUMN);
	}
	
	public function setAutoFileDeleteDays($days)
	{
		$this->putInCustomData(self::AUTO_FILE_DELETE_DAYS_COLUMN, $days);
	}
	
	
} // DropFolder
