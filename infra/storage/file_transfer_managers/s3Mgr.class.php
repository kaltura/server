<?php

// AWS SDK PHP Client Library
require_once(KAutoloader::buildPath(KALTURA_ROOT_PATH, 'vendor', 'aws', 'aws-autoloader.php'));

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Enum\CannedAcl;

/**
 * Extends the 'kFileTransferMgr' class & implements a file transfer manager using the Amazon S3 protocol with Authentication Version 4.
 * For additional comments please look at the 'kFileTransferMgr' class.
 *
 * @package infra
 * @subpackage Storage
 */
class s3Mgr extends kFileTransferMgr
{
	private $s3;
		
	protected $filesAcl = CannedAcl::PRIVATE_ACCESS;
	protected $s3Region = '';
	protected $sseType = '';
	protected $sseKmsKeyId = '';
	protected $signatureType = null;
	protected $endPoint = null;
	
	// instances of this class should be created usign the 'getInstance' of the 'kFileTransferMgr' class
	protected function __construct(array $options = null)
	{
		parent::__construct($options);
	
		if($options && isset($options['filesAcl']))
		{
			$this->filesAcl = $options['filesAcl'];
		}
		
		if($options && isset($options['s3Region']))
		{
			$this->s3Region = $options['s3Region'];
		}
		
		if($options && isset($options['sseType']))
		{
			$this->sseType = $options['sseType'];
		}
		
		if($options && isset($options['sseKmsKeyId']))
		{
			$this->sseKmsKeyId = $options['sseKmsKeyId'];
		}
		
		if($options && isset($options['signatureType']))
		{
			$this->signatureType = $options['signatureType'];
		}
		
		if($options && isset($options['endPoint']))
		{
			$this->endPoint = $options['endPoint'];
		}
		
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
	//
	// S3 Signature is required to be V4 for SSE-KMS support. Newer S3 regions also require V4.
	//
	protected function doLogin($sftp_user, $sftp_pass)
	{
		if(!class_exists('Aws\S3\S3Client')) 
		{
			KalturaLog::err('Class Aws\S3\S3Client was not found!!');
			return false;
		}

		$config = array(
					'credentials' => array(
							'key'    => $sftp_user,
							'secret' => $sftp_pass,
					),
					'region' => $this->s3Region,
					'signature' => $this->signatureType ? $this->signatureType : 'v4',
			);
		
		if ($this->endPoint)
			$config['endpoint'] = $this->endPoint;
		 
		$this->s3 = S3Client::factory($config);
		
		/** 
		 * There is no way of "checking the credentials" on s3.
		 * Doing a ListBuckets would only check that the user has the s3:ListAllMyBuckets permission
		 * which we don't use anywhere else in the code anyway. The code will fail soon enough in the
		 * code elsewhere if the permissions are not sufficient.
		 **/
		return true;
	}


	// login using a public key
	protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null)
	{
		return false;
	}


	// upload a file to the server (ftp_mode is irrelevant
	protected function doPutFile ($remote_file , $local_file)
	{
		$retries = 3;
		while($retries)
		{
			list($success, $message) = @($this->doPutFileHelper($remote_file , $local_file));
			if($success)
				return true;

			KalturaLog::debug("Failed to export File: ".$remote_file." number of retries left: ".$retries);
			$retries--;
		}
		//throw temporary exception so that the batch will retry
		throw new kTemporaryException("Can't put file [$remote_file] - " . $message);
	}

	private function doPutFileHelper($remote_file , $local_file)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		KalturaLog::debug("remote_file: ".$remote_file);

		try
		{

			$params = array(
					'Bucket'       => $bucket,
					'Key'          => $remote_file,
					'SourceFile'   => $local_file,
					'ACL'          => $this->filesAcl,
			);

			if($this->sseType === "KMS")
			{
				$params['ServerSideEncryption'] = "aws:kms";
				$params['SSEKMSKeyId'] = $this->sseKmsKeyId;
			}

			if($this->sseType === "AES256")
			{
				$params['ServerSideEncryption'] = "AES256";
			}

			$res = $this->s3->putObject($params);

			KalturaLog::debug("File uploaded to Amazon, info: " . print_r($res, true));
			return array(true, null);
		}
		catch ( Exception $e )
		{
			KalturaLog::err("error uploading file ".$local_file." s3 info: ".$e->getMessage());
			return array(false, $e->getMessage());
		}
	}

	// download a file from the server (ftp_mode is irrelevant)
	protected function doGetFile ($remote_file, $local_file = null)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		KalturaLog::debug("remote_file: ".$remote_file);

		$params = array(
				'Bucket' => $bucket,
				'Key'    => $remote_file,
			);		

		if($local_file)
		{
			$params['SaveAs'] = $local_file;
		}

		$response = $this->s3->getObject( $params );
		if($response && !$local_file)
		{
			return $response['Body'];
		}
			
		return $response;
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
		if($this->isdirectory($remote_file)) 
		{
			return true;
		}
		KalturaLog::debug("remote_file: ".$remote_file);

		$exists = $this->s3->doesObjectExist($bucket, $remote_file);
		return $exists;
	}

	private function isdirectory($file_name) {
		if(strpos($file_name,'.') === false) return TRUE;
		return false;
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

		$deleted = false;
		try
		{
			$this->s3->deleteObject(array(
					'Bucket' => $bucket,
					'Key' => $remote_file,
				));

			$deleted = true;
		}
		catch ( Exception $e )
		{
			KalturaLog::err("Couldn't delete file [$remote_file] from bucket [$bucket]: {$e->getMessage()}");
		}
		
		return $deleted;
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

	protected function doListFileObjects ($remoteDir)
	{
		return false;
	}

	protected function doFileSize($remote_file)
	{
		return false;
	}

	// execute the given command on the server
	private function execCommand($command_str)
	{
		return false;
	}
}
