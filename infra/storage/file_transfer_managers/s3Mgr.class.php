<?php

// AWS SDK PHP Client Library
require_once(KAutoloader::buildPath(KALTURA_ROOT_PATH, 'vendor', 'aws', 'aws-autoloader.php'));

use Aws\S3\S3Client;
use Aws\Sts\StsClient;

use Aws\Credentials\CredentialProvider;
use Aws\DoctrineCacheAdapter;
use Doctrine\Common\Cache\FilesystemCache;

use Aws\S3\Exception\S3Exception;
use Aws\Exception\AwsException;

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

	protected $filesAcl = 'private'; //CannedAcl::PRIVATE_ACCESS;
	protected $s3Region = '';
	protected $sseType = '';
	protected $sseKmsKeyId = '';
	protected $signatureType = null;
	protected $endPoint = null;
	protected $storageClass = null;
	protected $s3Arn = null;
	protected $dirnameSuffix = null;

	const SIZE = 'Size';
	const LAST_MODIFICATION = 'LastModified';
	const CONTENT_LENGTH = 'ContentLength';
	const HEAD_OBJECT = 'headObject';
	const DEFAULT_S3_APP_NAME = "Kaltura-Server";

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
		
		if($options && isset($options['s3Arn']))
		{
			$this->s3Arn = $options['s3Arn'];
			$this->dirnameSuffix = 'dropFolderWatcherRemoteS3';
		}
		else
		{
			if (class_exists('KBatchBase'))
			{
				$this->s3Arn = kBatchUtils::getKconfParam('arnRole', true);
			}
			else
			{
				$this->s3Arn = kConf::get('s3Arn', 'cloud_storage', null);
			}
			
			$this->dirnameSuffix = 's3Mgr';
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
			KalturaLog::debug("Login using ARN [ {$this->s3Arn} ] and region [ {$this->s3Region} ]");
			
			if(!class_exists('Aws\Sts\StsClient'))
			{
				KalturaLog::err('Class Aws\S3\StsClient was not found!!');
				return false;
			}

			return $this->generateCachedCredentialsS3Client();
		}

		/**
		 * There is no way of "checking the credentials" on s3.
		 * Doing a ListBuckets would only check that the user has the s3:ListAllMyBuckets permission
		 * which we don't use anywhere else in the code anyway. The code will fail soon enough in the
		 * code elsewhere if the permissions are not sufficient.
		 **/
		return $this->generateStaticCredsClient($sftp_user, $sftp_pass);
	}

	private function generateCachedCredentialsS3Client()
	{
		$cacheProviderCredentials = RefreshableRole::getCacheCredentialsProvider($this->s3Arn, $this->s3Region, $this->dirnameSuffix);
		$config = $this->getBaseClientConfig();
		$config['credentials'] = $cacheProviderCredentials;
		$this->s3 = S3Client::factory($config);

		return true;
	}

	private function generateStaticCredsClient($key, $secret)
	{
		$config = $this->getBaseClientConfig();
		$config['credentials'] = array(
			'key'    => $key,
			'secret' => $secret,
		);

		$this->s3 = S3Client::factory($config);

		return true;
	}

	private function getBaseClientConfig()
	{
		$config = array(
			'region' => $this->s3Region,
			'signature' => 'v4',
			'version' => '2006-03-01',
			'ua_append' => array($this->getClientUserAgent())
		);

		if ($this->endPoint)
			$config['endpoint'] = $this->endPoint;

		return $config;
	}

	private function getClientUserAgent()
	{
		$appName = self::DEFAULT_S3_APP_NAME;
		return "APN/1.0 batch/1.0 $appName/1.0";
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
			return kBatchUtils::getKconfParam('maxConcurrentUploadConnections', true);
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
		if($this->isDirectory($remote_file))
		{
			return true;
		}
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		KalturaLog::debug("remote_file: ".$remote_file);

		$exists = $this->s3->doesObjectExist($bucket, $remote_file);
		return $exists;
	}

	private function isDirectory($remote_file)
	{
		//When checking if path is Dir in s3 add a trailing slash to the path to avoid considering files with the same name but different ext as dir's
		// Example:
		//  my_bucket/dir1/dir2/my_file.mp4
		//  my_bucket/dir1/dir2/my_file.mp4.log
		$remote_file = $remote_file . '/';
		list($bucket, $key) = explode("/",ltrim($remote_file,"/"),2);
		try
		{
			$dirListObjectsRaw = $this->s3->getIterator('ListObjects', array(
				'Bucket' => $bucket,
				'Prefix' => $key
			));
			
			foreach ($dirListObjectsRaw as $dirListObject)
			{
				return true;
			}
		}
		catch ( Exception $e )
		{
			kSharedFileSystemMgr::safeLog("Couldn't determine if path [$remote_file] is dir: {$e->getMessage()}");
		}
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
					"fileSize" => $dirListObject[self::SIZE],
					'modificationTime' => $dirListObject[self::LAST_MODIFICATION],
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
		$files =  $this->doList ($remoteDir);
		$fileObjectsResult = array ();
		foreach($files as $file)
		{
			if(trim($remoteDir, '/') === trim($file['path'],'/') || $file['fileSize'] == 0)
			{
				continue;
			}
			$fileObject = new FileObject();
			$fileObject->filename = substr($file['path'], strlen($remoteDir));
			$fileObject->fileSize = $file['fileSize'];
			$fileObject->modificationTime = strtotime($file['modificationTime']);
			$fileObjectsResult[] = $fileObject;
		}
		return $fileObjectsResult;
	}

	public function initBasicS3Params($filePath)
	{
		list($bucket, $filePath) = explode("/",ltrim($filePath,"/"),2);

		$params = array(
			'Bucket' => $bucket,
			'Key'    => $filePath,
		);
		return $params;
	}

	protected function s3Call($command, $params = null, $filePath = null)
	{
		if(!$params && $filePath)
		{
			$params = $this->initBasicS3Params($filePath);
		}

		try
		{
			$result = $this->s3->{$command}($params);
			return $result;
		}
		catch (S3Exception $e)
		{
			KalturaLog::warning($e->getMessage());
			return false;
		}
	}

	protected function doFileSize($remoteFile)
	{
		$result = $this->s3Call(self::HEAD_OBJECT, null, $remoteFile);
		if(!$result)
		{
			return false;
		}
		return $result->get(self::CONTENT_LENGTH);
	}

	protected function doModificationTime($remoteFile)
	{
		$result = $this->s3Call(self::HEAD_OBJECT, null, $remoteFile);
		if(!$result)
		{
			return false;
		}
		return strtotime($result->get(self::LAST_MODIFICATION));
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
		return $this->getPreSignedUrl($remote_file);
	}
	
	private function getPreSignedUrl($remote_file, $expiry = null)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		
		$params = array(
			'Bucket' => $bucket,
			'Key'    => $remote_file,
		);
		
		if(!$expiry)
		{
			$expiry = time() + 600;
		}
		
		$cmd = $this->s3->getCommand('GetObject', $params);
		
		$request = $this->s3->createPresignedRequest($cmd, $expiry);
		$preSignedUrl = (string)$request->getUri();
		
		KalturaLog::debug("remote_file: [$remote_file] presignedUrl [$preSignedUrl]");
		return $preSignedUrl;
	}

	public function getFileUrl($remote_file, $expires = null)
	{
		KalturaLog::debug("Get file url for remote_file: " . $remote_file);
		
		if(!$expires)
		{
			list($bucket, $remote_file) = explode("/", ltrim($remote_file, "/"), 2);
			return $this->s3->getObjectUrl($bucket, $remote_file);
		}
		
		return $this->getPreSignedUrl($remote_file, $expires);
	}
}
