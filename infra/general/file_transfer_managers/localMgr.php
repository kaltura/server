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
	protected function __construct()
	{
		// do nothing
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
	protected function doLogin($local_user, $local_pass, $ftp_passive_mode = TRUE)
	{
		return true;
	}


	// login using a public key - not supported in FTP
	protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null)
	{
		return false; // NOT SUPPORTED
	}


	// upload a file to the server (ftp_mode is irrelevant
	protected function doPutFile ($remote_file,  $local_file, $ftp_mode, $http_field_name = null, $http_file_name = null)
	{
		return @copy($remote_file, $local_file);
	}


	// download a file from the server (ftp_mode is irrelevant)
	protected function doGetFile ($remote_file, $local_file, $ftp_mode)
	{
		return @copy($remote_file, $local_file);
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
		return @unlink($remote_file);
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
	    clearstatcache();
	    return @filesize($remote_file);
	}

}
?>