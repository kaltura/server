<?php
/**
 * Created by IntelliJ IDEA.
 * User: yossi.papiashvili
 * Date: 5/26/19
 * Time: 4:19 PM
 */

// AWS SDK PHP Client Library
require_once(dirname(__FILE__) . '/../../../vendor/aws/aws-autoloader.php');
require_once(dirname(__FILE__) . '/kS3SharedFileSystemMgr.php');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class kS3SharedFileSystemMgr_V3_SDK extends kSharedFileSystemMgr
{
	const MULTIPART_UPLOAD_MINIMUM_FILE_SIZE = 5368709120;
	const MAX_PARTS_NUMBER = 10000;
	const MIN_PART_SIZE = 5242880;
	
	//This works only for SDK V3 for V2 need to use ‌NoSuchKey && getAwsErrorCode
	//const AWS_404_ERROR = 'NotFound';
	
	const GET_EXCEPTION_CODE_FUNCTION_NAME = "getExceptionCode";
	const AWS_404_ERROR = '‌NoSuchKey';
	
	protected $filesAcl;
	protected $s3Region;
	protected $sseType;
	protected $sseKmsKeyId;
	protected $signatureType;
	protected $endPoint;
	protected $accessKeySecret;
	protected $accessKeyId;
	
	protected $s3Client;
	
	protected $retriesNum;
	
	// instances of this class should be created usign the 'getInstance' of the 'kLocalFileSystemManger' class
	public function __construct(array $options = null)
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
		
		if($options && isset($options['accessKeySecret']))
		{
			$this->accessKeySecret = $options['accessKeySecret'];
		}
		
		if($options && isset($options['accessKeyId']))
		{
			$this->accessKeyId = $options['accessKeyId'];
		}
		
		$this->retriesNum = kConf::get('aws_client_retries', 'local', 3);
		return $this->login();
	}
	
	private function login()
	{
		if(!class_exists('Aws\S3\S3Client'))
		{
			KalturaLog::err('Class Aws\S3\S3Client was not found!!');
			return false;
		}
		
		$config = array(
			'credentials' => array(
				'key'    => $this->accessKeyId,
				'secret' => $this->accessKeySecret,
			),
			'region' => $this->s3Region,
			'signature' => $this->signatureType ? $this->signatureType : 'v4',
			'version' => '2006-03-01',
			'scheme'  => 'http'
		);
		
		if ($this->endPoint)
			$config['endpoint'] = $this->endPoint;
		
		$this->s3Client = S3Client::factory($config);
		
		/**
		 * There is no way of "checking the credentials" on s3.
		 * Doing a ListBuckets would only check that the user has the s3:ListAllMyBuckets permission
		 * which we don't use anywhere else in the code anyway. The code will fail soon enough in the
		 * code elsewhere if the permissions are not sufficient.
		 **/
		return true;
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
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		try
		{
			$res = $this->s3Client->upload($bucket,
				$filePath,
				$fileContent,
				$this->filesAcl,
				array('params' => $params)
			);
			
			return array(true, $res);
		}
		catch (Exception $e)
		{
			return array(false, $e->getMessage());
		}
	}
	
	protected function doPutFileContent($filePath, $fileContent, $flags = 0, $context = null)
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
		
		while ($retries)
		{
			list($success, $res) = @($this->doPutFileHelper($filePath, $fileContent, $params));
			if ($success)
				return $res;
			
			$retries--;
		}
		
		KalturaLog::err("put file content failed with error: {$res->getMessage()}");
		
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
		if(!$this->doCopy($filePath, $newFilePath))
		{
			return false;
		}
		
		$this->doUnlink($filePath);
		return true;
	}
	
	protected function doGetFileFromResource($resource, $destFilePath = null, $allowInternalUrl = false)
	{
		$this->registerStreamWrappers();
		
		$sourceFH = fopen($resource, 'rb');
		if(!$sourceFH)
		{
			KalturaLog::err("Could not open source file [$resource] for read");
			$this->unregisterStreamWrappers();
			return false;
		}
		
		$uploadId = $this->createMultipartUpload($destFilePath);
		if(!$uploadId)
		{
			$this->unregisterStreamWrappers();
			return false;
		}
		
		KalturaLog::debug("Starting multipart upload for [$resource] to [$destFilePath] with upload id [$uploadId]");
		
		// Upload the file in parts.
		$partNumber = 1;
		$parts = array();
		while (!feof($sourceFH))
		{
			$srcContent = stream_get_contents($sourceFH, 16 * 1024 * 1024);
			$result = $this->multipartUploadPartUpload($uploadId, $partNumber, $srcContent, $destFilePath);
			if(!$result)
			{
				$this->unregisterStreamWrappers();
				$this->abortMultipartUpload($destFilePath, $uploadId);
				return false;
			}
			
			$parts['Parts'][$partNumber] = array(
				'PartNumber' => $partNumber,
				'ETag' => $result['ETag'],
			);
			
			KalturaLog::debug("Uploading part [$partNumber] dest file path [$destFilePath]");
			$partNumber++;
		}
		
		fclose($sourceFH);
		
		// Complete the multipart upload.
		$result = $this->completeMultiPartUpload($destFilePath, $uploadId, $parts);
		if(!$result)
		{
			$this->unregisterStreamWrappers();
			return false;
		}
		
		$this->unregisterStreamWrappers();
		return true;
	}
	
	protected function doFullMkdir($path, $rights = 0755, $recursive = true)
	{
		return $this->doFullMkfileDir(dirname($path), $rights, $recursive);
	}
	
	protected function doFullMkfileDir($path, $rights = 0777, $recursive = true)
	{
		if($this->doIsDir($path))
		{
			return;
		}
		
		list($bucket, $key) = $this->getBucketAndFilePath($path);
		$dirList = explode($key, "/");
		$fullDir = "/$bucket/";
		
		while($currDir = array_shift($dirList))
		{
			$fullDir .= "$currDir/";
			$this->doMkdir($fullDir, $rights, $recursive);
		}
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
		$res = $this->getHeadObjectForPath($path);
		if(!$res)
		{
			return false;
		}
		
		$effectiveUrl = $res['@metadata']['effectiveUri'];
		$contentLength = $res['ContentLength'];
		
		return ($contentLength == 0 && substr($effectiveUrl, -1) == "/") ? true : false;
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
		$params = $this->initBasicS3Params($path);
		$params['Body'] = '';
		$result = $this->s3Call('putObject', $params, $path);
		
		if(!$result)
		{
			return false;
		}
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
		return $result['ContentLength'];
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
	
	protected function doListFiles($filePath, $pathPrefix = '')
	{
		$results = $this->s3Call('listObjects', null, $filePath);
		
		if(!$results)
		{
			return false;
		}
		return $results;
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
		
		$effectiveUrl = $res['@metadata']['effectiveUri'];
		$contentLength = $res['ContentLength'];
		
		return ($contentLength != 0 && substr($effectiveUrl, -1) != "/") ? true : false;
	}
	
	protected function doRealPath($filePath, $getRemote = true)
	{
		if(!$getRemote)
			return $filePath;
		
		$params = $this->initBasicS3Params($filePath);
		
		$cmd = $this->s3Client->getCommand('GetObject', $params);
		
		$request = $this->s3Client->createPresignedRequest($cmd, '+120 minutes');
		$preSignedUrl = (string)$request->getUri();
		return $preSignedUrl;
	}
	
	protected function doDumpFilePart($filePath, $range_from, $range_length)
	{
		$fileUrl = $this->doRealPath($filePath);
		
		KalturaLog::debug("Test:: range_from [$range_from] range_length [$range_length]");
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $fileUrl);
		curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		$range_to = ($range_from + $range_length) - 1;
		curl_setopt($ch, CURLOPT_RANGE, "$range_from-$range_to");
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'kFileUtils::read_body');
		
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
		return $response['Body'];
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
		$result = $this->s3Call('getObject', null, $filePath);
		
		if(!$result)
		{
			return false;
		}
		return $result['Last-Modified'];
	}
	
	protected function doMoveLocalToShared($from, $to, $copy = false)
	{
		$res = $this->doGetFileFromResource($from, $to);
		if(!$res)
		{
			KalturaLog::debug("Failed to move local file [$from] to shared [$to]");
			return $res;
		}
		
		if($copy)
			return $res;
		
		@unlink($from);
		return $res;
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
		
		while ($retries)
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
				}
				$this->handleS3Exception($command, $retries, $params, $e);
			}
		}
		
		return false;
	}
	
	
	protected function registerStreamWrappers()
	{
		stream_wrapper_restore('http');
		stream_wrapper_restore('https');
	}
	
	protected function unregisterStreamWrappers()
	{
		stream_wrapper_unregister('https');
		stream_wrapper_unregister('http');
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
		
		KalturaLog::warning("S3 [$command] command failed. Retries left: [$retries] Params: " . print_r($params, true)."\n{$e->getMessage()}");
	}
	
	protected function doCopySharedToSharedAllowed()
	{
		return false;
	}
	
}