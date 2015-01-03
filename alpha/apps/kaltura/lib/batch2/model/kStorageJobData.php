<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kStorageJobData extends kJobData
{
	/**
	 * @var string
	 */   	
    private $serverUrl; 

	/**
	 * @var string
	 */   	
    private $serverUsername; 

	/**
	 * @var string
	 */   	
    private $serverPassword;
    
    /**
     * @var string
     */
    private $serverPrivateKey;
    
    /**
     * @var string
     */
    private $serverPublicKey;
    
    /**
     * @var string
     */
    private $serverPassPhrase;

	/**
	 * @var bool
	 */   	
    private $ftpPassiveMode;

	/**
	 * @var string
	 */   	
    private $srcFileSyncLocalPath;

	/**
	 * @var string
	 */   
	private $srcFileSyncId;
	
	/**
	 * @var string
	 */   	
    private $destFileSyncStoredPath;
    
	/**
	 * @return the $serverUrl
	 */
	public function getServerUrl()
	{
		return $this->serverUrl;
	}

	/**
	 * @return the $serverUsername
	 */
	public function getServerUsername()
	{
		return $this->serverUsername;
	}

	/**
	 * @return the $serverPassword
	 */
	public function getServerPassword()
	{
		return $this->serverPassword;
	}

	/**
	 * @return the $ftpPassiveMode
	 */
	public function getFtpPassiveMode()
	{
		return $this->ftpPassiveMode;
	}

	/**
	 * @return the $srcFileSyncLocalPath
	 */
	public function getSrcFileSyncLocalPath()
	{
		return $this->srcFileSyncLocalPath;
	}

	/**
	 * @return the $srcFileSyncId
	 */
	public function getSrcFileSyncId()
	{
		return $this->srcFileSyncId;
	}

	/**
	 * @param $serverUrl the $serverUrl to set
	 */
	public function setServerUrl($serverUrl)
	{
		$this->serverUrl = $serverUrl;
	}

	/**
	 * @param $serverUsername the $serverUsername to set
	 */
	public function setServerUsername($serverUsername)
	{
		$this->serverUsername = $serverUsername;
	}

	/**
	 * @param $serverPassword the $serverPassword to set
	 */
	public function setServerPassword($serverPassword)
	{
		$this->serverPassword = $serverPassword;
	}

	/**
	 * @param $ftpPassiveMode the $ftpPassiveMode to set
	 */
	public function setFtpPassiveMode($ftpPassiveMode)
	{
		$this->ftpPassiveMode = $ftpPassiveMode;
	}

	/**
	 * @param $srcFileSyncLocalPath the $srcFileSyncLocalPath to set
	 */
	public function setSrcFileSyncLocalPath($srcFileSyncLocalPath)
	{
		$this->srcFileSyncLocalPath = $srcFileSyncLocalPath;
	}

	/**
	 * @param $srcFileSyncId the $srcFileSyncId to set
	 */
	public function setSrcFileSyncId($srcFileSyncId)
	{
		$this->srcFileSyncId = $srcFileSyncId;
	}
	
	/**
	 * @return the $destFileSyncStoredPath
	 */
	public function getDestFileSyncStoredPath()
	{
		return $this->destFileSyncStoredPath;
	}

	/**
	 * @param $destFileSyncStoredPath the $destFileSyncStoredPath to set
	 */
	public function setDestFileSyncStoredPath($destFileSyncStoredPath)
	{
		$this->destFileSyncStoredPath = $destFileSyncStoredPath;
	}
	
	/**
	 * @return the $serverPrivateKey
	 */
	public function getServerPrivateKey() {
		return $this->serverPrivateKey;
	}

	/**
	 * @return the $serverPublicKey
	 */
	public function getServerPublicKey() {
		return $this->serverPublicKey;
	}

	/**
	 * @return the $serverPassPhrase
	 */
	public function getServerPassPhrase() {
		return $this->serverPassPhrase;
	}

	/**
	 * @param string $serverPrivateKey
	 */
	public function setServerPrivateKey($serverPrivateKey) {
		$this->serverPrivateKey = $serverPrivateKey;
	}

	/**
	 * @param string $serverPublicKey
	 */
	public function setServerPublicKey($serverPublicKey) {
		$this->serverPublicKey = $serverPublicKey;
	}

	/**
	 * @param string $serverPassPhrase
	 */
	public function setServerPassPhrase($serverPassPhrase) {
		$this->serverPassPhrase = $serverPassPhrase;
	}
	
}
