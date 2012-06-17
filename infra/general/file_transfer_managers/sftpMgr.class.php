<?php
/**
 * Extends the 'kFileTransferMgr' class & implements a file transfer manager using the SFTP protocol.
 * For additional comments please look at the 'kFileTransferMgr' class.
 * 
 * @package infra
 * @subpackage Storage
 */
class sftpMgr extends kFileTransferMgr
{
	/**
	 * @var Net_SFTP
	 */
	private $sftp = null;
	
	/**
	 * Instances of this class should be created usign the 'getInstance' of the 'kFileTransferMgr' class
	 * 
	 * @see kFileTransferMgr::getInstance()
	 */
	protected function __construct()
	{
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::getConnection()
	 */
	public function getConnection()
	{
		if(!$this->sftp)
			return null;
		
		return $this->sftp->session_id;
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doConnect()
	 * 
	 * sftp connect to server:port
	 */
	protected function doConnect($sftp_server, &$sftp_port)
	{
		$this->sftp = new Net_SFTP($sftp_server, $sftp_port);
		return $this->sftp->fsock;
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doLogin()
	 * 
	 * Login to an existing connection with given user/pass 
	 * ftp_passive_mode is irrelevant
	 */
	protected function doLogin($sftp_user, $sftp_pass, $ftp_passive_mode = true)
	{
		return $this->sftp->login($sftp_user, $sftp_pass);
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doLoginPubKey()
	 * 
	 * Login using a public key
	 */
	protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null)
	{
		$crypt = new Crypt_RSA();
		$crypt->setPublicKey(file_get_contents($pubKeyFile));
		$crypt->loadKey(file_get_contents($privKeyFile));
		return $this->sftp->login($user, $crypt);
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doPutFile()
	 * 
	 * Upload a file to the server
	 * ftp_mode is irrelevant
	 */
	protected function doPutFile($remote_file, $local_file, $ftp_mode, $http_field_name = null, $http_file_name = null)
	{
		return $this->sftp->put($remote_file, $local_file);
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doGetFile()
	 * 
	 * Download a file from the server
	 * ftp_mode is irrelevant
	 */
	protected function doGetFile($remote_file, $local_file, $ftp_mode)
	{
		return $this->sftp->get($remote_file, $local_file);
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doMkDir()
	 * 
	 * Create a new directory
	 */
	protected function doMkDir($remote_path)
	{
		return $this->sftp->mkdir($remote_path);
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doChmod()
	 * 
	 * Change permissions of the given remote file
	 */
	protected function doChmod($remote_file, $chmod_code)
	{
		return $this->sftp->chmod($chmod_code, $remote_file);
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doFileExists()
	 * 
	 * Return true/false according to existence of file on the server
	 */
	protected function doFileExists($remote_file)
	{
		return is_array($this->sftp->stat($remote_file));
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doPwd()
	 * 
	 * Return the current working directory
	 */
	protected function doPwd()
	{
		return $this->sftp->pwd();
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doDelFile()
	 * 
	 * Delete a file and return true/false according to success
	 */
	protected function doDelFile($remote_file)
	{
		return $this->sftp->delete($remote_file);
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doDelDir()
	 * 
	 * Delete a directory and return true/false according to success
	 */
	protected function doDelDir($remote_path)
	{
		return $this->sftp->rmdir($remote_path);
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doList()
	 */
	protected function doList($remote_path)
	{
		return $this->sftp->nlist($remote_path);
	}
	
	/**
	 * Download a file from the server
	 * 
	 * @param string $remote_file
	 * @return string
	 */
	public function fileGetContents($remote_file)
	{
		return $this->sftp->get($remote_file);
	}
	
	/**
	 * Upload a file to the server
	 * 
	 * @param string $remote_file
	 * @param string $contents
	 */
	public function filePutContents($remote_file, $contents)
	{
		return $this->sftp->put($remote_file, $contents);
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::doFileSize()
	 */
	protected function doFileSize($remote_file)
	{
		return $this->sftp->size($remote_file);
	}
	
	/**
	 * @param string $remote_file
	 * @return string
	 */
	protected function doModificationTime($remote_file)
	{
		$stat = $this->sftp->stat($remote_file);
		if(!$stat)
			return false;
			
		return $stat['mtime'];
	}
}
