<?php

/**
* @package plugins.dropFolder
* @subpackage model
*/
class FtpDropFolder extends RemoteDropFolder
{
    
    // ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
    
    const CUSTOM_DATA_FTP_HOST = 'ftp_host';
    const CUSTOM_DATA_FTP_PORT = 'ftp_port';
    const CUSTOM_DATA_FTP_USERNAME = 'ftp_username';
    const CUSTOM_DATA_FTP_PASSWORD = 'ftp_password';
    
	/**
	 * @return string
	 */
	public function getFtpHost()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FTP_HOST);
	}
	
	/**
	 * @param string $ftpHost
	 */
	public function setFtpHost($ftpHost)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FTP_HOST, $ftpHost);
	}
	
	/**
	 * @return int
	 */
	public function getFtpPort()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PORT);
	}
	
	/**
	 * @param int $ftpPort
	 */
	public function setFtpPort($ftpPort)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FTP_PORT, $ftpPort);
	}
	
	/**
	 * @return string
	 */
	public function getFtpUsername()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FTP_USERNAME);
	}
	
	/**
	 * @param string $ftpUsername
	 */
	public function setFtpUsername($ftpUsername)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FTP_USERNAME, $ftpUsername);
	}
	
	/**
	 * @return string
	 */
	public function getFtpPassword()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PASSWORD);
	}
	
	/**
	 * @param string $ftpPassword
	 */
	public function setFtpPassword($ftpPassword)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FTP_PASSWORD, $ftpPassword);
	}
	
	// ------------------------------------------
	// -- File Transfer params-------------------
	// ------------------------------------------
	
	public function getFolderUrl()
	{
	    $url = 'ftp://';
	    if ($this->getFtpUsername()) {
	        $url .= $this->getFtpUsername();
	        if ($this->getFtpPassword()) {
	            $url .= ':'.$this->getFtpPassword();
	        }
	        $url .= '@';
	    }
	    $url .= $this->getFtpHost();
	    $url .= '/'.$this->getPath();
	    return $url;
	}
		
	/**
	 * @return kDropFolderImportJobData
	 */
	public function getImportJobData()
	{
	    $jobData = new kDropFolderImportJobData();
	    $jobData->setPrivateKey(null);
	    $jobData->setPublicKey(null);
	    $jobData->setPassPhrase(null);
	    return $jobData;	    
	}
	
	protected function getRemoteFileTransferMgrType()
	{
	    return kFileTransferMgrType::FTP;
	}
	
	public function loginByCredentialsType(kFileTransferMgr $fileTransferMgr)
	{
		return $fileTransferMgr->login($this->getFtpHost(), $this->getFtpUsername(), $this->getFtpPassword(), $this->getFtpPort());
	}
	
    
}