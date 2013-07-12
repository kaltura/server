<?php
/**
 * Extends the 'kFileTransferMgr' class & implements a file transfer manager for the local filesystem.
 * For additional comments please look at the 'kFileTransferMgr' class.
 * 
 * @package infra
 * @subpackage Storage
 */
class localMgr extends kFileTransferMgr
{

	// instances of this class should be created usign the 'getInstance' of the 'kFileTransferMgr' class
	protected function __construct(array $options = null)
	{
		parent::__construct($options);
	}


	/**********************************************************************/
	/* Implementation of abstract functions from class 'kFileTransferMgr' */
	/**********************************************************************/

	// ftp connect to server:port
	protected function doConnect($local_server, &$local_port)
	{
		return true;
	}


	// login to an existing connection with given user/pass
	protected function doLogin($local_user, $local_pass)
	{
		return true;
	}


	// login using a public key - not supported in FTP
	protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null)
	{
		return true;
	}


	// upload a file to the server (ftp_mode is irrelevant
	protected function doPutFile ($remote_file,  $local_file)
	{
		return @copy($remote_file, $local_file);
	}


	// download a file from the server (ftp_mode is irrelevant)
	protected function doGetFile ($remote_file, $local_file = null)
	{
		if($local_file)
			return @copy($remote_file, $local_file);
			
		return file_get_contents($remote_file);
	}


	// create a new directory on the server
	protected function doMkDir ($remote_path)
	{
	    return mkdir($remote_path);
	}


	// chmod to the given remote file
	protected function doChmod ($remote_file, $chmod_code)
	{
		return chmod($remote_file, $chmod_code);
	}


	// check if the given file/dir exists on the server
	protected function doFileExists($remote_file)
	{
	    clearstatcache();
	    return @file_exists($remote_file);
	}

	// return the current working directory
	protected function doPwd()
	{
	    return getcwd();
	}

	// delete a file and return true/false according to success
	protected function doDelFile ($remote_file)
	{
		// if the file doesnt exist don't return an error. when using a local drop folder the files may be moved and not copied
		// in this case the file won't exist anymore in the drop folder however the watcher will make sure orphan files are deleted.
		return file_exists($remote_file) ? @unlink($remote_file) : true;
	}

	// delete a directory and return true/false according to success
	protected function doDelDir ($remote_path)
	{
	    return @rmdir($remote_path);
	}

	protected function doList ($remoteDir)
	{
	    clearstatcache();
		return @scandir($remoteDir, 0);
	}
	
	protected function doFileSize($remote_file)
	{
		return kFile::fileSize($remote_file);
	}
	
    protected function doModificationTime($remote_file)
	{
	    clearstatcache();
	    $modificationTime = @filemtime($remote_file);
	    if (!$modificationTime) {
	        return null;
	    }
	    return $modificationTime;
	}	

}
