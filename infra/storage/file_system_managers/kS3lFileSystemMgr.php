<?php
/**
 * Created by IntelliJ IDEA.
 * User: yossi.papiashvili
 * Date: 5/26/19
 * Time: 4:19 PM
 */

// AWS SDK PHP Client Library
require_once(dirname(__FILE__) . '/../../../vendor/aws/aws-autoloader.php');
require_once(dirname(__FILE__) . '/kFileSystemMgr.php');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Enum\CannedAcl;

class kS3lFileSystemMgr extends kFileSystemMgr
{
	const MULTIPART_UPLOAD_MINIMUM_FILE_SIZE = 5368709120;
	
	protected $filesAcl;
	protected $s3Region;
	protected $sseType;
	protected $sseKmsKeyId;
	protected $signatureType;
	protected $endPoint;
	protected $userName;
	protected $password;
	
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
		
		if($options && isset($options['password']))
		{
			$this->password = $options['password'];
		}
		
		if($options && isset($options['userName']))
		{
			$this->userName = $options['userName'];
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
				'key'    => $this->userName,
				'secret' => $this->password,
			),
			'region' => $this->s3Region,
			'signature' => $this->signatureType ? $this->signatureType : 'v4',
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
	
	protected function doGetFile($filePath)
	{
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		
		$params = array(
			'Bucket' => $bucket,
			'Key'    => $filePath,
		);
		
		$response = $this->s3Client->getObject( $params );
		if($response && !$local_file)
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
			KalturaLog::err("Couldn't delete file [$remote_file] from bucket [$bucket]: {$e->getMessage()}");
		}
		
		return $deleted;
	}
	
	protected function doPutFileAtomic($filePath, $fileContent)
	{
		return $this->doPutFile($filePath, $fileContent);
	}
	
	private function doPutFileHelper($filePath , $fileContent, $params)
	{
		list($bucket, $filePath) = $this->getBucketAndFilePath($filePath);
		try
		{
			$size = strlen($fileContent);
			if ($size > self::MULTIPART_UPLOAD_MINIMUM_FILE_SIZE)
			{
				$res = $this->s3Client->upload($bucket,
					$filePath,
					$fileContent,
					$this->filesAcl,
					array('params' => $params)
				);
			}
			else
			{
				$params['Bucket'] = $bucket;
				$params['Key'] = $filePath;
				$params['Body'] = (string)$fileContent;
				$params['ACL'] = $this->filesAcl;
				
				$res = $this->s3Client->putObject($params);
			}
			
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
	
	protected function doPutFile($filePath, $fileContent)
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
}