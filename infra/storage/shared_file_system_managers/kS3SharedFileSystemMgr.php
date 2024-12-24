<?php
/**
 * Created by IntelliJ IDEA.
 * User: yossi.papiashvili
 * Date: 5/26/19
 * Time: 4:19 PM
 */

// AWS SDK PHP Client Library
require_once(dirname(__FILE__) . '/../../../vendor/aws/aws-autoloader.php');
require_once(dirname(__FILE__) . '/kSharedFileSystemMgr.php');
require_once(dirname(__FILE__) . '/../RefreshableRole.class.php');

use Aws\S3\S3Client;
use Aws\Sts\StsClient;

use Aws\Credentials\CredentialProvider;
use Aws\DoctrineCacheAdapter;
use Doctrine\Common\Cache\FilesystemCache;

use Aws\S3\Exception\S3Exception;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3MultipartUploadException;

class kS3SharedFileSystemMgr extends kSharedFileSystemMgr
{
	const MULTIPART_UPLOAD_MINIMUM_FILE_SIZE = 5368709120;
	const MAX_PARTS_NUMBER = 10000;
	const MIN_PART_SIZE = 5242880;

	const GET_EXCEPTION_CODE_FUNCTION_NAME = "getAwsErrorCode";
	const AWS_404_ERROR = "NotFound";
	const AWS_404_NO_SUCH_KEY = 'NoSuchKey';

	const S3_ARN_ROLE_ENV_NAME = "S3_ARN_ROLE";
	const DEFAULT_S3_APP_NAME = "Kaltura-Server";
	
	protected $filesAcl;
	protected $s3Region;
	protected $sseType;
	protected $sseKmsKeyId;
	protected $signatureType;
	protected $endPoint;
	protected $accessKeySecret;
	protected $accessKeyId;
	protected $storageClass;
	protected $concurrency;
	protected $userAgentRegex;
	protected $userAgentPartner;
	protected $multiPartUploadState;
	
	/* @var S3Client $s3Client */
	protected $s3Client;
	
	protected $retriesNum;
	
	protected $s3Arn;
	
	// instances of this class should be created usign the 'getInstance' of the 'kLocalFileSystemManger' class
	public function __construct(array $options = null)
	{
		parent::__construct($options);
		
		$arnRole = getenv(self::S3_ARN_ROLE_ENV_NAME);
		if(!$options || (is_array($options) && !count($options)))
		{
			$options = kConf::get('storage_options', 'cloud_storage', null);
			$arnRole = kConf::get("s3Arn" , "cloud_storage", null);
		}
		
		if($options)
		{
			$this->filesAcl = isset($options['filesAcl']) ? $options['filesAcl'] : null;
			$this->s3Region = isset($options['s3Region']) ? $options['s3Region'] : null;
			$this->sseType = isset($options['sseType']) ? $options['sseType'] : null;
			$this->sseKmsKeyId = isset($options['sseKmsKeyId']) ? $options['sseKmsKeyId'] : null;
			$this->signatureType = isset($options['signatureType']) ? $options['signatureType'] : null;
			$this->endPoint = isset($options['endPoint']) ? $options['endPoint'] : null;
			$this->accessKeySecret = isset($options['accessKeySecret']) ? $options['accessKeySecret'] : null;
			$this->accessKeyId = isset($options['accessKeyId']) ? $options['accessKeyId'] : null;
			$this->s3Arn = isset($options['arnRole']) ? $options['arnRole'] : $arnRole;
			$this->userAgentRegex = isset($options['userAgentRegex']) ? $options['userAgentRegex'] : null;
		}
		
		$this->userAgentPartner = isset($options['userAgentPartner']) ? $options['userAgentPartner'] : "Kaltura";
		$this->concurrency = isset($options['concurrency']) ? $options['concurrency'] : 1;
		$this->storageClass = isset($options['storageClass']) ? $options['storageClass'] : 'INTELLIGENT_TIERING';
		$this->retriesNum = kConf::get('aws_client_retries', 'local', 3);
		return $this->login();
	}
	private function getClientUserAgent()
	{
		$appName = self::DEFAULT_S3_APP_NAME;
		$hostName = (class_exists('kCurrentContext') && isset(kCurrentContext::$host)) ? kCurrentContext::$host : gethostname();
		if($this->userAgentRegex && preg_match($this->userAgentRegex, $hostName, $matches) && isset($matches[0]))
		{
			$appName = $matches[0];
		}

		return "APN/1.0 $this->userAgentPartner/1.0 $appName/1.0";
	}
	
	private function login()
	{
		if(!class_exists('Aws\S3\S3Client'))
		{
			self::safeLog('Class Aws\S3\S3Client was not found!!');
			return false;
		}
		
		if($this->s3Arn)
		{
			if(!class_exists('Aws\Sts\StsClient'))
			{
				self::safeLog('Class Aws\S3\StsClient was not found!!');
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
		return $this->generateStaticCredsClient();
	}

	private function generateCachedCredentialsS3Client()
	{
		$cacheProviderCredentials = RefreshableRole::getCacheCredentialsProvider($this->s3Arn, $this->s3Region);
		$config = $this->getBaseClientConfig();
		$config['credentials'] = $cacheProviderCredentials;
		$this->s3Client = S3Client::factory($config);

		return true;
	}

	private function generateStaticCredsClient()
	{
		$config = $this->getBaseClientConfig();
		$config['credentials'] = array(
			'key'    => $this->accessKeyId,
			'secret' => $this->accessKeySecret,
		);

		$this->s3Client = S3Client::factory($config);

		return true;
	}

	private function getBaseClientConfig()
	{
		$config = array(
			'region' => $this->s3Region,
			'signature' => $this->signatureType ? $this->signatureType : 'v4',
			'version' => '2006-03-01',
			'ua_append' => array($this->getClientUserAgent())
		);

		if ($this->endPoint)
			$config['endpoint'] = $this->endPoint;

		return $config;
	}

	protected function getBucketAndFilePath($filePath)
	{
		return explode("/",ltrim($filePath,"/"),2);
	}
	
	protected function doCreateDirForPath($filePath)
	{
		$dirname = dirname($filePath);
		if (!$this->doIsDir($dirname))
		{
			return $this->doMkdir($dirname);
		}
		
		return true;
	}
	
	protected function doCheckFileExists($filePath)
	{
		list($bucket, $key) = $this->getBucketAndFilePath($filePath);
		
		try
		{
			$exists = $this->s3Client->doesObjectExist($bucket, $key);
		}
		catch(Exception $e)
		{
			return false;
		}
		
		return $exists;
	}
	
	protected function doGetFileContent($filePath, $from_byte = 0, $to_byte = -1)
	{
		if($to_byte > 0)
		{
			return $this->getSpecificObjectRange($filePath, $from_byte, $to_byte);
		}
		
		$response = $this->s3Call('getObject', null, $filePath);
		
		if($response)
		{
			return (string)$response['Body'];
		}
		
		return $response;
	}
	
	protected function doUnlink($filePath)
	{
		$response = $this->s3Call('deleteObject', null, $filePath);
		
		if($response)
		{
			return true;
		}
		return false;
	}

	protected function doPutFileContentAtomic($filePath, $fileContent, $flags = 0, $context = null)
	{
		return $this->doPutFileContent($filePath, (string)$fileContent);
	}
	
	private function doPutFileHelper($filePath , $fileContent, $params)
	{
		$params['StorageClass'] = $this->storageClass;
		$params['concurrency'] = $this->concurrency;
		
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		try
		{
			$res = $this->s3Client->upload($bucket,
				$filePath,
				$fileContent,
				$this->filesAcl,
				array('params' => $params) + (isset($this->multiPartUploadState) ? array('state' => $this->multiPartUploadState) : array())
			);
			
			KalturaLog::debug("File uploaded to s3, info: " . print_r($res, true));
			return array(true, $res);
		}
		catch (Exception $e)
		{
			KalturaLog::warning("Failed to uploaded to s3, info with message: " . $e->getMessage());
			
			if ($e instanceof S3MultipartUploadException)
			{
				KalturaLog::debug("S3MultiPartUpload exception - attempting to resume failed parts");
				$this->multiPartUploadState = $e->getState();
				
				// To resume S3MultiPartUpload, if the 'body' ($fileContent) is a resource of type 'stream'
				// Need to rewind the pointer (https://docs.aws.amazon.com/en_us/aws-sdk-php/guide/latest/service/s3-multipart-upload.html)
				if (is_resource($fileContent) && get_resource_type($fileContent) === 'stream')
				{
					rewind($fileContent);
				}
			}
			
			return array(false, $e->getMessage());
		}
	}
	
	protected function doPutFileContent($filePath, $fileContent, $flags = 0, $context = null)
	{
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
		
		$retries = $this->retriesNum;
		while ($retries > 0)
		{
			list($success, $res) = @($this->doPutFileHelper($filePath, $fileContent, $params));
			if ($success)
				return $res;
			
			$retries--;
		}

		KalturaLog::err("put file content failed with error: {$res}");

		return false;
	}

	protected function doCopy($fromFilePath, $toFilePath)
	{
		$params = $this->initBasicS3Params($toFilePath);
		$params['CopySource'] = $fromFilePath;
		
		$response = $this->s3Call('copyObject', $params);
		
		if($response)
		{
			return true;
		}
		return false;
	}
	
	protected function doRename($filePath, $newFilePath)
	{
		if(kFile::isSharedPath($filePath) && !$this->doCopy($filePath, $newFilePath))
		{
			return false;
		}
		
		if(!$this->doMoveLocalToShared($filePath, $newFilePath, true))
		{
			return false;
		}
		
		kFile::unlink($filePath);
		return true;
	}
	
	protected function doGetFileFromResource($resource, $destFilePath = null, $allowInternalUrl = false)
	{
		kSharedFileSystemMgr::restoreStreamWrappers();
		
		$sourceFH = fopen($resource, 'rb');
		if(!$sourceFH)
		{
			self::safeLog("Could not open source file [$resource] for read");
			kSharedFileSystemMgr::unRegisterStreamWrappers();
			return false;
		}
		
		$uploadId = $this->createMultipartUpload($destFilePath);
		if(!$uploadId)
		{
			kSharedFileSystemMgr::unRegisterStreamWrappers();
			return false;
		}
		
		self::safeLog("Starting multipart upload for [$resource] to [$destFilePath] with upload id [$uploadId]");
		
		// Upload the file in parts.
		$partNumber = 1;
		$parts = array();
		while (!feof($sourceFH))
		{
			$srcContent = stream_get_contents($sourceFH, 16 * 1024 * 1024);
			$result = $this->multipartUploadPartUpload($uploadId, $partNumber, $srcContent, $destFilePath);
			if(!$result)
			{
				kSharedFileSystemMgr::unRegisterStreamWrappers();
				$this->abortMultipartUpload($destFilePath, $uploadId);
				return false;
			}
			
			$parts['Parts'][$partNumber] = array(
				'PartNumber' => $partNumber,
				'ETag' => $result['ETag'],
			);
			
			self::safeLog("Uploading part [$partNumber] dest file path [$destFilePath]");
			$partNumber++;
		}
		
		fclose($sourceFH);
		
		// Complete the multipart upload.
		$result = $this->completeMultiPartUpload($destFilePath, $uploadId, $parts);
		if(!$result)
		{
			kSharedFileSystemMgr::unRegisterStreamWrappers();
			return false;
		}
		
		kSharedFileSystemMgr::unRegisterStreamWrappers();
		return true;
	}
	
	protected function doFullMkdir($path, $rights = 0755, $recursive = true)
	{
		return true;
	}
	
	protected function doFullMkfileDir($path, $rights = 0777, $recursive = true)
	{
		return true;
	}
	
	protected function doMoveFile($from, $to, $override_if_exists = false, $copy = false)
	{
		$from = kFileBase::fixPath($from);
		$to = kFileBase::fixPath($to);
		
		if(!$this->doCheckFileExists($from))
		{
			KalturaLog::err("file [$from] does not exist locally or on external storage");
			return false;
		}
		if (strpos($to, '\"') !== false)
		{
			KalturaLog::err("Illegal destination file [$to]");
			return false;
		}
		return $this->copyRecursively($from, $to, !$copy);
	}
	
	protected function doDeleteFile($file_name)
	{
		$this->doUnlink($file_name);
	}
	
	protected function doIsDir($path)
	{
		//Object storage does use directories so to determine if path is dir or not we simply list the path on s3
		//If it returns more than 1 match than its a directory
		$dirList = array();
		
		//When checking if path is Dir in s3 add a trailing slash to the path to avoid considering files with the same name but different ext as dir's
		// Example:
		//  my_bucket/dir1/dir2/my_file.mp4
		//  my_bucket/dir1/dir2/my_file.mp4.log
		$path = $path . '/';
		list($bucket, $key) = $this->getBucketAndFilePath($path);
		try
		{
			$dirListObjectsRaw = $this->s3Client->getIterator('ListObjects', array(
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
			self::safeLog("Couldn't determine if path [$path] is dir: {$e->getMessage()}");
		}
		return false;
	}
	
	protected function getHeadObjectForPath($path)
	{
		$res = $this->s3Call('headObject', null, $path, array(self::AWS_404_ERROR));
		
		if(!$res)
		{
			return false;
		}
		return $res;
	}
	
	protected function doMkdir($path, $mode, $recursive)
	{
		return true;
	}
	
	protected function doCopySingleFile($src, $dest, $deleteSrc)
	{
		if(!kFile::isSharedPath($src))
		{
			return $this->copyFileLocalToShared($src, $dest, $deleteSrc);
		}
		
		return $this->copyFileFromShared($src, $dest, $deleteSrc);
	}
	
	protected function copyFileLocalToShared($src, $dest, $deleteSrc)
	{
		$result = $this->doGetFileFromResource($src, $dest);
		if (!$result)
		{
			KalturaLog::err("Failed to upload file: [$src] to [$dest]");
			return false;
		}
		if ($deleteSrc && (!unlink($src)))
		{
			KalturaLog::err("Failed to delete source file : [$src]");
			return false;
		}
		
		return true;
	}
	
	protected function copyFileFromShared($src, $dest, $deleteSrc)
	{
		if(kFile::isSharedPath($dest))
		{
			$result = $this->doCopy($src, $dest);
		}
		else
		{
			$result = $this->copySharedToLocal($src, $dest);
		}
		
		if ($result && $deleteSrc)
		{
			return $this->doDeleteFile($src);
		}
		
		KalturaLog::debug("Copy file from shared result [$result] ");
		return $result;
	}
	
	protected function doRmdir($path)
	{
		list($bucket, $filePathWithoutBucket) = $this->getBucketAndFilePath($path);
		try
		{
			$this->s3Client->deleteMatchingObjects($bucket, $filePathWithoutBucket);
		}
		catch (Exception $e)
		{
			KalturaLog::err("Error trying to remove dir [$path] from bucket [$bucket]: {$e->getMessage()}");
			return false;
		}
		return true;
	}
	
	protected function doChmod($path, $mode)
	{
		return true;
	}
	
	protected function doFileSize($filename)
	{
		$result = $this->s3Call('headObject', null, $filename, array(self::AWS_404_ERROR));
		
		if(!$result)
		{
			return false;
		}
		
		return $result->get('ContentLength');
	}
	
	public function createMultipartUpload($destFilePath)
	{
		$result = $this->s3Call('createMultipartUpload', null, $destFilePath);
		
		if(!$result)
		{
			return false;
		}
		
		$uploadId = $result['UploadId'];
		KalturaLog::debug("multipart upload started to [$destFilePath] with upload id [$uploadId]");
		return $uploadId;
	}
	
	public function multipartUploadPartCopy($uploadId, $partNumber, $s3FileKey, $destFilePath)
	{
		$params = $this->initBasicS3Params($destFilePath);
		$params['CopySource'] = $s3FileKey;
		$params['UploadId'] = $uploadId;
		$params['PartNumber'] = $partNumber;
		
		$result = $this->s3Call('uploadPartCopy', $params);
		
		if(!$result)
		{
			return false;
		}
		KalturaLog::debug("copied part [$partNumber] from [$s3FileKey]. dest file path [$destFilePath]");
		return $result;
	}
	
	public function abortMultipartUpload($path, $uploadId)
	{
		$params = $this->initBasicS3Params($path);
		$params['UploadId'] = $uploadId;
		
		$result = $this->s3Call('abortMultipartUpload', $params);
		
		if(!$result)
		{
			return false;
		}
		return true;
	}
	
	public function completeMultiPartUpload($destFilePath, $uploadId, $parts)
	{
		$params = $this->initBasicS3Params($destFilePath);
		$params['UploadId'] = $uploadId;
		$params['MultipartUpload'] = $parts;
		
		$result = $this->s3Call('completeMultipartUpload', $params);
		
		if(!$result)
		{
			return false;
		}
		return $result['Location'];
	}

	protected function doListFiles($filePath, $pathPrefix = '', $recursive = true, $fileNamesOnly = false)
	{
		$dirList = array();
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);

		try
		{
			$dirListObjectsRaw = $this->s3Client->getIterator('ListObjects', array(
				'Bucket' => $bucket,
				'Prefix' => $filePath
			));

			$originalFilePath = $bucket . '/' . $filePath . '/';
			foreach ($dirListObjectsRaw as $dirListObject)
			{
				$fullPath = '/' . $bucket . '/' . $dirListObject['Key'];
				$fileName = $pathPrefix.basename($fullPath);
				if($originalFilePath == $fullPath)
					continue;

				$fileType = "file";
				if($dirListObject['Size'] == 0 && substr_compare($fullPath, '/', -strlen('/')) === 0)
				{
					$fileType = 'dir';
				}

				if ($fileType == 'dir')
				{
					$dirList[] = $fileNamesOnly ?  $fileName : array($fileName, 'dir', $dirListObject['Size']);
					if( $recursive)
					{
						$dirList = array_merge($dirList, self::doListFiles($fullPath, $pathPrefix, $fileNamesOnly));
					}
				}
				else
				{
					$dirList[] = $fileNamesOnly ? $fileName : array($fileName, 'file', $dirListObject['Size']);
				}
			}
		}
		catch ( Exception $e )
		{
			self::safeLog("Couldn't list file objects for remote path, [$filePath] from bucket [$bucket]: {$e->getMessage()}");
		}

		return $dirList;
	}

	protected function doGetMaximumPartsNum()
	{
		return self::MAX_PARTS_NUMBER;
	}
	
	protected function doGetUploadMinimumSize()
	{
		return self::MIN_PART_SIZE;
	}
	
	protected function doGetUploadMaxSize()
	{
		return self::MULTIPART_UPLOAD_MINIMUM_FILE_SIZE;
	}
	
	protected function doIsFile($filePath)
	{
		$res = $this->getHeadObjectForPath($filePath);
		if(!$res)
		{
			return false;
		}
		
		$contentLength = $res->get('ContentLength');
		return ($contentLength != 0 && substr($filePath, -1) != "/") ? true : false;
	}
	
	protected function doRealPath($filePath, $getRemote = true)
	{
		if(!$getRemote)
			return $filePath;
		
		$params = $this->initBasicS3Params($filePath);
		
		$cmd = $this->s3Client->getCommand('GetObject', $params);

		$request = $this->s3Client->createPresignedRequest($cmd, time() + 5 * 86400);
		return (string)$request->getUri();;
	}
	
	protected function doMimeType($filePath)
	{
		$res = $this->getHeadObjectForPath($filePath);
		if(!$res)
		{
			return null;
		}
		
		return $res->get('ContentType');
	}
	
	protected function doDumpFilePart($filePath, $range_from, $range_length)
	{
		$fileUrl = $this->doRealPath($filePath);
		
		$serveRedirectToStorageSecret = kConf::get("proxy_redirect_to_storage_secret", "local", null);
		if ($serveRedirectToStorageSecret && isset($_SERVER['HTTP_X_KALTURA_SERVE_REDIRECT_TO_STORAGE']) && $_SERVER['HTTP_X_KALTURA_SERVE_REDIRECT_TO_STORAGE'] === $serveRedirectToStorageSecret)
		{
			header("X-Kaltura-Serve-Redirect-To-Storage: $serveRedirectToStorageSecret");
			header("X-Accel-Redirect: /storage/" . preg_replace('#^https?://#', '', $fileUrl));
			return;
		}
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $fileUrl);
		curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		$range_to = ($range_from + $range_length) - 1;
		curl_setopt($ch, CURLOPT_RANGE, "$range_from-$range_to");
		
		$result = curl_exec($ch);

//		$defaultChunkSize = 500000;
//		$fileSize = $this->doFileSize($filePath);
//		while($range_length >= 0)
//		{
//			$chunkSize = min($defaultChunkSize, $range_length);
//			$range_to = min($range_from + $chunkSize, $fileSize);
//			$content = $this->getSpecificObjectRange($filePath, $range_from, $range_to);
//			echo $content;
//			$range_length -= $chunkSize;
//			$range_from = $range_from + $chunkSize + 1;
//		}
	}
	
	protected function getSpecificObjectRange($filePath, $startRange, $endRange)
	{
		$params = $this->initBasicS3Params($filePath);
		$params['Range'] = "bytes=$startRange-$endRange";
		
		$response = $this->s3Call('getObject', $params);
		
		if(!$response)
		{
			return false;
		}
		
		return (string)$response['Body'];
	}
	
	protected function doChgrp($filePath, $contentGroup)
	{
		return true;
	}
	
	protected function doDir($filePath)
	{
		return null;
	}
	
	protected function doChown($path, $user, $group)
	{
		return true;
	}
	
	protected function doFilemtime($filePath)
	{
		$result = $this->s3Call('getObject', null, $filePath, array(self::AWS_404_NO_SUCH_KEY));
		
		if(!$result)
		{
			return false;
		}
		return strtotime($result['LastModified']);
	}
	
	protected function doMoveLocalToShared($src, $dest, $copy = false)
	{
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
		
		$fp = fopen($src, 'r');
		if(!$fp)
		{
			KalturaLog::err("Failed to open file: [$src]");
			return false;
		}
		
		$retries = $this->retriesNum;
		while ($retries > 0)
		{
			list($success, $res) = $this->doPutFileHelper($dest, $fp, $params);
			if ($success)
			{
				break;
			}
			
			sleep(rand(1,3));
			$retries--;
		}
		
		//Silence error to avoid warning caused by file handle being changed by the s3 client upload action
		if(is_resource($fp))
		{
			@fclose($fp);
		}
		
		if (!$success)
		{
			KalturaLog::err("Failed to upload file: [$src] to [$dest]");
			return false;
		}
		if (!$copy && (!unlink($src)))
		{
			KalturaLog::err("Failed to delete source file : [$src]");
			return false;
		}
		
		return $success;
	}
	
	public function getListObjectsPaginator($filePath)
	{
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		
		$paginator = $this->s3Client->getPaginator('ListObjects', array(
			'Bucket' => $bucket,
			'Prefix' => $filePath
		));
		
		return $paginator;
	}
	
	protected function doCopyDir($src, $dest, $deleteSrc)
	{
		$paginator = $this->getListObjectsPaginator($src);
		list($bucket, $filePath) = $this->getBucketAndFilePath($src);
		
		foreach ($paginator as $page)
		{
			foreach ($page['Contents'] as $object)
			{
				if(kFile::isDir($object['Key']))
				{
					KalturaLog::err("Copying of non-flat directories is illegal");
					return false;
				}
				
				$fileName = basename($object['Key']);
				
				$res = kFile::copySingleFile ("/$bucket/{$object['Key']}", $dest . DIRECTORY_SEPARATOR . $fileName , $deleteSrc);
				if (! $res)
				{
					return false;
				}
			}
		}
		return true;
	}
	
	protected function copySharedToLocal($src, $dest)
	{
		$params = $this->initBasicS3Params($src);
		$params['SaveAs'] = $dest;
		
		$result = $this->s3Call('getObject', $params);
		
		if(!$result)
		{
			return false;
		}
		return $result;
	}
	
	public function multipartUploadPartUpload($uploadId, $partNumber, &$srcContent, $destFilePath)
	{
		$params = $this->initBasicS3Params($destFilePath);
		$params['Body'] = $srcContent;
		$params['UploadId'] = $uploadId;
		$params['PartNumber'] = $partNumber;
		
		$result = $this->s3Call('uploadPart', $params);
		gc_collect_cycles();
		
		if(!$result)
		{
			return false;
		}
		KalturaLog::debug("uploaded part [$partNumber]. dest file path [$destFilePath]");
		return $result;
	}
	
	protected function s3Call($command, $params = null, $filePath = null, $finalErrorCodes = array())
	{
		if(!$params && $filePath)
		{
			$params = $this->initBasicS3Params($filePath);
		}

		$retries = $this->retriesNum;
		while ($retries > 0)
		{
			try
			{
				$result = $this->s3Client->{$command}($params);
				return $result;
			}
			catch (S3Exception $e)
			{
				$getExceptionFunctionName = self::GET_EXCEPTION_CODE_FUNCTION_NAME;
				$retries--;
				if(in_array($e->$getExceptionFunctionName(), $finalErrorCodes))
				{
					$retries = 0;
					//In case final status is passed dont log the exception to avoid spamming the log file
					return false;
				}
				$this->handleS3Exception($command, $retries, $params, $e);
			}
		}
		
		return false;
	}
	
	public function initBasicS3Params($filePath)
	{
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		
		$params = array(
			'Bucket' => $bucket,
			'Key'    => $filePath,
		);
		return $params;
	}
	
	protected function handleS3Exception($command, $retries, $params, $e)
	{
		// don't print body to logs
		if(isset($params['Body']));
		{
			unset($params['Body']);
		}
		
		self::safeLog("S3 [$command] command failed. Retries left: [$retries] Params: " . print_r($params, true)."\n{$e->getMessage()}");
	}
	
	protected function doCopySharedToSharedAllowed()
	{
		return false;
	}
	
	protected function doShouldPollFileExists()
	{
		return false;
	}
}
