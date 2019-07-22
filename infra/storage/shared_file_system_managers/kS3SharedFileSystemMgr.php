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
use Aws\S3\Enum\CannedAcl;

class kS3SharedFileSystemMgr extends kSharedFileSystemMgr
{
	const MULTIPART_UPLOAD_MINIMUM_FILE_SIZE = 5368709120;
	const MAX_PARTS_NUMBER = 10000;
	const MIN_PART_SIZE = 5242880;
	
	protected $filesAcl;
	protected $s3Region;
	protected $sseType;
	protected $sseKmsKeyId;
	protected $signatureType;
	protected $endPoint;
	protected $accessKeySecret;
	protected $accessKeyId;
	
	protected $s3Client;
	
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
		
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		
		$params = array(
			'Bucket' => $bucket,
			'Key'    => $filePath,
		);
		
		$response = $this->s3Client->getObject( $params );
		if($response)
		{
			return (string)$response['Body'];
		}
		
		return $response;
	}
	
	protected function doUnlink($filePath)
	{
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		
		$deleted = false;
		try
		{
			$params['Bucket'] = $bucket;
			$params['Key'] = $filePath;
			$this->s3Client->deleteObject($params);
			
			$deleted = true;
		}
		catch ( Exception $e )
		{
			KalturaLog::err("Couldn't delete file [$filePath] from bucket [$bucket]: {$e->getMessage()}");
		}
		
		return $deleted;
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
			$size = strlen($fileContent);
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
		list($toBucket, $toFilePath) = $this->getBucketAndFilePath($toFilePath);
		
		$params['Bucket'] = $toBucket;
		$params['Key'] = $toFilePath;
		$params['CopySource'] = $fromFilePath;
		
		try
		{
			$this->s3Client->copyObject($params);
		}
		catch(Exception $e)
		{
			return false;
		}
		
		return true;
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
		stream_wrapper_restore('http');
		stream_wrapper_restore('https');
		
		$sourceFH = fopen($resource, 'rb');
		if(!$sourceFH)
		{
			KalturaLog::err("Could not open source file [$resource] for read");
			return false;
		}
		
		list($bucket, $filePath) = self::getBucketAndFilePath($destFilePath);
		
		$result = $this->s3Client->createMultipartUpload(array(
			'Bucket'       => $bucket,
			'Key'          => $filePath,
		));
		$uploadId = $result['UploadId'];
		KalturaLog::debug("Starting multipart upload for [$resource] to [$destFilePath] with upload id [$uploadId]");
		
		// Upload the file in parts.
		try
		{
			$partNumber = 1;
			$parts = array();
			while (!feof($sourceFH))
			{
				$result = $this->s3Client->uploadPart(array(
					'Bucket'     => $bucket,
					'Key'        => $filePath,
					'UploadId'   => $uploadId,
					'PartNumber' => $partNumber,
					'Body'       => stream_get_contents($sourceFH, 16 * 1024 * 1024),
				));
				$parts['Parts'][$partNumber] = array(
					'PartNumber' => $partNumber,
					'ETag' => $result['ETag'],
				);
				
				KalturaLog::debug("Uploading part [$partNumber] dest file path [$destFilePath]");
				$partNumber++;
			}
			
			fclose($sourceFH);
		}
		catch (S3Exception $e)
		{
			$result = $this->s3Client->abortMultipartUpload(
				array(
					'Bucket'   => $bucket,
					'Key'      => $filePath,
					'UploadId' => $uploadId
				));
			
			KalturaLog::debug("Upload of [$destFilePath] failed");
		}
		
		// Complete the multipart upload.
		$result = $this->s3Client->completeMultipartUpload(array(
			'Bucket'   => $bucket,
			'Key'      => $filePath,
			'UploadId' => $uploadId,
			'MultipartUpload'    => $parts,
		));
		
		$url = $result['Location'];
		
		stream_wrapper_unregister('https');
		stream_wrapper_unregister('http');
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
		$from = str_replace("\\", "/", $from);
		$to = str_replace("//", "/", $to);

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
		list($bucket, $key) = self::getBucketAndFilePath($path);
		
		try
		{
			$res = $this->s3Client->headObject(array(
				'Bucket' => $bucket,
				'Key'    => $key
			));
		}
		catch (Exception $e)
		{
			KalturaLog::debug("Failed to fetch object head for file [$path], with error Error: {$e->getMessage()}");
			return false;
		}
		
		return $res;
	}

	protected function doMkdir($path, $mode, $recursive)
	{
		list($bucket, $key) = self::getBucketAndFilePath($path);
		
		$result = $this->s3Client->putObject(array(
			'Bucket' => $bucket,
			'Key'    => $key,
		));
		
		return true;
	}

	protected function doCopySingleFile($src, $dest, $deleteSrc)
	{
		if(kFile::isSharedPath($src))
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
		list($bucket, $filePathWithoutBucket) = $this->getBucketAndFilePath($filename);
		$params['Bucket'] = $bucket;
		$params['Key'] = $filePathWithoutBucket;
		try
		{
			$result = $this->s3Client->headObject($params);
		}
		catch (Exception $e)
		{
			return false;
		}
		return $result['ContentLength'];
	}

	public function createMultipartUpload($destFilePath)
	{
		list($bucket, $filePath) = self::getBucketAndFilePath($destFilePath);

		$params = array('Bucket' => $bucket, 'Key' => $filePath);
		try
		{
			$result = $this->s3Client->createMultipartUpload($params);
		}
		catch (Exception $e)
		{
			KalturaLog::err("Couldn't create multipart upload for [$filePath] on bucket [$bucket]: {$e->getMessage()}");
			return false;
		}
		$uploadId = $result['UploadId'];
		KalturaLog::debug("multipart upload started to [$destFilePath] with upload id [$uploadId]");
		return $uploadId;
	}

	public function multipartUploadPartCopy($uploadId, $partNumber, $s3FileKey, $destFilePath)
	{
		list($bucket, $filePath) = self::getBucketAndFilePath($destFilePath);
		
		try
		{
			$result = $this->s3Client->uploadPartCopy(array(
				'Bucket'     => $bucket,
				'CopySource' => $s3FileKey,
				'UploadId'   => $uploadId,
				'PartNumber' => $partNumber,
				'Key'       => $filePath,
			));

			KalturaLog::debug("coping part [$partNumber] from [$s3FileKey]. dest file path [$destFilePath]");
		}
		catch (S3Exception $e)
		{
			KalturaLog::debug("Upload of [$s3FileKey] failed.  Error: {$e->getMessage()}");
			$this->abortMultipartUpload($bucket, $filePath, $uploadId);
			return false;
		}

		return $result;
	}
	
	public function abortMultipartUpload($bucket, $filePath, $uploadId)
	{
		try
		{
			$result = $this->s3Client->abortMultipartUpload(
				array(
					'Bucket' => $bucket,
					'Key' => $filePath,
					'UploadId' => $uploadId
				));
			KalturaLog::debug("Upload of [$filePath] failed");
		}
		catch (S3Exception $e)
		{
			KalturaLog::err("Couldn't abort multipart upload for [$filePath] on bucket [$bucket]. Error: {$e->getMessage()}");
			return false;
		}
	}
		
	public function completeMultiPartUpload($destFilePath, $uploadId, $parts)
	{
		list($bucket, $filePath) = self::getBucketAndFilePath($destFilePath);

		try
		{
			$result = $this->s3Client->completeMultipartUpload(array(
				'Bucket'   => $bucket,
				'Key'      => $filePath,
				'UploadId' => $uploadId,
				'MultipartUpload'    => $parts,
			));
		}
		catch (S3Exception $e)
		{
			KalturaLog::debug("could not complete Upload of [$destFilePath]. Error: {$e->getMessage()}");
			return false;
		}

		$url = $result['Location'];
		return $url;
	}

	protected function doListFiles($filePath, $pathPrefix = '')
	{
		list($bucket, $prefix) = self::getBucketAndFilePath($filePath);

		KalturaLog::debug("list objects on bucket [$bucket] with prefix [$prefix]");
		$params = array('Bucket' => $bucket, 'Prefix' => $prefix);

		try {
			$results = $this->s3Client->listObjects($params);
		}

		catch (S3Exception $e)
		{
			KalturaLog::debug("could not list [$filePath] objects.  Error: {$e->getMessage()}");
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
		
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		
		$cmd = $this->s3Client->getCommand('GetObject',
			array(
			'Bucket' => $bucket,
			'Key' => $filePath
		));
		
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
		list($bucket, $key) = $this->getBucketAndFilePath($filePath);

		$params = array(
			'Bucket' => $bucket,
			'Key'    => $key,
			'Range'  => "bytes=$startRange-$endRange",
		);

		try
		{
			$response = $this->s3Client->getObject( $params );
		}
		catch (Exception $e)
		{
			KalturaLog::debug("Failed to fetch object range for file [$filePath], with error Error: {$e->getMessage()}");
			return false;
		}
		
		if($response)
		{
			return $response['Body'];
		}

		return $response;
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
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);

		$fileMeta = $this->s3Client->getObject(array(
			'Bucket' => $bucket,
			'Key'    => $filePath
		));
		
		return $fileMeta['Last-Modified'];
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
		list($bucket, $filePath) = $this->getBucketAndFilePath($src);
		
		$result = $this->s3Client->getObject(array(
			'Bucket' => $bucket,
			'Key'    => $filePath,
			'SaveAs' => $dest
		));
		
		return $result;
	}
	
	public function multipartUploadPartUpload($uploadId, $partNumber, $srcContent, $destFilePath)
	{
		list($bucket, $filePath) = self::getBucketAndFilePath($destFilePath);
		try
		{
			$result = $this->s3Client->uploadPart(array(
				'Bucket'     => $bucket,
				'Body' => $srcContent,
				'UploadId'   => $uploadId,
				'PartNumber' => $partNumber,
				'Key'       => $filePath,
			));
			KalturaLog::debug("uploading part [$partNumber]. dest file path [$destFilePath]");
		}
		catch (S3Exception $e)
		{
			KalturaLog::debug("Upload failed.  Error: {$e->getMessage()}");
			$this->abortMultipartUpload($bucket, $filePath, $uploadId);
			return false;
		}
		return $result;
	}
}
