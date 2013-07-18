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
		$data = null;
		switch($protocol)
		{
			case StorageProfile::STORAGE_PROTOCOL_S3:
				$data = new kAmazonS3StorageExportJobData();
			default:
				$data = KalturaPluginManager::loadObject('kStorageExportJobData', $protocol);
		}
		if (!$data)
			$data = new kStorageExportJobData();
		
		return $data;
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
	
	function calculateEstimatedEffort(BatchJob $batchJob) {
		$fileSize = filesize($this->getSrcFileSyncLocalPath());
		if($fileSize !== False)
			return $fileSize;
		
		return self::MAX_ESTIMATED_EFFORT;
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
