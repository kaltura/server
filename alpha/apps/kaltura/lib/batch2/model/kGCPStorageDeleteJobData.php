<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kGCPStorageDeleteJobData extends kStorageDeleteJobData
{
	 /**
	 * @var string
	 */   	
    private $bucketName;
	
	/**
	* @var string
	*/
	private $keyFile;
	
	/**
	 * @var StorageProfile $storage
	 * @var FileSync $fileSync
	 */
	public function setJobData (StorageProfile $storage, FileSync $filesync)
	{
		$this->setBucketName($storage->getBucketName());
		$this->setKeyFile($storage->getKeyFile());
		$this->setSrcFileSyncId($filesync->getId());
		$this->setDestFileSyncStoredPath($storage->getStorageBaseDir() . '/' . $filesync->getFilePath());
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
	 * @return the $keyFile
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