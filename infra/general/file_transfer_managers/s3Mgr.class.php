<?php
/**
 * Extends the 'kFileTransferMgr' class & implements a file transfer manager using the Amazon S3 protocol.
 * For additional comments please look at the 'kFileTransferMgr' class.
 *
 * @package infra
 * @subpackage Storage
 */
class s3Mgr extends kFileTransferMgr
{
	private $s3;
	// instances of this class should be created usign the 'getInstance' of the 'kFileTransferMgr' class
	protected function __construct()
	{
		// do nothing
		$this->connection_id = 1; //SIMULATING!
	}



	public function getConnection()
	{
		return $this->connection_id;
	}

	/**********************************************************************/
	/* Implementation of abstract functions from class 'kFileTransferMgr' */
	/**********************************************************************/

	// sftp connect to server:port
	protected function doConnect($sftp_server, &$sftp_port)
	{
		return 1;
	}


	// login to an existing connection with given user/pass (ftp_passive_mode is irrelevant)
	protected function doLogin($sftp_user, $sftp_pass, $ftp_passive_mode = TRUE)
	{
		//KalturaLog::debug("doLogin is active");
		if(!class_exists("S3")) {
			KalturaLog::debug("Class S3 was not found!!");
			return FALSE;
		}
		$this->s3 = new S3($sftp_user, $sftp_pass,FALSE);
		//KalturaLog::debug("after new S3");
		$buckets = $this->s3->listBuckets(); //just to check whether the connection is good
		//KalturaLog::debug("buckets: ".print_r($buckets, TRUE));
		if($buckets !== FALSE) {
			//KalturaLog::debug("Connected to Amazon");
			return TRUE;
		}
		//KalturaLog::debug("Connection to Amazon failed");
		return FALSE;
	}


	// login using a public key
	protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null)
	{
		return false;
	}


	// upload a file to the server (ftp_mode is irrelevant
	protected function doPutFile ($remote_file , $local_file , $ftp_mode, $http_field_name = null, $http_file_name = null)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		KalturaLog::debug("remote_file: ".$remote_file);
		$res = $this->s3->putObjectFile($local_file, $bucket, $remote_file, S3::ACL_PUBLIC_READ);

		if ($res)
		{
			$info = $this->s3->getObjectInfo($bucket, $remote_file);
			if ($info && $info['size'] == filesize($local_file))
			{
				KalturaLog::debug("File uploaded to Amazon, info: ".print_r($info, TRUE));
				return TRUE;
			}
			else
			{
				KalturaLog::debug("error uploading file ".$local_file." s3 info ".print_r($info, true));
				return FALSE;
			}
		}
	}

	// download a file from the server (ftp_mode is irrelevant)
	protected function doGetFile ($remote_file, $local_file, $ftp_mode)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		KalturaLog::debug("remote_file: ".$remote_file);
		$saveTo = fopen($local_file,"w");
		if(!$saveTo) return FALSE;
		return $this->s3->getObject($bucket, $remote_file, $saveTo);
	}

	// create a new directory
	protected function doMkDir ($remote_path)
	{
		return false;
	}

	// chmod the given remote file
	protected function doChmod ($remote_file, $chmod_code)
	{
		return false;
	}

	// return true/false according to existence of file on the server
	protected function doFileExists($remote_file)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		if($this->isdirectory($remote_file)) {
			return true;
		}
		KalturaLog::debug("remote_file: ".$remote_file);
		$info = $this->s3->getObjectInfo($bucket, $remote_file);
		return ($info && $info['size'] != 0);
	}

	private function isdirectory($file_name) {
		if(strpos($file_name,'.') === FALSE) return TRUE;
		return FALSE;
	}
	
	// return the current working directory
	protected function doPwd ()
	{
		return false;
	}

	// delete a file and return true/false according to success
	protected function doDelFile ($remote_file)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		KalturaLog::debug("remote_file: ".$remote_file);
		return $this->s3->getObject($bucket, $remote_file);
	}

	// delete a directory and return true/false according to success
	protected function doDelDir ($remote_path)
	{
		return false;
	}

	protected function doList ($remote_path)
	{
		return false;
	}

	protected function doFileSize($remote_file)
	{
		return false;
	}

	// download a file from the server
	public function fileGetContents ($remote_file)
	{
		return false;
	}

	// upload a file to the server
	public function filePutContents ($remote_file, $contents)
	{
		return false;
	}

	// execute the given command on the server
	private function execCommand($command_str)
	{
		return false;
	}

}
