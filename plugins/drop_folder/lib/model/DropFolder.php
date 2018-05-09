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
class DropFolder extends BaseDropFolder implements IBaseObject
{
	
	const AUTO_FILE_DELETE_DAYS_DEFAULT_VALUE = 0;
	const FILE_SIZE_CHECK_INTERVAL_DEFAULT_VALUE = '600'; // 600 seconds = 10 minutes
	const FILE_NAME_PATTERNS_DEFAULT_VALUE = '*';
	const INCREMENTAL = 'incremental';
	const LAST_FILE_TIMESTAMP = 'last_file_timestamp'; 
	const METADATA_PROFILE_ID = 'metadata_profile_id';
	const CATEGORIES_METADATA_FIELD_NAME = 'categories_metadata_field_name';
	const ENFORCE_ENTITLEMENT = 'enforce_entitlement';
	const SHOULD_VALIDATE_KS = 'should_validate_ks';
	
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
			$this->setFileSizeCheckInterval(DropFolder::FILE_SIZE_CHECK_INTERVAL_DEFAULT_VALUE);
		}
				
		if (is_null($this->getFileDeletePolicy())) {
			$this->setFileDeletePolicy(DropFolderFileDeletePolicy::AUTO_DELETE);
		}
		
		if (is_null($this->getAutoFileDeleteDays())) {
			$this->setAutoFileDeleteDays(DropFolder::AUTO_FILE_DELETE_DAYS_DEFAULT_VALUE);
		}    	
    	
		return $ret;
	}

	
	// -------------------------------------
	// -- Override base methods ------------
	// -------------------------------------
	
	/**
	 * @return DropFolderFileHandlerConfig
	 */
	public function getFileHandlerConfig()
	{
		$serializedConfig = parent::getFileHandlerConfig();
		try {
			$config = @unserialize($serializedConfig);
		}
		catch (Exception $e) {
			KalturaLog::err('Error unserializing file handler config for drop folder id ['.$this->getId().']');
			$config = null;
		}
		if ($config instanceof DropFolderFileHandlerConfig) {
			return $config;
		}
		return null;
	}
	
	/**
	 * @param DropFolderFileHandlerConfig $fileHandlerConfig
	 */
	public function setFileHandlerConfig($fileHandlerConfig)
	{
		if ($fileHandlerConfig instanceof DropFolderFileHandlerConfig)
		{
			$serializedConfig = serialize($fileHandlerConfig);
			parent::setFileHandlerConfig($serializedConfig);
		}
		else
		{
			KalturaLog::err('Given input $fileHandlerConfig is not an instance of DropFolderFileHandlerConfig - ignoring');
		}
	}	
	
	
	// ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
	
	const CUSTOM_DATA_FILE_SIZE_CHECK_INTERVAL = 'file_size_check_interval';
	const CUSTOM_DATA_AUTO_FILE_DELETE_DAYS  = 'auto_file_delete_days';
	const CUSTOM_DATA_IGNORE_FILE_NAME_PATTERNS = 'ignore_file_name_patterns';
	const CUSTOM_DATA_LAST_ACCESSED_AT = 'last_accessed_at';
	
	
	// File size check interval - value in seconds
	
	/**
	 * @return int
	 */
	public function getFileSizeCheckInterval()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FILE_SIZE_CHECK_INTERVAL);
	}
	
	public function setFileSizeCheckInterval($interval)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FILE_SIZE_CHECK_INTERVAL, $interval);
	}
	
	

	
	// Automatic file delete days
		
	/**
	 * @return int
	 */
	public function getAutoFileDeleteDays()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_AUTO_FILE_DELETE_DAYS);
	}
	
	public function setAutoFileDeleteDays($days)
	{
		$this->putInCustomData(self::CUSTOM_DATA_AUTO_FILE_DELETE_DAYS, $days);
	}
		
	// Ignore file patterns
		
	/**
	 * @return int
	 */
	public function getIgnoreFileNamePatterns()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_IGNORE_FILE_NAME_PATTERNS);
	}
	
	public function setIgnoreFileNamePatterns($patterns)
	{
		$this->putInCustomData(self::CUSTOM_DATA_IGNORE_FILE_NAME_PATTERNS, $patterns);
	}
	
	// last accessed by watcher
		
	/**
	 * @return int
	 */
	public function getLastAccessedAt()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_LAST_ACCESSED_AT);
	}
	
	public function setLastAccessedAt($date)
	{
		$this->putInCustomData(self::CUSTOM_DATA_LAST_ACCESSED_AT, $date);
	}
	
	/**
	 * @return bool
	 */
	public function getIncremental()
	{
		return $this->getFromCustomData(self::INCREMENTAL);
	}
	
	/**
	 * @param bool $v
	 */
	public function setIncremental($v)
	{
		$this->putInCustomData(self::INCREMENTAL, $v);
	}
	
	/**
	 * @return int
	 */
	public function getLastFileTimestamp()
	{
		return $this->getFromCustomData(self::LAST_FILE_TIMESTAMP);
	}
	
	/**
	 * @param bool $v
	 */
	public function setLastFileTimestamp($v)
	{
		$this->putInCustomData(self::LAST_FILE_TIMESTAMP, $v);
	}
	
	/**
	 * @param int $v
	 */
	public function setMetadataProfileId ($v)
	{
		$this->putInCustomData(self::METADATA_PROFILE_ID, $v);
	}
	
	/**
	 * return int
	 */
	public function getMetadataProfileId ()
	{
		return $this->getFromCustomData(self::METADATA_PROFILE_ID);
	}
	
		/**
	 * return string
	 */
	public function getCategoriesMetadataFieldName ()
	{
		return $this->getFromCustomData(self::CATEGORIES_METADATA_FIELD_NAME);
	}
	
	/**
	 * @param string $v
	 */
	public function setCategoriesMetadataFieldName ($v)
	{
		$this->putInCustomData(self::CATEGORIES_METADATA_FIELD_NAME, $v);
	}
	
	/**
	 * return bool
	 */
	public function getEnforceEntitlement ()
	{
		return $this->getFromCustomData(self::ENFORCE_ENTITLEMENT);
	}
	
	/**
	 * @param bool $v
	 */
	public function setEnforceEntitlement ($v)
	{
		$this->putInCustomData(self::ENFORCE_ENTITLEMENT, $v);
	}
	
	/**
 	 * @return bool
	 */
	public function getShouldValidateKS()
	{
		return $this->getFromCustomData(self::SHOULD_VALIDATE_KS);
	}
	
	/**
	 * @param bool $v
	 */
	public function setShouldValidateKS($v)
	{
		$this->putInCustomData(self::SHOULD_VALIDATE_KS, $v);
	}
	
	public function getCacheInvalidationKeys()
	{
		return array("dropFolder:id=".strtolower($this->getId()), "dropFolder:dc=".strtolower($this->getDc()));
	}
	
	/**
	 * @return kFileTransferMgrType
	 */
	public function getFileTransferMgrType()
	{
		return kFileTransferMgrType::LOCAL;
	}

	/**
	 * Login using fileTransferMgr according to the available credentials
	 * @param kFileTransferMgr $fileTransferMgr
	 */
	public function loginByCredentialsType(kFileTransferMgr $fileTransferMgr)
	{
		return $fileTransferMgr->login(null, null, null);
	}

	/**
	 * get full local file path
	 * @param string $fileName
	 * @param int $fileId
	 * @param kFileTransferMgr $fileTransferMgr
	 */
	public function getLocalFilePath($fileName, $fileId, kFileTransferMgr $fileTransferMgr)
	{
		$dropFolderFilePath = $this->getPath().'/'.$fileName;
		return realpath($dropFolderFilePath);
	}	
} // DropFolder
