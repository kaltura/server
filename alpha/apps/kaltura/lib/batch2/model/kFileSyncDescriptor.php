<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kFileSyncDescriptor 
{
	/**
	 * @var string
	 */
	private $fileSyncLocalPath;

	/**
	 * @var string
	 */
	private $fileEncryptionKey;
	
	/**
	 * @var string
	 */
	private $fileSyncRemoteUrl;
	
	/**
	 * 
	 * @var int
	 */
	private $fileSyncObjectSubType;

	/**
	 * @return the $fileSyncLocalPath
	 */
	public function getFileSyncLocalPath() 
	{
		return $this->fileSyncLocalPath;
	}

	/**
	 * @return string $fileEncryptionKey
	 */
	public function getFileEncryptionKey()
	{
		return $this->fileEncryptionKey;
	}

	/**
	 * @return the $fileSyncRemoteUrl
	 */
	public function getFileSyncRemoteUrl() 
	{
		return $this->fileSyncRemoteUrl;
	}

	/**
	 * @param string $fileSyncLocalPath
	 */
	public function setFileSyncLocalPath($fileSyncLocalPath) 
	{
		$this->fileSyncLocalPath = $fileSyncLocalPath;
	}

	/**
	 * @param string $fileEncryptionKey
	 */
	public function setFileEncryptionKey($fileEncryptionKey)
	{
		$this->fileEncryptionKey = $fileEncryptionKey;
	}

	/**
	 * @param string $fileSyncRemoteUrl
	 */
	public function setFileSyncRemoteUrl($fileSyncRemoteUrl) 
	{
		$this->fileSyncRemoteUrl = $fileSyncRemoteUrl;
	}
		
	/**
	 * @return the $fileSyncObjectSubType
	 */
	public function getFileSyncObjectSubType() {
		return $this->fileSyncObjectSubType;
	}
	
	/**
	 * @param int $fileSyncObjectSubType
	 */
	public function setFileSyncObjectSubType($fileSyncObjectSubType) {
		$this->fileSyncObjectSubType = $fileSyncObjectSubType;
	}	
	
	public function setPathAndKeyByFileSync(FileSync $fileSync)
	{
		$this->fileSyncLocalPath = $fileSync->getFullPath();
		if ($fileSync->isEncrypted())
			$this->fileEncryptionKey = $fileSync->getEncryptionKey();
	}
}