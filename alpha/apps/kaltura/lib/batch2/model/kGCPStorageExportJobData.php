<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kGCPStorageExportJobData extends kStorageExportJobData
{
	 /**
	 * @var KalturaGCPStorageProfileFilesPermissionLevel
	 */   	
    private $filesPermissionInGCP;
    
	 /**
	 * @var string
	 */   	
    private $bucketName;
	
	/**
	* @var string
	*/
	private $keyFile;


	public function setStorageExportJobData(StorageProfile $externalStorage, FileSync $fileSync, FileSync $srcFileSync, $force = false)
	{
		$this->setSrcFileSyncLocalPath($srcFileSync->getFullPath());
		$this->setSrcFileEncryptionKey($srcFileSync->getEncryptionKey());
		$this->setSrcFileSyncId($fileSync->getId());
		$this->setForce($force);
		$this->setDestFileSyncStoredPath($externalStorage->getStorageBaseDir() . '/' . $fileSync->getFilePath());
		$this->setCreateLink($externalStorage->getCreateFileLink());
		$this->setFilesPermissionInGCP($externalStorage->getFilesPermissionInGCP());
		$this->setBucketName($externalStorage->getBucketName());
		$this->setKeyFile($externalStorage->getKeyFile());
	}

	/**
	 * @return KalturaGCPStorageProfileFilesPermissionLevel
	 */
	public function getFilesPermissionInGCP()
	{
		return $this->filesPermissionInGCP;
	}
	
	/**
	 * @param $filesPermissionInGCP the $filesPermissionInGCP to set
	 */
	public function setFilesPermissionInGCP($filesPermissionInGCP)
	{
		$this->filesPermissionInGCP = $filesPermissionInGCP;
	}

	/**
	 * @return string
	 */
	public function getBucketName()
	{
		return $this->bucketName;
	}
	
	/**
	 * @param $bucketName the $bucketName to set
	 */
	public function setBucketName($bucketName)
	{
		$this->bucketName = $bucketName;
	}	
	
	/**
	 * @return string
	 */
	public function getKeyFile()
	{
		return $this->keyFile;
	}
	
	/**
	 * @param $keyFile the $keyFile to set
	 */
	public function setKeyFile($keyFile)
	{
		$this->keyFile = $keyFile;
	}	
	
}