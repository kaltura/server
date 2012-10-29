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
	
	const CUSTOM_DATA_FILES_PERMISSION_PUBLIC_IN_S3 = 'files_permission_public_in_s3';
	
	public function getKalturaObjectType()
	{
		return 'KalturaAmazonS3StorageProfile';
	}
	
	/* Files Permission Public */
	
	public function setFilesPermissionPublicInS3($v)
	{
		if (!is_null($v)){
	    	$this->putInCustomData(self::CUSTOM_DATA_FILES_PERMISSION_PUBLIC_IN_S3, $v);
		}
	}
	
	public function getFilesPermissionPublicInS3()
	{
	    $v = $this->getFromCustomData(self::CUSTOM_DATA_FILES_PERMISSION_PUBLIC_IN_S3);
	    return $v;
	}
}