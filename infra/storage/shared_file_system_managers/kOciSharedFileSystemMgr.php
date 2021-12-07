<?php
/**
 * Created by IntelliJ IDEA.
 * User: yossi.papiashvili
 * Date: 5/26/19
 * Time: 4:19 PM
 */

// AWS SDK PHP Client Library
require_once(dirname(__FILE__) . '/../../../vendor/oci/vendor/autoload.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/Auth/AuthProviderInterface.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/OciResponse.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/Regions.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/UserAgent.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/HttpUtils.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/ObjectStorage/ObjectStorageClient.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/ObjectStorage/ObjectStorageAsyncClient.php');
require_once(dirname(__FILE__) . '/kSharedFileSystemMgr.php');

use Oracle\Oci\Common\HttpUtils;
use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\Common\OciException;
use Oracle\Oci\Common\OciBadResponseException;
use Oracle\Oci\Common\AbstractClient;
use Oracle\Oci\Common\ConfigFile;
use Oracle\Oci\Common\Logging\EchoLogAdapter;
use Oracle\Oci\Common\Auth\ConfigFileAuthProvider;
use Oracle\Oci\Common\Auth\InstancePrincipalsAuthProvider;
use Oracle\Oci\Common\Auth\CachingRequestingAuthProvider;

use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use Oracle\Oci\ObjectStorage\Transfer\UploadManager;
use Oracle\Oci\ObjectStorage\Transfer\MultipartUploadException;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Oracle\Oci\ObjectStorage\Transfer\UploadManagerRequest;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use GuzzleHttp\Exception\ClientException;
use Cache\Adapter\Filesystem\FilesystemCachePool;


class kOciSharedFileSystemMgr extends kSharedFileSystemMgr
{
	const MULTIPART_UPLOAD_MINIMUM_FILE_SIZE = 5368709120;
	const MAX_PARTS_NUMBER = 10000;
	const MIN_PART_SIZE = 5242880;
	const CHUNK_SIZE = 102400;
	const DEFAULT_APP_NAME = "Kaltura-Server";
	
	const GET_EXCEPTION_CODE_FUNCTION_NAME = "getCode";
	const COPY_OBJECT_STATUS_ACCEPTED = 'ACCEPTED';
	const COPY_OBJECT_STATUS_IN_PROGRESS = 'IN_PROGRESS';
	const COPY_OBJECT_STATUS_COMPLETED = 'COMPLETED';
	const COPY_OBJECT_STATUS_FAILED = 'FAILED';
	const OS_404_ERROR = 404;
	const OS_200_VALID = 200;
	
	/* @var ObjectStorageAsyncClient $objectStoarageAsyncClient */
	protected $objectStorageAsyncClient;
	
	/* @var ObjectStorageClient $objectStoargeClient */
	protected $objectStoargeClient;
	protected $retriesNum;
	protected $concurrency;
	
	protected $region;
	protected $namespaceName;
	protected $userAgentRegex;
	protected $userAgentPartner;
	protected $configFileLocation;
	
	// instances of this class should be created usign the 'getInstance' of the 'kLocalFileSystemManger' class
	public function __construct(array $options = null)
	{
		parent::__construct($options);
		if(!$options || (is_array($options) && !count($options)))
		{
			$options = kConf::get('storage_options', 'cloud_storage', null);
		}
		
		if($options)
		{
			$this->region = isset($options['region']) ? $options['region'] : null;
			$this->namespaceName = isset($options['namespaceName']) ? $options['namespaceName'] : null;
			$this->userAgentRegex = isset($options['userAgentRegex']) ? $options['userAgentRegex'] : null;
			$this->userAgentPartner = isset($options['userAgentPartner']) ? $options['userAgentPartner'] : "Kaltura";
			$this->configFileLocation = isset($options['configFileLocation']) ? $options['configFileLocation'] : null;
		}
		
		$this->concurrency = isset($options['concurrency']) ? $options['concurrency'] : 1;
		$this->retriesNum = kConf::get('aws_client_retries', 'local', 3);
		return $this->login();
	}
	
	private function login()
	{
		if(!class_exists('Oracle\Oci\ObjectStorage\ObjectStorageClient'))
		{
			self::safeLog('Class ObjectStorageClient was not found!!');
			return false;
		}
		
		if($this->configFileLocation)
		{
			$authProvider = new ConfigFileAuthProvider(ConfigFile::loadFromFile($this->configFileLocation));
		}
		else
		{
			$filesystemAdapter = new Local("/tmp/oci_creds_cache");
			$filesystem = new Filesystem($filesystemAdapter);
			$cache = new FilesystemCachePool($filesystem);
			$uncachedAuthProvider = new InstancePrincipalsAuthProvider();
			$authProvider = new CachingRequestingAuthProvider($uncachedAuthProvider, $cache);
		}
		
		$this->objectStoargeClient = new ObjectStorageClient($authProvider,  $this->region);
		$this->objectStorageAsyncClient = new ObjectStorageAsyncClient($authProvider, $this->region);
		UserAgent::setAdditionalClientUserAgent($this->getKalturaCustomUserAgent());
	}
	
	protected function getKalturaCustomUserAgent()
	{
		$appName = self::DEFAULT_APP_NAME;
		$hostName = (class_exists('kCurrentContext') && isset(kCurrentContext::$host)) ? kCurrentContext::$host : gethostname();
		if($this->userAgentRegex && preg_match($this->userAgentRegex, $hostName, $matches) && isset($matches[0]))
		{
			$appName = $matches[0];
		}
		
		return "APN/1.0 $this->userAgentPartner/1.0 $appName/1.0";
	}
	
	protected function doCreateDirForPath($filePath)
	{
		return true;
	}
	
	protected function doCheckFileExists($filePath)
	{
		return $this->getHeadObjectForPath($filePath);
	}
	
	protected function doGetFileContent($filePath, $from_byte = 0, $to_byte = -1)
	{
		if($to_byte > 0)
		{
			return $this->getSpecificObjectRange($filePath, $from_byte, $to_byte);
		}
		
		$response = $this->osCall('getObject', null, $filePath);
		if(!$response)
		{
			return '';
		}
		
		return (string)$response->getBody();
	}
	
	protected function doUnlink($filePath)
	{
		$response = $this->osCall('deleteObject', null, $filePath);
		if($response)
		{
			return true;
		}
		return false;
	}
	
	protected function doPutFileContentAtomic($filePath, $fileContent)
	{
		return $this->doPutFileContent($filePath, (string)$fileContent);
	}
	
	private function doPutFileHelper($filePath , $fileContent)
	{
		list($bucketName, $fileName) = $this->getBucketAndFilePath($filePath);
		
		$uploadManagerRequest = UploadManagerRequest::createUploadManagerRequest(
			$this->namespaceName,
			$bucketName,
			$fileName,
			$fileContent
		);
		
		$uploadManager = new UploadManager($this->objectStorageAsyncClient);
		$retries = $this->retriesNum;
		$resumeInfo = null;
		
		while ($retries > 0)
		{
			try
			{
				if (!$resumeInfo)
				{
					$uploadPromise = $uploadManager->upload($uploadManagerRequest);
					$res = $uploadPromise->wait();
				}
				else
				{
					$resumePromise = $uploadManager->resumeUploadFromResumeInfo($resumeInfo);
					$res = $resumePromise->wait();
				}
				
				self::safeLog("File uploaded to OS, info: " . print_r($res, true));
				return array(true, $res);
			}
			catch (MultipartUploadException $e)
			{
				$resumeInfo = $e->getMultipartResumeInfo();
				$retries--;
				self::safeLog("Caught MultipartUploadException, retries left [$retries] info: ". $e->getFailureExceptions());
				KalturaMonitorClient::sleep(rand(1,3));
			}
			catch (OciBadResponseException $e)
			{
				$retries--;
				self::safeLog("Caught OciBadResponseException, retries left [$retries] info: ". $e->getMessage());
			}
			catch (Exception $e)
			{
				$retries--;
				self::safeLog("Caught generic exception, retries left [$retries] info: ". $e->getMessage());
			}
			
		}
		
		self::safeLog("Failed to upload to OS, info with message: " . $e->getMessage());
		return array(false, $e);
	}
	
	protected function doPutFileContent($filePath, $fileContent, $flags = 0, $context = null)
	{
		list($success, $res) = @($this->doPutFileHelper($filePath, $fileContent));
		if ($success)
			return $res;
		
		self::safeLog("Put file content failed with error: {$res->getMessage()}");
		return false;
	}
	
	protected function doRename($filePath, $newFilePath)
	{
		if (kFile::isSharedPath($filePath))
		{
			list($bucket, $fileName) = $this->getBucketAndFilePath($filePath);
			list($toBucket, $newFileName) = $this->getBucketAndFilePath($newFilePath);
			
			$params = array(
				'namespaceName' => $this->namespaceName,
				'bucketName' => $bucket,
				'renameObjectDetails' => array(
					'sourceName' => $fileName,
					'newName' => $newFileName
				)
			);
			
			$response = $this->objectStoargeClient->renameObject($params);
			$statusCode = $response->getStatusCode();
			if ($statusCode != self::OS_200_VALID)
			{
				$headers = $response->getHeaders();
				$opcRequestId = $headers['opc-request-id'][0];
				self::safeLog("Failed to rename [$filePath] to [$newFilePath] OS returned status [$statusCode] OPC Request ID [$opcRequestId] body: " . print_r($response->getBody(), 1));
				return false;
			}
			return true;
		}

		return $this->doMoveLocalToShared($filePath, $newFilePath);
	}
	
	protected function doCopy($fromFilePath, $toFilePath)
	{
		$params = $this->getCopyParams($fromFilePath, $toFilePath);
		$response = $this->osCall('copyObject', $params);

		if (!$response)
		{
			self::safeLog('Copy Object Failed');
			return false;
		}

		// OS copyObject works asynchronously need to check for status
		$headers = $response->getHeaders();
		$workRequestId = $headers['opc-work-request-id'][0];
		$params['workRequestId'] = $workRequestId;
		self::safeLog("Copy request sent to OS from [$fromFilePath] to [$toFilePath] OS Work Request Id [$workRequestId]");
		
		list($validWorkRequest, $status) = $this->validateWorkRequestAccepted($params);
		
		if(!$validWorkRequest)
		{
			self::safeLog("Failed to copy, status [$status] for OS Work Request Id [$workRequestId]");
			return false;
		}
		
		self::safeLog("Copy request status = [$status] for OS Work Request Id [$workRequestId]");
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

		$multiPartParams = $this->getMultipartUploadParams($destFilePath);

		$responseJsonObject = $this->objectStoargeClient->createMultipartUpload($multiPartParams)->getJson();
		$uploadId = $responseJsonObject->uploadId;
		if(!$uploadId)
		{
			self::safeLog("Failed to get Multipart Upload ID - aborting");
			kSharedFileSystemMgr::unRegisterStreamWrappers();
			return false;
		}

		self::safeLog("Starting multipart upload for [$resource] to [$destFilePath] with upload id [$uploadId]");

		// Upload the file in parts.
		$partNumber = 1;
		$parts = array();
		$params = $this->getUploadPartParams($destFilePath, $uploadId);

		while (!feof($sourceFH))
		{
			$srcContentPart = stream_get_contents($sourceFH, 16 * 1024 * 1024);

			$params['uploadPartNum'] = $partNumber;
			$params['uploadPartBody'] = $srcContentPart;
			$result = $this->objectStoargeClient->uploadPart($params);

			if(!$result)
			{
				kSharedFileSystemMgr::unRegisterStreamWrappers();
				$this->objectStoargeClient->abortMultipartUpload($params);
				return false;
			}

			$resHeaders = $result->getHeaders();

			$parts['partsToCommit'][] = array(
				'partNum' => $partNumber,
				'etag' => $resHeaders['etag'][0]
			);
			self::safeLog("Uploading part [$partNumber] dest file path [$destFilePath]");
			$partNumber++;
		}
		fclose($sourceFH);

		// Commit the multipart upload.
		$params['commitMultipartUploadDetails'] = $parts;
		$result = $this->objectStoargeClient->commitMultipartUpload($params);

		kSharedFileSystemMgr::unRegisterStreamWrappers();
		if(!$result)
		{
			return false;
		}
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
			self::safeLog("file [$from] does not exist locally or on external storage");
			return false;
		}
		if (strpos($to, '\"') !== false)
		{
			self::safeLog("Illegal destination file [$to]");
			return false;
		}
		return $this->copyRecursively($from, $to, !$copy);
	}

	protected function doIsDir($path)
	{
		//Object storage does use directories so to determine if path is dir or not we simply list the path on OS
		//If it returns more than 1 match than its a directory
		//When checking if path is Dir in OS add a trailing slash to the path to avoid considering files with the same name but different ext as dir's
		// Example:
		//  my_bucket/dir1/dir2/my_file.mp4
		//  my_bucket/dir1/dir2/my_file.mp4.log
		$path = $path . '/';
		$params = $this->initBasicOciParams($path);
		$params['prefix'] = $params['objectName'];

		try
		{
			$dirListObjects = $this->objectStoargeClient->listObjects($params)->getJson();
			foreach ($dirListObjects->objects as $dirListObject)
			{
				return true;
			}
		}
		catch ( Exception $e )
		{
			//404 indicates the path does not exist
			$getExceptionFunctionName = self::GET_EXCEPTION_CODE_FUNCTION_NAME;
			$returnStatusCode = $e->$getExceptionFunctionName();
			if($returnStatusCode == self::OS_404_ERROR)
			{
				return false;
			}
			
			self::safeLog("Couldn't determine if path [$path] is dir: sc {$returnStatusCode} msg {$e->getMessage()}");
		}
		return false;
	}
	
	protected function doMkdir($path, $mode, $recursive)
	{
		return true;
	}
	
	protected function doRmdir($path)
	{
		$rmSuccess = true;
		$successMetric = array(false => 0, true => 0);
		list($bucket, $filePath) = $this->getBucketAndFilePath($path);
		
		$objectsToDelete = $this->doListFiles($path,'', true);
		foreach($objectsToDelete as $objectToDelete)
		{
			$unlinkSuccess = $this->doUnlink($objectToDelete[3]);
			$successMetric[$unlinkSuccess] = $successMetric[$unlinkSuccess]++;
			$rmSuccess = $rmSuccess && $unlinkSuccess;
		}
		
		self::safeLog("rmDir result is [$rmSuccess], success [$successMetric[0]] failed [$successMetric[1]]");
		return $rmSuccess;
	}
	
	protected function doChown($path, $user, $group)
	{
		return true;
	}
	
	protected function doChmod($path, $mode)
	{
		return true;
	}
	
	protected function doFileSize($filename)
	{
		$result = $this->getHeadObjectForPath($filename);
		if(!$result)
		{
			return false;
		}
		$headers = $result->getHeaders();
		return $headers['Content-Length'][0];
	}
	
	protected function doDeleteFile($filename)
	{
		return $this->doUnlink($filename);
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
			self::safeLog("Failed to upload file: [$src] to [$dest]");
			return false;
		}
		if ($deleteSrc && (!unlink($src)))
		{
			self::safeLog("Failed to delete source file : [$src]");
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
		
		self::safeLog("Copy file from shared result [$result] ");
		return $result;
	}
	
	protected function copySharedToLocal($src, $dest)
	{
		$bytesWritten = 0;
		list($sourceFH, $destFH) = $this->getSourceAndDestinationStreams($src, $dest);

		$start = microtime(true);
		while (!feof($sourceFH))
		{
			$data = fread($sourceFH, self::CHUNK_SIZE);
			$bytesWritten += strlen($data);
			fwrite($destFH, $data);
		}
		$totalCopyTime = microtime(true) - $start;
		self::safeLog("Took [$totalCopyTime] seconds, bytes written [$bytesWritten]");
		$this->closeSourceAndDestinationStreams($sourceFH, $destFH);

		return true;
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
	
	protected function doListFiles($filePath, $pathPrefix = '', $recursive = true, $fileNamesOnly = false)
	{
		$dirList = array();
		try
		{
			$params = $this->initBasicOciParams($filePath);
			$params['prefix'] = $params['objectName'];
			$params['fields'] = 'size';
			$bucket = $params['bucketName'];
			$dirListObjects = $this->objectStoargeClient->listObjects($params)->getJson();
			
			$originalFilePath = $bucket . '/' . $filePath . '/';
			
			foreach ($dirListObjects->objects as $dirListObject)
			{
				$fullPath = '/' . $bucket . '/' . $dirListObject->name;
				$fileName = $pathPrefix.basename($fullPath);
				if($originalFilePath == $fullPath)
					continue;
				
				$fileType = "file";
				if($dirListObject->size == 0 && substr_compare($fullPath, '/', -strlen('/')) === 0)
				{
					$fileType = 'dir';
				}
				
				if ($fileType == 'dir')
				{
					$dirList[] = $fileNamesOnly ?  $fileName : array($fileName, 'dir', $dirListObject->size, $fullPath);
					if( $recursive)
					{
						$dirList = array_merge($dirList, self::doListFiles($fullPath, $pathPrefix, $fileNamesOnly));
					}
				}
				else
				{
					$dirList[] = $fileNamesOnly ? $fileName : array($fileName, 'file', $dirListObject->size, $fullPath);
				}
			}
		}
		catch ( Exception $e )
		{
			self::safeLog("Couldn't list file objects for remote path, [$filePath] from bucket [$bucket]: {$e->getMessage()}");
		}
		
		return $dirList;
	}
	
	protected function doIsFile($filePath)
	{
		$response = $this->getHeadObjectForPath($filePath);
		if(!$response)
		{
			return false;
		}
		
		$resHeaders = $response->getHeaders();
		$contentLength = $resHeaders['Content-Length'][0];
		return ($contentLength != 0 && substr($filePath, -1) != "/") ? true : false;
	}
	
	protected function doRealPath($filePath, $getRemote = true)
	{
		if(!$getRemote)
			return $filePath;
		
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		
		$preAuthenticatedRequestDetails = array(
			'accessType' => 'ObjectRead',
			'objectName' => $filePath,
			'name' => "kaltura-$filePath",
			'timeExpires' => date('Y-m-d\TH:i:sP', strtotime('+5 days'))
		);
		
		$params = array(
			'bucketName' => $bucket,
			'namespaceName' => $this->namespaceName,
			'createPreauthenticatedRequestDetails' => $preAuthenticatedRequestDetails
		);
		
		$preSignedUrl = null;
		$retries = $this->retriesNum;
		while ($retries > 0)
		{
			try
			{
				$preSignedUrl = $this->objectStoargeClient->createPreauthenticatedRequest($params)->getJson();
				break;
			}
			catch (Exception $e)
			{
				self::safeLog("Couldn't create pre signed url for [$filePath]: {$e->getMessage()}");
				KalturaMonitorClient::sleep(rand(1,3));
				$retries--;
			}
		}
		
		if(!$preSignedUrl)
		{
			self::safeLog("No preSignedUrl object returned");
			return null;
		}
		
		return "https://objectstorage.{$this->region}.oraclecloud.com" . $preSignedUrl->accessUri;
	}
	
	protected function doMimeType($filePath)
	{
		$result = $this->getHeadObjectForPath($filePath);
		if(!$result)
		{
			return null;
		}
		
		return $result->getHeaderValue('Content-Type');
	}
	
	protected function doDumpFilePart($filePath, $range_from, $range_length)
	{
		$fileUrl = $this->doRealPath($filePath);
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $fileUrl);
		curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		$range_to = ($range_from + $range_length) - 1;
		curl_setopt($ch, CURLOPT_RANGE, "$range_from-$range_to");
		
		$result = curl_exec($ch);
	}
	
	protected function doChgrp($filePath, $contentGroup)
	{
		return true;
	}
	
	protected function doFilemtime($filePath)
	{
		$response = $this->getHeadObjectForPath($filePath);
		
		if(!$response)
		{
			return false;
		}
		$headers = $response->getHeaders();
		return $headers['last-modified'][0];
	}
	
	protected function doMoveLocalToShared($src, $dest, $copy = false)
	{
		$params = array();
		
		$fp = fopen($src, 'r');
		if(!$fp)
		{
			self::safeLog("Failed to open file: [$src]");
			return false;
		}
		
		list($success, $res) = $this->doPutFileHelper($dest, $fp);
		
		//Silence error to avoid warning caused by file handle being changed by the OS client upload action
		@fclose($fp);
		if (!$success)
		{
			self::safeLog("Failed to upload file: [$src] to [$dest]");
			return false;
		}
		if (!$copy && (!unlink($src)))
		{
			self::safeLog("Failed to delete source file : [$src]");
			return false;
		}

		return $success;
	}
	
	protected function doDir($filePath)
	{
		return null;
	}
	
	protected function doCopyDir($src, $dest, $deleteSrc)
	{
		$copySuccess = true;
		$dirObjects = $this->getListObjects($src);
		list($bucket, $filePath) = $this->getBucketAndFilePath($src);
		
		foreach ($dirObjects as $object)
		{
			if(kFile::isDir($object->name))
			{
				self::safeLog("Initiating internal dir copy from [{$object->name}] to [" . $dest . basename($object->name) . "] delSrc [$deleteSrc]");
				$copySuccess = kFile::copyDir($object->name, $dest . basename($object->name), $deleteSrc);
				continue;
			}
				
			$fileName = basename($object->name);
			
			$from = "/$bucket/{$object->name}";
			$to = $dest . DIRECTORY_SEPARATOR . $fileName;
			$res = kFile::copySingleFile($from, $to , $deleteSrc);
			if (!$res)
			{
				self::safeLog("Failed to copy file from [$from] to [$to], continue to other objects");
				$copySuccess = false;
			}
		}
		
		return $copySuccess;
	}
	
	protected function doShouldPollFileExists()
	{
		return false;
	}
	
	protected function doCopySharedToSharedAllowed()
	{
		return false;
	}
	
	protected function getHeadObjectForPath($path)
	{
		$res = $this->osCall('headObject', null, $path, array(self::OS_404_ERROR));
		if(!$res)
		{
			return false;
		}
		return $res;
	}
	
	protected function getSpecificObjectRange($filePath, $startRange, $endRange)
	{
		$params = $this->initBasicOciParams($filePath);
		$params['range'] = "bytes=$startRange-$endRange";
		
		$response = $this->osCall('getObject', $params);
		
		if(!$response)
		{
			return false;
		}
		
		return (string)$response->getBody();
	}
	
	protected function osCall($command, $params = null, $filePath = null, $finalErrorCodes = array())
	{
		if (!$params && $filePath)
		{
			$params = $this->initBasicOciParams($filePath);
		}
		
		$retries = $this->retriesNum;
		while ($retries > 0)
		{
			try
			{
				$result = $this->objectStoargeClient->{$command}($params);
				return $result;
			}
			catch (ClientException $e)
			{
				$this->handleException($command, $retries, $params, $e, $finalErrorCodes);
			}
			catch (OciBadResponseException $e)
			{
				$this->handleException($command, $retries, $params, $e, $finalErrorCodes);
			}
			catch(Exception $e)
			{
				$retries--;
				self::safeLog("Caught generic Exception " . $e->getMessage());
			}
		}
		
		return false;
	}
	
	protected function initBasicOciParams($filePath)
	{
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		
		$params = array(
			'bucketName' => $bucket,
			'objectName'    => $filePath,
			'namespaceName' => $this->namespaceName,
		);
		return $params;
	}
	
	protected function getBucketAndFilePath($filePath)
	{
		return explode("/",ltrim($filePath,"/"),2);
	}
	
	protected function handleException($command, &$retries, $params, $e, $finalErrorCodes)
	{
		$getExceptionFunctionName = self::GET_EXCEPTION_CODE_FUNCTION_NAME;
		$retries--;
		if (in_array($e->$getExceptionFunctionName(), $finalErrorCodes))
		{
			//In case final status is passed dont log the exception to avoid spamming the log file
			$retries = 0;
			return false;
		}
		
		// don't print body to logs
		if(isset($params['Body']));
		{
			unset($params['Body']);
		}
		
		self::safeLog("OS [$command] command failed. Retries left: [$retries] Params: " . print_r($params, true)."\n{$e->getMessage()}");
	}
	
	protected function getListObjects($filePath)
	{
		$params = $this->initBasicOciParams($filePath);
		$params['prefix'] = $params['objectName'];
		
		try
		{
			$dirListObjects = $this->objectStoargeClient->listObjects($params)->getJson();
		}
		catch ( Exception $e )
		{
			self::safeLog("Could not list objects for path [$filePath] with msg: {$e->getMessage()}");
		}
		
		return $dirListObjects->objects;
	}

	protected function getCopyParams($fromFilePath, $toFilePath)
	{
		list($fromBucket, $fromFilePath) = $this->getBucketAndFilePath($fromFilePath);
		list($toBucket, $toFilePath) = $this->getBucketAndFilePath($toFilePath);

		$copyObjectDetails = array(
			'sourceObjectName' => $fromFilePath,
			'destinationRegion' => $this->region,
			'destinationNamespace' => $this->namespaceName,
			'destinationBucket' => $toBucket,
			'destinationObjectName' => $toFilePath
		);

		$params = array(
			'bucketName' => $fromBucket,
			'namespaceName' => $this->namespaceName,
			'copyObjectDetails' => $copyObjectDetails
		);

		return $params;
	}

	protected function getMultipartUploadParams($destFilePath)
	{
		list($bucketName, $fileName) = $this->getBucketAndFilePath($destFilePath);
		$multiPartParams = array(
			'namespaceName' => $this->namespaceName,
			'bucketName' => $bucketName,
			'createMultipartUploadDetails' => array('object' => $fileName)
		);
		return $multiPartParams;
	}

	protected function getUploadPartParams($destFilePath, $uploadId)
	{
		$params = $this->initBasicOciParams($destFilePath);
		$params['uploadId'] = $uploadId;
		return $params;
	}

	protected function getSourceAndDestinationStreams($src, $dest)
	{
		kSharedFileSystemMgr::restoreStreamWrappers();
		$srcRealPath = $this->doRealPath($src);

		$sourceFH = fopen($srcRealPath, 'rb');
		if (!$sourceFH)
		{
			self::safeLog("Could not open source file [$src] for read");
			kSharedFileSystemMgr::unRegisterStreamWrappers();
			return false;
		}

		$destFH = fopen($dest, 'w');
		if (!$destFH)
		{
			self::safeLog("Could not open destination file [$dest] for read");
			kSharedFileSystemMgr::unRegisterStreamWrappers();
			return false;
		}

		if (function_exists('stream_set_chunk_size'))
		{
			stream_set_chunk_size($sourceFH, self::CHUNK_SIZE);
		}

		return array($sourceFH, $destFH);
	}

	protected function closeSourceAndDestinationStreams($sourceFH, $destFH)
	{
		fclose($sourceFH);
		fclose($destFH);
		kSharedFileSystemMgr::unRegisterStreamWrappers();
		return true;
	}
	
	protected function validateWorkRequestAccepted($params)
	{
		// OS are making a copy request 'asynchronically'
		// we need to constantly check 'getWorkRequest' to get the Work request Id status
		// Because it can take a long time (~10GB avg. is 3 min) we send the request and don't wait.
		
		$response = $this->osCall('getWorkRequest', $params);
		$status = $response->getJson()->status;
		self::safeLog("OS copy request status = [$status]");
		
		switch ($status)
		{
			case self::COPY_OBJECT_STATUS_ACCEPTED:
			case self::COPY_OBJECT_STATUS_IN_PROGRESS:
			case self::COPY_OBJECT_STATUS_COMPLETED:
				break;
			case self::COPY_OBJECT_STATUS_FAILED:
				return array(false, $status);
			default:
				self::safeLog("OS unexpected status = [$status] returned");
				return array(false, $status);
		}
		return array(true, $status);
	}
}
