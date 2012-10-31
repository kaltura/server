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
	 * @var string
	 */   	
    private $filesPermissionPublicInS3;
	
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
	 * @return the $filesPermissionPublicInS3
	 */
	public function getfilesPermissionPublicInS3()
	{
		return $this->filesPermissionPublicInS3;
	}
	
	/**
	 * @param $filesPermissionPublicInS3 the $filesPermissionPublicInS3 to set
	 */
	public function setFilesPermissionPublicInS3 ($filesPermissionPublicInS3)
	{
		$this->filesPermissionPublicInS3 = $filesPermissionPublicInS3;	
	}
	
}
