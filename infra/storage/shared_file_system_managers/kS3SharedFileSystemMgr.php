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
		if($this->isDirectory($filePathWithoutBucket))
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
	
	protected function doGetFileContent($filePath)
	{
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
	
	protected function doPutFileContentAtomic($filePath, $fileContent)
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
			
			return array(true, null);
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
	
	protected function doPutFileContent($filePath, $fileContent)
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
			list($success, $message) = @($this->doPutFileHelper($filePath, $fileContent, $params));
			if ($success)
				return true;
			
			$retries--;
		}
		
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
	
	protected function doGetFileFromRemoteUrl($url, $destFilePath = null, $allowInternalUrl = false)
	{
		stream_wrapper_restore('http');
		stream_wrapper_restore('https');
		
		$sourceFH = fopen($url, 'rb');
		if(!$sourceFH)
		{
			KalturaLog::err("Could not open source file [$url] for read");
			return false;
		}
		
		list($bucket, $filePath) = self::getBucketAndFilePath($destFilePath);
		
		$result = self::$s3Client->createMultipartUpload([
			'Bucket'       => $bucket,
			'Key'          => $filePath,
		]);
		$uploadId = $result['UploadId'];
		KalturaLog::debug("Starting multipart upload for [$url] to [$destFilePath] with upload id [$uploadId]");
		
		// Upload the file in parts.
		try
		{
			$partNumber = 1;
			$parts = array();
			while (!feof($sourceFH))
			{
				$result = self::$s3Client->uploadPart(array(
					'Bucket'     => $bucket,
					'Key'        => $filePath,
					'UploadId'   => $uploadId,
					'PartNumber' => $partNumber,
					'Body'       => stream_get_contents($sourceFH, 128 * 1024 * 1024),
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
			$result = self::$s3Client->abortMultipartUpload(
				array(
					'Bucket'   => $bucket,
					'Key'      => $filePath,
					'UploadId' => $uploadId
				));
			
			KalturaLog::debug("Upload of [$destFilePath] failed");
		}
		
		// Complete the multipart upload.
		$result = self::$s3Client->completeMultipartUpload(array(
			'Bucket'   => $bucket,
			'Key'      => $filePath,
			'UploadId' => $uploadId,
			'MultipartUpload'    => $parts,
		));
		
		$url = $result['Location'];
		
		stream_wrapper_unregister('https');
		stream_wrapper_unregister('http');
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

		if(!$this->checkFileExists($from))
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

		return $this->isDirectory();
	}

	protected function doMkdir($path)
	{
		return true;
	}

	protected function copySingleFile($src, $dest, $deleteSrc, $fromLocal = true)
	{
		$srcContent = kFile::getFileContent($src);
		if (!$this->putFileContent($dest ,$srcContent))
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

	public function doChmod($path, $mode)
	{
		return true;
	}

	public function doFileSize($filename)
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
}