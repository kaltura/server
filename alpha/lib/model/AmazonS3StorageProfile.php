<?php

/**
 * Subclass for representing a row from the 'storage_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 

class AmazonS3StorageProfile extends StorageProfile
{
	
	const CUSTOM_DATA_FILES_PERMISSION_IN_S3 = 'files_permission_in_s3';
	const CUSTOM_DATA_S3_REGION = 's3Region';
	
	public function getKalturaObjectType()
	{
		return 'KalturaAmazonS3StorageProfile';
	}
	
	/* Files Permission Public */
	
	public function setFilesPermissionInS3($v)
	{
		if (!is_null($v)){
	    	$this->putInCustomData(self::CUSTOM_DATA_FILES_PERMISSION_IN_S3, $v);
		}
	}
	
	public function getFilesPermissionInS3()
	{
	    $v = $this->getFromCustomData(self::CUSTOM_DATA_FILES_PERMISSION_IN_S3);
	    return $v;
	}

	public function setS3Region($v)
	{
		if (!is_null($v)){
	    	$this->putInCustomData(self::CUSTOM_DATA_S3_REGION, $v);
		}
	}
	
	public function getS3Region()
	{
	    $v = $this->getFromCustomData(self::CUSTOM_DATA_S3_REGION);
	    return $v;
	}
}