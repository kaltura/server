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
	 * @var resource
	 */
	private $sftpId = false;
	
	/**
	 * @var string
	 */
	protected $host;
	
	/**
	 * @var int
	 */
	protected $port;
	
	/**
	 * @var string
	 */
	protected $username;
	
	/**
	 * @var string
	 */
	protected $password = null;
	
	/**
	 * @var string
	 */
	protected $privKeyFile;
	
	/**
	 * @var string
	 */
	protected $passphrase;
	
	/**
	 * @var boolean
	 */
	private $useCmd = true;
	
	/**
	 * @var string
	 */
	private $sshpassCmd = 'sshpass';
	
	/**
	 * @var string
	 */
	private $sftpCmd = 'sftp';
	
	/**
	 * @var boolean
	 */
	private $useCmdChmod = true;
	
	/**
	 * @var float
	 */
	private $cmdPutMinimumFileSize = 1048576; // 1024 * 1024
	
	/**
	 * @var string
	 */
	private $tmpDir = null;
	
	/**
	 * Instances of this class should be created usign the 'getInstance' of the 'kFileTransferMgr' class
	 * Supported options:
	 * - useCmd - indicates that sftp CLI should be used for GET and PUT actions.
	 * - cmdPutMinimumFileSize - CLI will be used for PUT aactions on files larger than this option.
	 * 
	 * @param array $options
	 */
	protected function __construct(array $options = null)
	{
		if(!function_exists('ssh2_connect'))
			throw new kFileTransferMgrException("SSH2 extension is not installed.", kFileTransferMgrException::extensionMissing);
			
		if(!function_exists('ssh2_sftp'))
			throw new kFileTransferMgrException("SSH2 SFTP extension is not installed.", kFileTransferMgrException::extensionMissing);
		
		parent::__construct($options);
		
		$this->tmpDir = sys_get_temp_dir();
		
		if($options)
		{
			if(isset($options['useCmd']))
				$this->useCmd = $options['useCmd'];
			
			if(isset($options['sftpCmd']))
				$this->sftpCmd = $options['sftpCmd'];
			
			if(isset($options['sshpassCmd']))
				$this->sshpassCmd = $options['sshpassCmd'];
			
			if(isset($options['useCmdChmod']))
				$this->useCmdChmod = $options['useCmdChmod'];
			
			if(isset($options['cmdPutMinimumFileSize']))
				$this->cmdPutMinimumFileSize = $options['cmdPutMinimumFileSize'];
			
			if(isset($options['tmpDir']))
				$this->tmpDir = $options['tmpDir'];
		}
	}
	
	/* (non-PHPdoc)
	 * @see kFileTransferMgr::getConnection()
	 */
	public function getConnection()
	{
		if($this->sftpId != null && $this->sftpId != false)
		{
			return $this->sftpId;
		}
		else
		{
			return $this->connection_id;
		}
	}
	
	/**
	 * @return resource
	 */
	private function getSsh2Connection()
	{
		return $this->connection_id;
	}
	
	/**
	 * @return resource
	 */
	private function getSftpConnection()
	{
		return $this->sftpId;
	}
	
	/**********************************************************************/
	/* Implementation of abstract functions from class 'kFileTransferMgr' */
	/**********************************************************************/
	
	/**
	 * sftp connect to server:port.
	 * Wrapping ssh2_connect
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doConnect()
	 */
	protected function doConnect($host, &$port)
	{
		if(!$port)
			$port = 22;
			
		$this->host = $host;
		$this->port = $port;
		
		return ssh2_connect($host, $port);
	}
	
	/**
	 * Login to an existing connection with given user/pass.
	 * Wrapping ssh2_auth_password
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doLogin()
	 */
	protected function doLogin($username, $password)
	{
		$methods = ssh2_auth_none($this->getSsh2Connection(), $username);
		if($methods === true)
			return true;
		
		if(!in_array('password', $methods))
			throw new kFileTransferMgrException("Password authentication is not supported by the server.", kFileTransferMgrException::cantAuthenticate);
			
		$this->username = $username;
		$this->password = $password;
		// try to login
		if(ssh2_auth_password($this->getSsh2Connection(), $username, $password))
		{
			$this->sftpId = ssh2_sftp($this->getSsh2Connection());
			return ($this->sftpId != false && $this->sftpId != null);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Login using a public key.
	 * Wrapping ssh2_auth_pubkey_file and ssh2_sftp
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doLoginPubKey()
	 */
	protected function doLoginPubKey($username, $pubKeyFile, $privKeyFile, $passphrase = null)
	{
		$methods = ssh2_auth_none($this->getSsh2Connection(), $username);
		if($methods === true)
			return true;
		
		if(!in_array('publickey', $methods))
			throw new kFileTransferMgrException("Public key authentication is not supported by the server.", kFileTransferMgrException::cantAuthenticate);
			
		$this->username = $username;
		$this->privKeyFile = $privKeyFile;
		$this->passphrase = $passphrase;
		// try to login
		if(ssh2_auth_pubkey_file($this->getSsh2Connection(), $username, $pubKeyFile, $privKeyFile, $passphrase))
		{
			$this->sftpId = ssh2_sftp($this->getSsh2Connection());
			return ($this->sftpId != false && $this->sftpId != null);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Upload a file to the server
	 * Uses fopen and fwrite or sftp CLI, according to config and file size
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doPutFile()
	 */
	protected function doPutFile($remoteFile, $localFile)
	{
		$sftp = $this->getSftpConnection();
		
		if($this->passphrase || !$this->useCmd || kFile::fileSize($localFile) < $this->cmdPutMinimumFileSize)
		{
			$absolutePath = trim($remoteFile, '/');
			$stream = @fopen("ssh2.sftp://$sftp/$absolutePath", 'w');
			if($stream)
			{
				// Writes the file in chunks (for large files bug)
				$fileToReadHandle = fopen($localFile, "r");
				$ret = $this->writeFileInChunks($fileToReadHandle, $stream);
				@fclose($fileToReadHandle);
				@fclose($stream);
				return $ret;
			}
		}
		
		if($this->useCmd && !$this->passphrase)
			return $this->execSftpCommand("put \"$localFile\" \"$remoteFile\"");
			
		return false;
	}
	
	/** 
	 * Download a file from the server
	 * Uses fopen and fread or sftp CLI, according to config
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doGetFile()
	 */
	protected function doGetFile($remoteFile, $localFile = null)
	{
		if($this->useCmd && !$this->passphrase)
		{
			if($localFile)
				return $this->execSftpCommand("get \"$remoteFile\" \"$localFile\"");
				
			$localFile = tempnam($this->tmpDir, 'sftp.download.');
			if($this->execSftpCommand("get \"$remoteFile\" \"$localFile\""))
				return file_get_contents($localFile);
		}
			
		$sftp = $this->getSftpConnection();
		$absolutePath = trim($remoteFile, '/');
		$stream = @fopen("ssh2.sftp://$sftp/$absolutePath", 'r');
		if(!$stream)
			return false;
			
		if(is_null($localFile))
		{
			$content = $this->writeFileInChunks($stream);
			fclose($stream);
			
			if($content === false)
				throw new kFileTransferMgrException("Failed to read file from [$remoteFile]");
			
			return $content;
		}
		
		// Writes the file in chunks (for large files bug)
		$fileToWriteHandle = fopen($localFile, "w+");
		$ret = $this->writeFileInChunks($stream, $fileToWriteHandle);
		@fclose($fileToWriteHandle);
		@fclose($stream);
		
		return $ret;
	}
	
	/** 
	 * Create a new directory.
	 * Wrapping ssh2_sftp_mkdir
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doMkDir()
	 */
	protected function doMkDir($remotePath)
	{
		return ssh2_sftp_mkdir($this->getSftpConnection(), $remotePath);
	}
	
	/** 
	 * Changes file permissions on the given remote file
	 * Wrapping ssh2_exec.
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doChmod()
	 */
	protected function doChmod($remoteFile, $mode)
	{
		if($this->useCmdChmod)
			return $this->execSftpCommand("chmod $mode \"$remoteFile\"");
			
		$chmod_cmd = "chmod $mode $remoteFile";
		$exec_output = $this->execCommand($chmod_cmd);
		return (trim($exec_output) == ''); // empty output means the command passed ok
	}
	
	/** 
	 * Return true/false according to existence of file on the server
	 * Wrapping ssh2_sftp_stat
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doFileExists()
	 */
	protected function doFileExists($remoteFile)
	{
		$sftp = $this->getSftpConnection();
		$stats = @ssh2_sftp_stat($sftp, $remoteFile);
		return ($stats !== false);
	}
	
	/** 
	 * Return the current working directory.
	 * Wrapping ssh2_exec
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doPwd()
	 */
	protected function doPwd()
	{
		return '/';
	}
	
	/** 
	 * Delete a file and return true/false according to success.
	 * Wrapping ssh2_sftp_unlink;
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doDelFile()
	 */
	protected function doDelFile($remoteFile)
	{
		return ssh2_sftp_unlink($this->getSftpConnection(), $remoteFile);
	}
	
	/** 
	 * Delete a directory and return true/false according to success.
	 * Wrapping ssh2_exec
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doDelDir()
	 */
	protected function doDelDir($remotePath)
	{
		return ssh2_sftp_rmdir($this->getSftpConnection(), $remotePath);
	}
	
	/** 
	 * Return list of files in the given directory
	 * Wrapping ssh2_exec
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doList()
	 */
	protected function doList($remotePath)
	{
		$sftp = $this->getSftpConnection();
		$absolutePath = trim($remotePath, '/');
		$handle = opendir("ssh2.sftp://{$sftp}/{$absolutePath}");
	   	if($handle !== false)
	   	{
	   		$ls = array();
			while (false !== ($file = readdir($handle))) 
			{
				if ($file == '.' || $file == '..')
					continue;
					
				$ls[] = $file;
			}
			closedir($handle);
			return $ls; 
	   	}
        	
		$lsDirCmd = "ls $remotePath";
		$execOutput = $this->execSftpCommand($lsDirCmd);
		KalturaLog::info("sftp rawlist [$execOutput]");
		return array_filter(array_map('trim', explode("\n", $execOutput)), 'strlen');
	}
	
	protected function doListFileObjects ($remote_path)
	{
		$files = $this->listDir($remote_path);
		
		$res = array(); 
		foreach($files as $file)
		{
			$fileObject = new FileObject();
			$fileObject->filename = $file;
			$fileObject->fileSize = $this->fileSize($remote_path . "/$file");
			$fileObject->modificationTime = $this->modificationTime($remote_path . "/$file");
			$res[] = $fileObject;
		}

		return $res;
	}
	
	/**
	 * Upload content to the server
	 * Uses fopen and fwrite
	 * 
	 * @param string $remoteFile
	 * @param string $contents
	 * @throws kFileTransferMgrException
	 */
	public function filePutContents($remoteFile, $contents)
	{
		if(!$this->fileExists(dirname($remoteFile)))
			$this->mkDir(dirname($remoteFile));
		
		$sftp = $this->getSftpConnection();
		$absolutePath = trim($remoteFile, '/');
		$uri = "ssh2.sftp://$sftp/$absolutePath";
		$stream = @fopen($uri, 'w');
		if(!$stream)
			throw new kFileTransferMgrException("Failed to open stream [" . $uri . "]");
		
		if(@fwrite($stream, $contents) === false)
		{
			@fclose($stream);
			throw new kFileTransferMgrException("Failed to upload file to [" . $uri . "]");
		}
		return @fclose($stream);
	}
	
	/** 
	 * Return the size of a given remote file
	 * 
	 * (non-PHPdoc)
	 * @see kFileTransferMgr::doFileSize()
	 */
	protected function doFileSize($remoteFile)
	{
		if(PHP_INT_SIZE >= 8)
		{
			$stat = ssh2_sftp_stat($this->getSftpConnection(), $remoteFile);
			if(isset($stat['size']))
				return $stat['size'];
		}
		
		$remoteFolder = dirname($remoteFile);
		$lsdirCmd = "ls -l $remoteFolder/*";
		$filesInfo = $this->execSftpCommand($lsdirCmd);
		
		KalturaLog::info('sftp rawlist ['. print_r($execOutput, true) .']');
		
		$escapedRemoteFolder = str_replace('/', '\/', $remoteFolder);
		// drwxrwxrwx 10 root root 4096 2010-11-24 23:45 file.ext
		// -rw-r--r--+ 1 mikew Domain Users 7270248766 Feb  9 11:16 Kaltura/LegislativeBriefing2012.mov
		$regexUnix = "^(?P<permissions>[-drwx]{10})\+?\s+(?P<number>\d{1,2})\s+(?P<owner>[\d\w]+)\s+(?P<group>[\d\w\s]+)\s+(?P<fileSize>\d*)\s+((?P<year1>\w{4})-(?P<month1>\d{2})-(?P<day1>\d{2})\s+(?P<hour1>\d{2}):(?P<minute1>\d{2})|(?P<month2>\w{3})\s+(?P<day2>\d{1,2})\s+((?P<hour2>\d{2}):(?P<minute2>\d{2})|(?P<year2>\d{4})))\s+$escapedRemoteFolder\/(?P<file>.+)\s*$";
		
		foreach($filesInfo as $fileInfo)
		{
			$matches = null;
			if(!preg_match("/$regexUnix/", $fileInfo, $matches))
			{
				KalturaLog::err("Unix regex does not match ftp rawlist output [$fileInfo]");
				continue;
			}
			
			if($matches['file'] == basename($remoteFile))
				return $matches['fileSize'];
		}
		return null;
	}
	
	/**
	 * Return unix timestamp of given file last modification time.
	 * Wrapping ssh2_sftp_stat
	 * 
	 * @param string $remoteFile
	 * @return int
	 */
	protected function doModificationTime($remoteFile)
	{
		$statinfo = ssh2_sftp_stat($this->getSftpConnection(), $remoteFile);
		$modificationTime = isset($statinfo['mtime']) ? $statinfo['mtime'] : null;
		return $modificationTime;
	}
	
	/**
	 * Execute the given command on the server
	 * 
	 * @param string $command
	 * @return string
	 */
	private function execSftpCommand($command)
	{
		$cliCommand = "{$this->sftpCmd} -oPort={$this->port} -oStrictHostKeyChecking=no";
		
		if($this->verbose)
			$cliCommand .= " -v";
			
		if($this->privKeyFile)
			$cliCommand .= " -oIdentityFile={$this->privKeyFile}";
		else
		{
		        $fixedPassword = str_replace("'","'\''",$this->password);
			$cliCommand = "{$this->sshpassCmd} -p '$fixedPassword' $cliCommand";
		}
			
		$cliCommand .= " {$this->username}@{$this->host}";
		
		$cmd = "(echo '$command' && echo 'quit') | $cliCommand 2>&1";
		KalturaLog::info("Command [$cmd]");
		$returnValue = null;
		
		exec($cmd, $output, $returnValue);
		if ($returnValue){ //any non-zero return value is an error
			KalturaLog::err("An error while running exec - " . print_r($output, true));
			@trigger_error($output[count($output)-2] ."; ". $output[count($output)-1]); //in order to populate the correct error to error_get_last() in kFileTransferMgr
			return false; 
		}
		
		return $output;
	}
	
	/**
	 * Execute the given command on the server
	 * 
	 * @param string $command
	 * @return string
	 */
	private function execCommand($command)
	{
		KalturaLog::info($command);
		
		$stream = ssh2_exec($this->getSsh2Connection(), $command);
		if(!$stream || !is_resource($stream))
			return null;
		
		stream_set_blocking($stream, true);
		$output = stream_get_contents($stream);
		fclose($stream);
		return $output;
	}
}
