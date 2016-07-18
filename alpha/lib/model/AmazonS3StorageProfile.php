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
	const CUSTOM_DATA_SSE_TYPE = 'sseType';
	const CUSTOM_DATA_SSE_KMS_KEY_ID = 'sseKmsKeyId';
	const CUSTOM_DATA_SIGNATURE_TYPE = 'signatureType';
	const CUSTOM_DATA_END_POINT = 'endPoint';
	
	public function getKalturaObjectType()
	{
		return 'KalturaAmazonS3StorageProfile';
	}
	
	/* Files Permission Public */
	
	public function setFilesPermissionInS3($v)
	{
		if (!is_null($v))
		{
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
		if (!is_null($v))
		{
	    	$this->putInCustomData(self::CUSTOM_DATA_S3_REGION, $v);
		}
	}
	
	public function getS3Region()
	{
	    $v = $this->getFromCustomData(self::CUSTOM_DATA_S3_REGION);
	    return $v;
	}
	
	public function setSseType($v)
	{
		if (!is_null($v))
		{
	    	$this->putInCustomData(self::CUSTOM_DATA_SSE_TYPE, $v);
		}
	}
	
	public function getSseType()
	{
	    $v = $this->getFromCustomData(self::CUSTOM_DATA_SSE_TYPE);
	    return $v;
	}
	
	public function setSseKmsKeyId($v)
	{
		if (!is_null($v))
		{
	    	$this->putInCustomData(self::CUSTOM_DATA_SSE_KMS_KEY_ID, $v);
		}
	}
	
	public function getSseKmsKeyId()
	{
	    $v = $this->getFromCustomData(self::CUSTOM_DATA_SSE_KMS_KEY_ID);
	    return $v;
	}
	
	public function setSignatureType($v)
	{
		if (!is_null($v))
		{
			$this->putInCustomData(self::CUSTOM_DATA_SIGNATURE_TYPE, $v);
		}
	}
	
	public function getSignatureType()
	{
		$v = $this->getFromCustomData(self::CUSTOM_DATA_SIGNATURE_TYPE);
		return $v;
	}
	
	public function setEndPoint($v)
	{
		if (!is_null($v))
		{
			$this->putInCustomData(self::CUSTOM_DATA_END_POINT, $v);
		}
	}
	
	public function getEndPoint()
	{
		$v = $this->getFromCustomData(self::CUSTOM_DATA_END_POINT);
		return $v;
	}

}