<?php

/**
 * Subclass for representing a row from the 'storage_profile' table.
 *
 * @package Core
 * @subpackage model
 */ 

class GCPStorageProfile extends StorageProfile
{
	
	const CUSTOM_DATA_FILES_PERMISSION_IN_GCP = 'files_permission_in_gcp';
	const CUSTOM_DATA_BUCKET_NAME = 'bucket_name';
	const CUSTOM_DATA_KEY_FILE = 'key_file';

	public function getKalturaObjectType()
	{
		return 'KalturaGCPStorageProfile';
	}
	
	/**
	 * Files Permission Public
	 * @param $v
	 */
	public function setFilesPermissionInGCP($v)
	{
		if (!is_null($v))
		{
	    	$this->putInCustomData(self::CUSTOM_DATA_FILES_PERMISSION_IN_GCP, $v);
		}
	}

	/**
	 * @return string
	 */
	public function getFilesPermissionInGCP()
	{
	    $v = $this->getFromCustomData(self::CUSTOM_DATA_FILES_PERMISSION_IN_GCP);
	    return $v;
	}

	/**
	 * @param $v
	 */
	public function setBucketName($v)
	{
		if (!is_null($v))
		{
	    	$this->putInCustomData(self::CUSTOM_DATA_BUCKET_NAME, $v);
		}
	}

	/**
	 * @return string
	 */
	public function getBucketName()
	{
	    $v = $this->getFromCustomData(self::CUSTOM_DATA_BUCKET_NAME);
	    return $v;
	}

	/**
	 * @param $v
	 */
	public function setKeyFile($v)
	{
		if (!is_null($v))
		{
	    	$this->putInCustomData(self::CUSTOM_DATA_KEY_FILE, $v);
		}
	}

	/**
	 * @return string
	 */
	public function getKeyFile()
	{
	    $v = $this->getFromCustomData(self::CUSTOM_DATA_KEY_FILE);
	    return $v;
	}
	
}