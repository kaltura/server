<?php

// AWS SDK PHP Client Library
require_once(KAutoloader::buildPath(KALTURA_ROOT_PATH, 'vendor', 'aws', 'aws-autoloader.php'));

use Aws\S3\S3Client;
use Aws\Sts\StsClient;

use Aws\S3\Exception\S3Exception;
use Aws\Exception\AwsException;
use Aws\S3\Enum\CannedAcl;

use Aws\Common\Credentials\Credentials;
use Aws\Common\Credentials\RefreshableInstanceProfileCredentials;
use Aws\Common\Credentials\AbstractRefreshableCredentials;
use Aws\Common\Credentials\CacheableCredentials;

use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;

/**
 * Extends the 'kFileTransferMgr' class & implements a file transfer manager using the Amazon S3 protocol with Authentication Version 4.
 * For additional comments please look at the 'kFileTransferMgr' class.
 *
 * @package infra
 * @subpackage Storage
 */
class s3Mgr extends kFileTransferMgr
{
	/* @var S3Client $s3 */
	private $s3;

	protected $filesAcl = CannedAcl::PRIVATE_ACCESS;
	protected $s3Region = '';
	protected $sseType = '';
	protected $sseKmsKeyId = '';
	protected $signatureType = null;
	protected $endPoint = null;
	protected $storageClass = null;
	protected $s3Arn = null;

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

		if($options && isset($options['storageClass']))
		{
			$this->storageClass = $options['storageClass'];
		}

		if (class_exists('KBatchBase'))
		{
			$this->s3Arn = KBatchBase::$taskConfig->s3Arn;
		}
		else
		{
			$this->s3Arn = kConf::get('s3Arn', 'cloud_storage', null);
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

		if($this->s3Arn && (!isset($sftp_user) || !$sftp_user) && (!isset($sftp_pass) || !$sftp_pass))
		{
			KalturaLog::debug('Found env VAR from config- ' . $this->s3Arn);
			if(!class_exists('Aws\Sts\StsClient'))
			{
				KalturaLog::err('Class Aws\S3\StsClient was not found!!');
				return false;
			}

			return $this->generateS3Client();
		}

		$config = array(
			'credentials' => array(
				'key'    => $sftp_user,
				'secret' => $sftp_pass,
			),
			'region' => $this->s3Region,
			'signature' => $this->signatureType ? $this->signatureType : 'v4',
			'version' => '2006-03-01',
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

	private function generateS3Client()
	{
		$credentialsCacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 's3_creds_cache';

		$roleRefresh = new RefreshableRole(new Credentials('', '', '', 1));
		$roleRefresh->setRoleArn($this->s3Arn);
		$roleCache = new DoctrineCacheAdapter(new FilesystemCache("$credentialsCacheDir/roleCache/"));
		$roleCreds = new CacheableCredentials($roleRefresh, $roleCache, 'creds_cache_key');

		$this->s3 = S3Client::factory(array(
			'credentials' => $roleCreds,
			'region' => $this->s3Region,
			'signature' => 'v4',
			'version' => '2006-03-01'
		));

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

		$params = array();
		if ($this->sseType === "KMS")
		{
			$params['ServerSideEncryption'] = "aws:kms";
			$params['SSEKMSKeyId'] = $this->sseKmsKeyId;
		}

		if ($this->sseType === "AES256")
		{
			$params['ServerSideEncryption'] = "AES256";
		}

		if ($this->storageClass)
		{
			$params['StorageClass'] = $this->storageClass;
		}

		while ($retries)
		{
			list($success, $message) = @($this->doPutFileHelper($remote_file, $local_file, $params));
			if ($success)
				return true;

			KalturaLog::debug("Failed to export File: " . $remote_file . " number of retries left: " . $retries);
			$retries--;
		}
		//throw temporary exception so that the batch will retry
		throw new kTemporaryException("Can't put file [$remote_file] - " . $message);
	}

	private function doPutFileHelper($remote_file , $local_file, $params)
	{
		list($bucket, $remote_file) = explode("/", ltrim($remote_file, "/"), 2);
		KalturaLog::debug("remote_file: " . $remote_file);
		$fp = null;
		try
		{
			$size = kFile::fileSize($local_file);
			KalturaLog::debug("file size is : " . $size);
			$options = array('params' => $params);
			$concurrency = $this->getConcurrency();
			if($concurrency)
			{
				$options['concurrency'] = $concurrency;
			}
			KalturaLog::debug("Executing Multipart upload to S3: for " . $local_file);
			$fp = fopen($local_file, 'r');
			
			if (!$fp)
			{
				KalturaLog::err("Failed to fopen given file [$local_file]");
				return array(false, "Failed to fopen given file [$local_file]");
			}
			
			$res = $this->s3->upload($bucket,
				$remote_file,
				$fp,
				$this->filesAcl,
				$options
			);
			
			if($fp)
			{
				fclose($fp);
			}

			KalturaLog::debug("File uploaded to Amazon, info: " . print_r($res, true));
			return array(true, null);
		}
		catch (Exception $e)
		{
			if ($fp)
			{
				fclose($fp);
			}
			KalturaLog::err("error uploading file " . $local_file . " s3 info: " . $e->getMessage());
			return array(false, $e->getMessage());
		}
	}

	/**
	 * @return bool|mixed|null
	 * @throws Exception
	 */
	private function getConcurrency()
	{
		if (class_exists('KBatchBase'))
		{
			if (isset(KBatchBase::$taskConfig->maxConcurrentUploadConnections))
			{
				return KBatchBase::$taskConfig->maxConcurrentUploadConnections;
			}
			return null;
		}
		else
		{
			return kConf::get('maxConcurrentUploadConnections', 'cloud_storage', null);
		}
	}

	// download a file from the server (ftp_mode is irrelevant)
	protected function doGetFile($remote_file, $local_file = null)
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
	protected function doMkDir($remote_path)
	{
		return false;
	}

	// chmod the given remote file
	protected function doChmod($remote_file, $chmod_code)
	{
		return false;
	}

	// return true/false according to existence of file on the server
	protected function doFileExists($remote_file)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		if($this->isDirectory($remote_file))
		{
			return true;
		}
		KalturaLog::debug("remote_file: ".$remote_file);

		$exists = $this->s3->doesObjectExist($bucket, $remote_file);
		return $exists;
	}

	private function isDirectory($file_name) {
		if(strpos($file_name,'.') === false) return TRUE;
		return false;
	}

	// return the current working directory
	protected function doPwd ()
	{
		return false;
	}

	// delete a file and return true/false according to success
	protected function doDelFile($remote_file)
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
		$dirList = array();
		list($bucket, $remoteDir) = explode("/",ltrim($remote_path,"/"),2);
		KalturaLog::debug("Listing dir contents for bucket [$bucket] and dir [$remoteDir]");
		
		try
		{
			$dirListObjectsRaw = $this->s3->getIterator('ListObjects', array(
				'Bucket' => $bucket,
				'Prefix' => $remoteDir
			));
			
			foreach ($dirListObjectsRaw as $dirListObject)
			{
				$dirList[] = array (
					"path" =>  $bucket . DIRECTORY_SEPARATOR . $dirListObject['Key'],
					"fileSize" => $dirListObject['Size']
				);
			}
		}
		catch ( Exception $e )
		{
			KalturaLog::err("Couldn't list file objects for remote path, [$remote_path] from bucket [$bucket]: {$e->getMessage()}");
		}
		
		return $dirList;
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

	public function registerStreamWrapper()
	{
		$this->s3->registerStreamWrapper();
	}
	
	public function getRemoteUrl($remote_file)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		
		$params = array(
			'Bucket' => $bucket,
			'Key'    => $remote_file,
		);
		
		$cmd = $this->s3->getCommand('GetObject', $params);
		
		$expiry = time() + 600;
		$preSignedUrl = $cmd->createPresignedUrl($expiry);
		
		KalturaLog::debug("remote_file: [$remote_file] presignedUrl [$preSignedUrl]");
		
		return $preSignedUrl;
	}

	public function getFileUrl($remote_file, $expires = null)
	{
		list($bucket, $remote_file) = explode("/", ltrim($remote_file, "/"), 2);
		KalturaLog::debug("remote_file: " . $remote_file);
		return $this->s3->getObjectUrl($bucket, $remote_file, $expires);
	}
}
