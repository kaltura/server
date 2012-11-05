<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kStorageExportJobData extends kStorageJobData
{
	/**
	 * @var bool
	 */   	
    private $force; 
        
    /**
	 * @var KalturaAmazonS3StorageProfileFilesPermissionLevel
	 */   	
    private $filesPermissionInS3;
	
	/**
	 * @return the $force
	 */
	public function getForce()
	{
		return $this->force;
	}

	/**
	 * @param $force the $force to set
	 */
	public function setForce($force)
	{
		$this->force = $force;
	}
	
	/**
	 * @return the $filesPermissionInS3
	 */
	public function getFilesPermissionInS3()
	{
		return $this->filesPermissionInS3;
	}
	
	/**
	 * @param $filesPermissionInS3 the $filesPermissionInS3 to set
	 */
	public function setFilesPermissionInS3 ($filesPermissionInS3)
	{
		$this->filesPermissionInS3 = $filesPermissionInS3;	
	}
	
}
