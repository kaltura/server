<?php
/**
 * Created by IntelliJ IDEA.
 * User: yossi.papiashvili
 * Date: 5/26/19
 * Time: 4:19 PM
 */

// AWS SDK PHP Client Library
require_once(dirname(__FILE__) . '/../../../vendor/oci/vendor/autoload.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/AuthProviderInterface.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/OciResponse.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/Regions.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/UserAgent.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/Common/HttpUtils.php');
require_once(dirname(__FILE__) . '/../../../vendor/oci/src/Oracle/Oci/ObjectStorage/ObjectStorageClient.php');
require_once(dirname(__FILE__) . '/kSharedFileSystemMgr.php');

use Oracle\Oci\Common\HttpUtils;
use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use GuzzleHttp\Exception\ClientException;
use Oracle\Oci\Common\AbstractClient;
use Oracle\Oci\Common\ConfigFileAuthProvider;
use Oracle\Oci\Common\Logging\EchoLogAdapter;


class kOciSharedFileSystemMgr extends kSharedFileSystemMgr
{
	const GET_EXCEPTION_CODE_FUNCTION_NAME = "getCode";
	const OS_404_ERROR = 404;
	
	/* @var ObjectStorageClient $objectStoargeClient */
	protected $objectStoargeClient;
	protected $retriesNum;
	
	protected $region;
	protected $endPoint;
	protected $accessKeySecret;
	protected $namespaceName;
	protected $accessKeyId;
	protected $storageClass;
	protected $userAgentRegex;
	protected $userAgentPartner;
	
	// instances of this class should be created usign the 'getInstance' of the 'kLocalFileSystemManger' class
	public function __construct(array $options = null)
	{
		parent::__construct($options);
		if(!$options || (is_array($options) && !count($options)))
		{
			$options = kConf::get('oci_storage_options', 'cloud_storage', null);
		}
		
		if($options)
		{
			$this->region = isset($options['region']) ? $options['region'] : null;
			$this->namespaceName = isset($options['namespaceName']) ? $options['namespaceName'] : null;
			$this->userAgentRegex = isset($options['userAgentRegex']) ? $options['userAgentRegex'] : null;
			$this->userAgentPartner = isset($options['userAgentPartner']) ? $options['userAgentPartner'] : "Kaltura";
		}
		
		$this->concurrency = isset($options['concurrency']) ? $options['concurrency'] : 1;
		$this->retriesNum = isset($options['concurrency']) ? $options['concurrency'] : 3;
		return $this->login();
	}
	
	private function login()
	{
		if(!class_exists('Oracle\Oci\ObjectStorage\ObjectStorageClient'))
		{
			self::safeLog('Class ObjectStorageClient was not found!!');
			return false;
		}
		
		$auth_provider = new ConfigFileAuthProvider();
		$this->objectStoargeClient = new ObjectStorageClient($auth_provider,  $this->region);
	}
	
	
	protected function doCreateDirForPath($filePath)
	{
		// TODO: Implement doCreateDirForPath() method.
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
		// TODO: Implement doUnlink() method.
	}
	
	protected function doPutFileContentAtomic($filePath, $fileContent)
	{
		// TODO: Implement doPutFileContentAtomic() method.
	}
	
	protected function doPutFileContent($filePath, $fileContent, $flags = 0, $context = null)
	{
		// TODO: Implement doPutFileContent() method.
	}
	
	protected function doRename($filePath, $newFilePath)
	{
		// TODO: Implement doRename() method.
	}
	
	protected function doCopy($fromFilePath, $toFilePath)
	{
		// TODO: Implement doCopy() method.
	}
	
	protected function doGetFileFromResource($resource, $destFilePath = null, $allowInternalUrl = false)
	{
		// TODO: Implement doGetFileFromResource() method.
	}
	
	protected function doFullMkdir($path, $rights = 0755, $recursive = true)
	{
		// TODO: Implement doFullMkdir() method.
	}
	
	protected function doFullMkfileDir($path, $rights = 0777, $recursive = true)
	{
		// TODO: Implement doFullMkfileDir() method.
	}
	
	protected function doMoveFile($from, $to, $override_if_exists = false, $copy = false)
	{
		// TODO: Implement doMoveFile() method.
	}
	
	protected function doIsDir($path)
	{
		// TODO: Implement doIsDir() method.
	}
	
	protected function doMkdir($path, $mode, $recursive)
	{
		// TODO: Implement doMkdir() method.
	}
	
	protected function doRmdir($path)
	{
		// TODO: Implement doRmdir() method.
	}
	
	protected function doChown($path, $user, $group)
	{
		// TODO: Implement doChown() method.
	}
	
	protected function doChmod($path, $mode)
	{
		// TODO: Implement doChmod() method.
	}
	
	protected function doFileSize($filename)
	{
		$result = $this->getHeadObjectForPath($filename);
		if(!$result)
		{
			return false;
		}
		
		return $result->getHeaderValue('Content-Length');
	}
	
	protected function doDeleteFile($filename)
	{
		// TODO: Implement doDeleteFile() method.
	}
	
	protected function doCopySingleFile($src, $dest, $deleteSrc)
	{
		// TODO: Implement doCopySingleFile() method.
	}
	
	protected function doGetMaximumPartsNum()
	{
		// TODO: Implement doGetMaximumPartsNum() method.
	}
	
	protected function doGetUploadMinimumSize()
	{
		// TODO: Implement doGetUploadMinimumSize() method.
	}
	
	protected function doGetUploadMaxSize()
	{
		// TODO: Implement doGetUploadMaxSize() method.
	}
	
	protected function doListFiles($filePath, $pathPrefix = '', $recursive = true, $fileNamesOnly = false)
	{
		// TODO: Implement doListFiles() method.
	}
	
	protected function doIsFile($filePath)
	{
		// TODO: Implement doIsFile() method.
	}
	
	protected function doRealPath($filePath, $getRemote = true)
	{
		// TODO: Implement doRealPath() method.
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
		// TODO: Implement doDumpFilePart() method.
	}
	
	protected function doChgrp($filePath, $contentGroup)
	{
		// TODO: Implement doChgrp() method.
	}
	
	protected function doFilemtime($filePath)
	{
		// TODO: Implement doFilemtime() method.
	}
	
	protected function doMoveLocalToShared($from, $to, $copy = false)
	{
		// TODO: Implement doMoveLocalToShared() method.
	}
	
	protected function doDir($filePath)
	{
		// TODO: Implement doDir() method.
	}
	
	protected function doCopyDir($src, $dest, $deleteSrc)
	{
		// TODO: Implement doCopyDir() method.
	}
	
	protected function doShouldPollFileExists()
	{
		// TODO: Implement doShouldPollFileExists() method.
	}
	
	protected function doCopySharedToSharedAllowed()
	{
		// TODO: Implement doCopySharedToSharedAllowed() method.
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
				$getExceptionFunctionName = self::GET_EXCEPTION_CODE_FUNCTION_NAME;
				$retries--;
				if (in_array($e->$getExceptionFunctionName(), $finalErrorCodes))
				{
					//In case final status is passed dont log the exception to avoid spamming the log file
					$retries = 0;
					return false;
				}
				$this->handleException($command, $retries, $params, $e);
			}
		}
		
		return false;
	}
	
	public function initBasicOciParams($filePath)
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
	
	protected function handleException($command, $retries, $params, $e)
	{
		// don't print body to logs
		if(isset($params['Body']));
		{
			unset($params['Body']);
		}
		
		self::safeLog("OS [$command] command failed. Retries left: [$retries] Params: " . print_r($params, true)."\n{$e->getMessage()}");
	}
}
