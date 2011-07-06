<?php

/**
* @package plugins.dropFolder
* @subpackage model
*/
class FtpDropFolder extends DropFolder
{
    
    // ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
    
    const CUSTOM_DATA_FTP_HOST = 'ftp_host';
    const CUSTOM_DATA_FTP_PORT = 'ftp_port';
    const CUSTOM_DATA_FTP_USERNAME = 'ftp_username';
    const CUSTOM_DATA_FTP_PASSWORD = 'ftp_password';
    const CUSTOM_DATA_FTP_FOLDER_PATH = 'ftp_folder_path';
    
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
	
	/**
	 * @return string
	 */
	public function getFtpFolderPath()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FTP_FOLDER_PATH);
	}
	
	/**
	 * @param string $ftpFolderPath
	 */
	public function setFtpFolderPath($ftpFolderPath)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FTP_FOLDER_PATH, $ftpFolderPath);
	}
    
    
    // ------------------------------------------
	// -- File Transfer Manager -----------------
	// ------------------------------------------
    
    /**
	 * @return kFileTransferMgr
	 */
	public function getFileTransferManager()
	{
	    return kFileTransferMgr::getInstance(kFileTransferMgrType::FTP);
	}
    
}