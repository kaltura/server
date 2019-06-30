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
		return true;
	}
	
	protected function doCheckFileExists($filePath)
	{
		list($bucket, $filePathWithoutBucket) = $this->getBucketAndFilePath($filePath);
		if(!$this->doIsFile($filePath))
		{
			return true;
		}
		try
		{
			$exists = $this->s3Client->doesObjectExist($bucket, $filePathWithoutBucket);
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
	
	private function isDirectory($fileName)
	{
		return !strpos($fileName,'.');
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
		
		$result = $this->s3Client->createMultipartUpload([
			'Bucket'       => $bucket,
			'Key'          => $filePath,
		]);
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
					'Body'       => stream_get_contents($sourceFH, 32 * 1024 * 1024),
				));
				$parts['Parts'][$partNumber] = array(
					'PartNumber' => $partNumber,
					'ETag' => $result['ETag'],
				);
				$partNumber++;
				
				KalturaLog::debug("Uploading part [$partNumber] dest file path [$destFilePath]");
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
		return true;
	}

	protected function doFullMkfileDir($path, $rights = 0777, $recursive = true)
	{
		return true;
	}

	protected function doMoveFile($from, $to, $override_if_exists = false, $copy = false)
	{
		$from = str_replace("\\", "/", $from);
		$to = str_replace("\\", "/", $to);

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
		return $this->isDirectory($path);
	}

	protected function doMkdir($path)
	{
		return true;
	}

	protected function doCopySingleFile($src, $dest, $deleteSrc)
	{
		if(kString::beginsWith($src, kSharedFileSystemMgr::getSharedRootPath()))
		{
			return $this->copyFileLocalToShared($src, $dest, $deleteSrc);
		}

		return $this->copyFileFrimShared($src, $dest, $deleteSrc);
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
		$srcUrl = $this->doRealPath($src);
		$srcContent = $this->doGetFileContent($srcUrl);

		$result = kFile::filePutContents($dest, $srcContent, 0, null);

		if (!$result)
		{
			KalturaLog::err("Failed to upload file: [$src] to [$dest]");
			return false;
		}
		if ($deleteSrc)
		{
			return $this->deleteFile($srcUrl);
		}
		return true;
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

	public function doCreateMultipartUpload($destFilePath)
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

	public function doMultipartUploadPartCopy($uploadId, $partNumber, $s3FileKey, $destFilePath)
	{
		list($bucket, $filePath) = self::getBucketAndFilePath($destFilePath);
		$srcPath = $bucket.'/'.$s3FileKey;
		try
		{
			$result = $this->s3Client->uploadPartCopy(array(
				'Bucket'     => $bucket,
				'CopySource' => $srcPath,
				'UploadId'   => $uploadId,
				'PartNumber' => $partNumber,
				'Key'       => $filePath,
			));

			KalturaLog::debug("coping part [$partNumber] from [$srcPath]. dest file path [$destFilePath]");
		}
		catch (S3Exception $e)
		{
			KalturaLog::debug("Upload of [$srcPath] failed.  Error: {$e->getMessage()}");
			$this->doAbortMultipartUpload($bucket, $filePath, $uploadId);
			return false;
		}

		return $result;
	}
	
	public function doAbortMultipartUpload($bucket, $filePath, $uploadId)
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
		
	public function doCompleteMultiPartUpload($destFilePath, $uploadId, $parts)
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
		$fileList = $this->doListFiles($filePath);
		if(!isset($fileList['Contents']))
		{
			KalturaLog::debug("Could not determine if provided file path [$filePath], is file");
			return false;
		}
		
		return count($fileList['Contents']) == 1;
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
		$defaultChunkSize = 100000;
		$exist = $this->doCheckFileExists($filePath);
		if($exist)
		{
			while($range_from <= $range_length)
			{
				$chunkSize = min($defaultChunkSize, ($range_length - $range_from));
				$range_to = $range_from + $chunkSize;
				$content = $this->getSpecificObjectRange($filePath, $range_from, $range_to);
				echo $content;
				$range_from = $range_to + 1;
			}
		}
	}

	protected function getSpecificObjectRange($filePath, $startRange, $endRange)
	{
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);

		$range = 'bytes='.$startRange.'-'.$endRange;

		$params = array(
			'Bucket' => $bucket,
			'Key'    => $filePath,
			'Range'  => $range
		);

		$response = $this->s3Client->getObject( $params );
		if($response)
		{
			return (string)$response['Body'];
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

		$fileList = $this->s3->getObject(array(
			'Bucket' => $bucket,
			'Key'    => $filePath
		));
		
		return $fileList['Last-Modified'];
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

	protected function doGetListObjectsPaginator($filePath)
	{
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);

		$paginator = $this->s3->getPaginator('ListObjects', array(
			'Bucket' => $bucket,
			'Prefix' => $filePath
		));

		return $paginator;
	}

	protected function doCopyDir($src, $dest, $deleteSrc)
	{

		$paginator = $this->s3->doGetListObjectsPaginator($src);

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

				$res = kFile::copySingleFile ($object['Key'], $dest . DIRECTORY_SEPARATOR . $fileName , $deleteSrc);
				if (! $res)
				{
					return false;
				}
			}
		}
		return true;
	}


}