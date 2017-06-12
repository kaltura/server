<?php
/**
 * Extends the 'kFileTransferMgr' class & implements a file transfer manager using the SCP protocol.
 * For additional comments please look at the 'kFileTransferMgr' class.
 * 
 * @package infra
 * @subpackage Storage
 */
class scpMgr extends kFileTransferMgr
{
	
	// instances of this class should be created usign the 'getInstance' of the 'kFileTransferMgr' class
	protected function __construct(array $options = null)
	{
		if(!function_exists('ssh2_connect'))
			throw new kFileTransferMgrException("SSH2 extension is not installed.", kFileTransferMgrException::extensionMissing);
	
		parent::__construct($options);
	}
	
	
	/**********************************************************************/
	/* Implementation of abstract functions from class 'kFileTransferMgr' */
	/**********************************************************************/
	
	// scp connect to server:port
	protected function doConnect($scp_server, &$scp_port)
	{
		// try connecting to server
		if (!$scp_port || $scp_port == 0) {
                	$scp_port = 22;
		}
		return ssh2_connect($scp_server, $scp_port);
	}
	
	
	// login to an existing connection with given user/pass (ftp_passive_mode is irrelevant)
	protected function doLogin($scp_user, $scp_pass)
	{
		// try to login
		return ssh2_auth_password($this->getConnection(), $scp_user, $scp_pass);
	}
	
	
	// login using a public key
	protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null)
	{
		return ssh2_auth_pubkey_file($this->getConnection(), $user, $pubKeyFile, $privKeyFile, $passphrase);
	}
	
	
	// upload a file to the server (ftp_mode is irrelevant
	protected function doPutFile ($remote_file , $local_file)
	{
		// try to upload file
		$remote_file = ltrim($remote_file, '/');
		return ssh2_scp_send ($this->getConnection() , $local_file, $remote_file);
	}
	
	
	// download a file from the server (ftp_mode is irrelevant)
	protected function doGetFile ($remote_file, $local_file = null)
	{	
		// try to download file
		$remote_file = ltrim($remote_file, '/');
		$ret = ssh2_scp_recv($this->getConnection(), $remote_file, $local_file);
	}
	
	// create a new directory
	protected function doMkDir ($remote_path)
	{
	    $remote_path = ltrim($remote_path, '/');
		$mkdir_cmd = 'mkdir -p ' . $remote_path;
		$exec_output = $this->execCommand($mkdir_cmd);
		return (trim($exec_output) == ''); // empty output means the command passed ok
	}
	
	// chmod the given remote file
	protected function doChmod ($remote_file, $chmod_code)
	{
	    $remote_file = ltrim($remote_file, '/');
		$chmod_cmd = 'chmod ' . $chmod_code . ' ' . $remote_file;
		$exec_output = $this->execCommand($chmod_cmd);
		return (trim($exec_output) == ''); // empty output means the command passed ok
	}
	
	// return true/false according to existence of file on the server
	protected function doFileExists($remote_file)
	{
	    $remote_file = ltrim($remote_file, '/');
		$exists_cmd = 'test -e ' . $remote_file . ' && echo EXISTS';
		$exec_output = $this->execCommand($exists_cmd);
		return (trim($exec_output) == 'EXISTS');
	}


        // return the current working directory
	protected function doPwd ()
	{
		$pwd_cmd = 'pwd';
		return $this->execCommand($pwd_cmd);
	}

    // delete a file and return true/false according to success
    protected function doDelFile ($remote_file)
    {
        $remote_file = ltrim($remote_file, '/');
        $delfile_cmd = 'rm ' . $remote_file;
        $exec_output = $this->execCommand($delfile_cmd);
        return (trim($exec_output) == ''); // empty output means the command passed ok
    }

     // delete a directory and return true/false according to success
    protected function doDelDir ($remote_path)
    {
        $remote_path = ltrim($remote_path, '/');
        $deldir_cmd = 'rm -r ' . $remote_path;
        $exec_output = $this->execCommand($deldir_cmd);
        return (trim($exec_output) == ''); // empty output means the command passed ok
    }
	

	protected function doList ($remote_path)
	{
        $remote_path = ltrim($remote_path, '/');
        $lsdir_cmd = 'ls ' . $remote_path;
        $exec_output = $this->execCommand($lsdir_cmd);
        return array_map('trim', explode("\n", $exec_output));
	}
	
	protected function doListFileObjects ($remoteDir)
	{
		$res = array();
		$filesList = $this->listDir($remoteDir);
		foreach ($filesList as $file)
		{
			$fileObject = new FileObject();
			$fileObject->filename =  $file;
			$res[$fileObject->filename] = $fileObject;
		}
		
		return $res;
	}		
	
	protected function doFileSize($remote_file)
	{
	    $remote_file = ltrim($remote_file, '/');
		$exists_cmd = 'du -b ' . $remote_file;
		$exec_output = $this->execCommand($exists_cmd);
		$output_array = explode("\t", $exec_output);
		if (isset($output_array[0]) && is_numeric($output_array[0])) {
		    return $output_array[0];
		}
		else {
		    return null;
		}
	}
	
	protected function doModificationTime($remote_file)
	{
	    $remote_file = ltrim($remote_file, '/');
		$exists_cmd = 'stat -c %Y ' . $remote_file;
		$exec_output = $this->execCommand($exists_cmd);
		$digitsRegex = '/[0-9]*/';
		$matches = array();
		if (preg_match($digitsRegex, $exec_output, $matches)) {
		    $match = reset($matches);
		    if (is_numeric($match)) {
		        return $match;
		    }
		}
		return null;
	}

	// execute the given command on the server
	private function execCommand($command_str)
	{
		$stream = ssh2_exec($this->getConnection(), $command_str);
  		stream_set_blocking($stream, true);
   		$output = stream_get_contents($stream);
   		fclose($stream);
   		return $output;
	}
	
}
?>