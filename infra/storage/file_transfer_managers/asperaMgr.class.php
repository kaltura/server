<?php
/**
 * 
 * @package infra
 * @subpackage Storage
 */
class asperaMgr extends kFileTransferMgr
{
	
	private $privKeyFile;
	private $pubKeyFile;
	private $passphrase;
	private $user;
	private $server;
	private $pass;
	private $port;
	
	const TEMP_DIRECTORY = 'aspera_upload';
	
	public function putFile($remote_file, $local_file){
		$remote_file = ltrim($remote_file,'/');
		$remoteFileName = basename ( $remote_file ) ;
		$remotePath = dirname ( $remote_file );
		$linkPath =  kConf::get('temp_folder') . '/' . self::TEMP_DIRECTORY . '/' .$remoteFileName;
		if (!file_exists(dirname( $linkPath )))
			mkdir(dirname( $linkPath ), 0750, true);
		symlink($local_file, $linkPath);
		$cmd= $this->getCmdPrefix();
		$cmd.=" $linkPath \"$this->user@$this->server:$remotePath\"";
		$res = $this->executeCmd($cmd);
		unlink($linkPath);
		return $res;
	}
	// upload a file to the server ising Aspera connection (ftp_mode is irrelevant)
	protected function doPutFile ($remote_file , $local_file)
	{
		return true;
	}
		
	// upload a file to the server ising Aspera connection (ftp_mode is irrelevant)
	public function getFile($remote_file, $local_file = null)
	{	
		$remote_file = ltrim($remote_file,'/');
		$cmd= $this->getCmdPrefix();
		$cmd.=" $this->user@$this->server:$remote_file $local_file";
		return $this->executeCmd($cmd);
	}
	
	private function getCmdPrefix(){
		$cmd = '';
		if ($this->privKeyFile){
			if ($this->passphrase)
				$cmd = "(echo $this->passphrase) | ascp ";
			else  
				$cmd = "ascp ";
		}
		else 
			$cmd = "(echo $this->pass) | ascp ";
		//creating folders on remote server
		$cmd.= " -d ";
		$cmd.=" -P $this->port ";
		if ($this->privKeyFile)
			$cmd.=" -i $this->privKeyFile ";
		return $cmd;
		
	}
	
	private function executeCmd($cmd){
		KalturaLog::debug('Executing command: '.$cmd);
		$return_value = null;
		$beginTime = time();
		system($cmd, $return_value);
		$duration = (time() - $beginTime)/1000;
		KalturaLog::debug("Execution took [$duration]sec with value [$return_value]");
		if ($return_value == 0)
			return true;
		return false;
	}
	
	public function login($server, $user, $pass, $port = null){
		$this->server = $server;
		$this->user = $user;
		$this->pass = $pass;
		$this->port = $port;
	}

	public function loginPubKey($server, $user, $pubKeyFile, $privKeyFile, $passphrase = null, $port = null){
		$this->server = $server;
		$this->user = $user;
		$this->privKeyFile = $privKeyFile;
		$this->pubKeyFile = $pubKeyFile;
		$this->passphrase = $passphrase;
		$this->port = $port;
	}
	
/* (non-PHPdoc)
	 * @see kFileTransferMgr::doConnect()
	 */
	protected function doConnect($server, &$port) {
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doLogin()
	 */
	protected function doLogin($user, $pass) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doLoginPubKey()
	 */
	protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doMkDir()
	 */
	protected function doMkDir($remote_path) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doDelFile()
	 */
	protected function doDelFile($remote_file) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doDelDir()
	 */
	protected function doDelDir($remote_path) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doChmod()
	 */
	protected function doChmod($remote_file, $chmod_code) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doFileExists()
	 */
	protected function doFileExists($remote_file) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doPwd()
	 */
	protected function doPwd() {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doList()
	 */
	protected function doList($remote_path) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see kFileTransferMgr::doFileSize()
	 */
	protected function doFileSize($remote_file) {
		// TODO Auto-generated method stub
		
	}
/* (non-PHPdoc)
	 * @see kFileTransferMgr::doGetFile()
	 */
	protected function doGetFile($remote_file, $local_file = null) {
		// TODO Auto-generated method stub
		
	}
}
