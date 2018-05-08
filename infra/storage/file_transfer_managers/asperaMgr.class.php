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
	private $ascpCmd = 'ascp';
	private $asperaTempFolder = null; 
	
	// instances of this class should be created usign the 'getInstance' of the 'kFileTransferMgr' class
	protected function __construct(array $options = null)
	{
		parent::__construct($options);
		
		if(!$options || !isset($options['asperaTempFolder']))
			throw new kFileTransferMgrException("Option attribute [asperaTempFolder] is missing.", kFileTransferMgrException::attributeMissing);
		$this->asperaTempFolder = $options['asperaTempFolder'];
		
		if(isset($options['ascpCmd']))
			$this->ascpCmd = $options['ascpCmd'];
	}
	
	public function putFile($remote_file, $local_file){
		$remote_file = ltrim($remote_file,'/');
		$remoteFileName = basename ( $remote_file ) ;
		$remotePath = dirname ( $remote_file );
		$linkPath =  $this->asperaTempFolder . '/' .$remoteFileName;
		if (!file_exists(dirname( $linkPath )))
			mkdir(dirname( $linkPath ), 0750, true);
		symlink($local_file, $linkPath);
		
		$this->validateParameters($remotePath);
		$cmd= $this->getCmdPrefix();
		$cmd.=" $linkPath \"$this->user@$this->server:'$remotePath'\"";
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
	public function getFile($remote_file, $local_file = null, $fileSizeRemoteFile = null)
	{	
		$remote_file = ltrim($remote_file,'/');
		
		// $local_file : arrived after validation
		$this->validateParameters($remote_file);
		$cmd= $this->getCmdPrefix();
		$cmd.=" $this->user@$this->server:'$remote_file' '$local_file'";
		return $this->executeCmd($cmd);
	}
	
	private function validateParameters($remote_file) {
		
		$VALID_HOSTNAME_PATTERN = "/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\\-]*[a-zA-Z0-9])\\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\\-]*[A-Za-z0-9])$/";
		$VALID_USERNAME_PATTERN = "/^([a-z_][a-z0-9_]{0,30})$/";
		
		$validInput = TRUE;
		
		$validInput &= (preg_match ($VALID_HOSTNAME_PATTERN, $this->server) === 1); // $this->server
		$validInput &= (preg_match ($VALID_USERNAME_PATTERN, $this->user) === 1); // $this->user
		$validInput &= (is_null($this->passphrase)) || (strpos($this->passphrase, "'") === FALSE);// $this->passphrase : can't contain '
		$validInput &= (is_null($this->pass)) || (strpos($this->pass, "'") === FALSE); // $this->pass : can't contain '
		$validInput &= is_numeric($this->port); // $this->port
		$validInput &= (is_null($this->privKeyFile)) || (realpath($this->privKeyFile) !== FALSE); // $this->privKeyFile exist
		$validInput &= (strpos($remote_file, "'") === FALSE); // $remote_file : can't contain '
	
		if(!$validInput)
			throw new kFileTransferMgrException("Can't put file, Illegal parameters");
	} 
	
	private function getCmdPrefix(){
		$cmd = '';
		if ($this->privKeyFile){
			if ($this->passphrase)
				$cmd = "(echo '$this->passphrase') | $this->ascpCmd ";
			else  
				$cmd = "$this->ascpCmd ";
		}
		else 
			$cmd = "(echo '$this->pass') | $this->ascpCmd ";
		//creating folders on remote server
		$cmd.= " -d ";
		//when connecting to a remote host and prompted to accept a host key, ascp ignores the request
		$cmd.=" --ignore-host-key ";
		$cmd.=" -P $this->port ";
		if ($this->privKeyFile)
			$cmd.=" -i $this->privKeyFile ";
		return $cmd;
		
	}
	
	private function executeCmd($cmd){
		KalturaLog::info('Executing command: '.$cmd);
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
	
	protected function doListFileObjects ($remoteDir) {
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
