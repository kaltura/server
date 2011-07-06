<?php

abstract class SshDropFolder extends DropFolder
{
   // ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
    
    const CUSTOM_DATA_SSH_HOST = 'ssh_host';
    const CUSTOM_DATA_SSH_PORT = 'ssh_port';
    const CUSTOM_DATA_SSH_USERNAME = 'ssh_username';
    const CUSTOM_DATA_SSH_PASSWORD = 'ssh_password';
    const CUSTOM_DATA_SSH_PRIVATE_KEY = 'ssh_private_key';
    const CUSTOM_DATA_SSH_PUBLIC_KEY = 'ssh_public_key';
    const CUSTOM_DATA_SSH_FOLDER_PATH = 'ssh_folder_path';
    
	/**
	 * @return string
	 */
	public function getSshHost()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SSH_HOST);
	}
	
	/**
	 * @param string $sshHost
	 */
	public function setSshHost($sshHost)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SSH_HOST, $sshHost);
	}
	
	/**
	 * @return int
	 */
	public function getSshPort()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SSH_PORT);
	}
	
	/**
	 * @param int $sshPort
	 */
	public function setSshPort($sshPort)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SSH_PORT, $sshPort);
	}
	
	/**
	 * @return string
	 */
	public function getSshUsername()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SSH_USERNAME);
	}
	
	/**
	 * @param string $sshUsername
	 */
	public function setSshUsername($sshUsername)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SSH_USERNAME, $sshUsername);
	}
	
	/**
	 * @return string
	 */
	public function getSshPassword()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SSH_PASSWORD);
	}
	
	/**
	 * @param string $sshPassword
	 */
	public function setSshPassword($sshPassword)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SSH_PASSWORD, $sshPassword);
	}
	
	/**
	 * @return string
	 */
	public function getSshPrivateKey()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SSH_PRIVATE_KEY);
	}
	
	/**
	 * @param string $sshPrivateKey
	 */
	public function setSshPrivateKey($sshPrivateKey)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SSH_PRIVATE_KEY, $sshPrivateKey);
	}
	
	/**
	 * @return string
	 */
	public function getSshPublicKey()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SSH_PUBLIC_KEY);
	}
	
	/**
	 * @param string $sshPublicKey
	 */
	public function setSshPublicKey($sshPublicKey)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SSH_PUBLIC_KEY, $sshPublicKey);
	}
	
	/**
	 * @return string
	 */
	public function getSshFolderPath()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SSH_FOLDER_PATH);
	}
	
	/**
	 * @param string $sshFolderPath
	 */
	public function setSshFolderPath($sshFolderPath)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SSH_FOLDER_PATH, $sshFolderPath);
	}
    
}