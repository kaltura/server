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
    
	public static function getInstance($protocol)
	{
		switch($protocol)
		{
			case StorageProfile::STORAGE_PROTOCOL_S3:
				return new kAmazonS3StorageExportJobData();
			default:
				return new kStorageExportJobData();
		}
	}
	
	public function setStorageExportJobData(StorageProfile $externalStorage, FileSync $fileSync, $srcFileSyncLocalPath, $force = false)
	{
		$this->setServerUrl($externalStorage->getStorageUrl()); 
	    $this->setServerUsername($externalStorage->getStorageUsername()); 
	    $this->setServerPassword($externalStorage->getStoragePassword());
	    $this->setFtpPassiveMode($externalStorage->getStorageFtpPassiveMode());
	    $this->setSrcFileSyncLocalPath($srcFileSyncLocalPath);
		$this->setSrcFileSyncId($fileSync->getId());
		$this->setForce($force);
		$this->setDestFileSyncStoredPath($externalStorage->getStorageBaseDir() . '/' . $fileSync->getFilePath());
	}
        
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
	
	
}
