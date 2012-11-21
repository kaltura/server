<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAmazonS3StorageExportJobData extends kStorageExportJobData  
{
	 /**
	 * @var KalturaAmazonS3StorageProfileFilesPermissionLevel
	 */   	
    private $filesPermissionInS3;
    
	public function setStorageExportJobData(StorageProfile $externalStorage, FileSync $fileSync, $srcFileSyncLocalPath, $force = false)
	{
		parent::setStorageExportJobData($externalStorage, $fileSync, $srcFileSyncLocalPath);
		$this->setFilesPermissionInS3($externalStorage->getFilesPermissionInS3());
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
	public function setFilesPermissionInS3($filesPermissionInS3)
	{
		$this->filesPermissionInS3 = $filesPermissionInS3;	
	}	
}