<?php
/**
 * Created by IntelliJ IDEA.
 * User: inbal.bendavid
 * Date: 6/20/2019
 * Time: 4:55 PM
 */

class kS3UploadTokenMgr extends kBaseUploadTokenMgr
{
	private static $sharedFsMgr;

	public function __construct(UploadToken $uploadToken, $finalChunk = true)
	{
		KalturaLog::info("Init for upload token id [{$uploadToken->getId()}]");
		self::$sharedFsMgr = kSharedFileSystemMgr::getInstance();
		parent::__construct($uploadToken, $finalChunk);
	}

	/**
	 * Resume the upload token with the uploaded file optionally at a given offset
	 *
	 * @param file $fileData
	 * @param float $resumeAt
	 * @return string
	 * @throws kUploadTokenException
	 */
	protected function handleResume($fileData, $resumeAt)
	{
		$minimumChunkSize = $this->_uploadToken->getMinimumChunkSize();

		if($minimumChunkSize && $minimumChunkSize >= kS3SharedFileSystemMgr::MIN_PART_SIZE)
		{
			return $this->handleOptimizedResume($fileData, $resumeAt);
		}
	}

	/**
	 * handle resume when all the chunks are suitable for S3 multipartUpload without the need of inner concatenation
	 *
	 * @param $fileData
	 * @param $resumeAt
	 * @return string
	 * @throws kUploadTokenException
	 */
	protected function handleOptimizedResume($fileData, $resumeAt)
	{
		$uploadFilePath = dirname($this->_uploadToken->getUploadTempPath());
		$sourceFilePath = $fileData['tmp_name'];
		$chunkSize = kFile::fileSize($sourceFilePath);
		$extension = $this->getFileExtension($this->_uploadToken->getFileName());

		if ($resumeAt != -1) // this may not be a sequential chunk added at the end of the file
		{
			// support backwards compatibility of overriding a final chunk at the offset zero
			$verifyFinalChunk = $this->_finalChunk && $resumeAt > 0;

			// if this is the final chunk the expected file size would be the resume position + the last chunk size
			$chunkSize = filesize($sourceFilePath);
			$expectedFileSize = $verifyFinalChunk ? ($resumeAt + $chunkSize) : 0;

			if($this->_autoFinalize && $this->checkIsFinalChunk($chunkSize))
			{
				$verifyFinalChunk = true;
				$expectedFileSize = $this->_uploadToken->getFileSize();
			}

			$chunkFilePath = "$uploadFilePath/$resumeAt.$extension";
			$this->uploadChunk($chunkFilePath, $sourceFilePath, $resumeAt, $chunkSize);

			if($verifyFinalChunk && $this->_uploadToken->getFileSize() != $expectedFileSize)
			{
				$this->createFullFile($uploadFilePath, $this->_uploadToken);
			}
		}
		else
		{
			// add sequentially at the end of file
			$currentFileSize = $this->_uploadToken->getLastFileSize();
			$chunkFilePath = "$uploadFilePath/$currentFileSize.$extension";
			$this->uploadChunk($chunkFilePath, $sourceFilePath, $this->_uploadToken->getLastFileSize(), $chunkSize);

			if($this->_autoFinalize && $this->_uploadToken->getFileSize() >= $currentFileSize)
			{
				$this->_finalChunk = true;
			}

			if($this->_finalChunk)
			{
				$this->createFullFile($uploadFilePath, $this->_uploadToken);
			}
		}

		$currentFileSize = $this->_uploadToken->getLastFileSize();
		return $currentFileSize;
	}

	/**
	 * upload chunk to s3
	 *
	 * @param $filePath
	 * @param $uploadToken
	 * @param $resumeAt
	 * @param $chunkSize
	 */
	protected function uploadChunk($filePath, $srcPath, $resumeAt, $chunkSize)
	{
		KalturaLog::info("upload file from [{$srcPath}] to [{$filePath}]");
		$fileContent = kFile::getFileContent($srcPath);
		$result = kFile::filePutContents($filePath, $fileContent);
		if($result)
		{
			$uploadedSize = $resumeAt + $chunkSize;
			$this->_uploadToken->setLastFileSize($uploadedSize);
		}
	}

	/**
	 * list all file chunks and upload them in multipart upload
	 *
	 * @param $finalFilePath
	 */
	protected function createFullFile($finalFilePath)
	{
		sleep(3);
		$uploadChunksResponse = kFile::listDir($finalFilePath);
		$uploadChunks = $uploadChunksResponse['Contents'];
		usort($uploadChunks,  array($this, 'compareKeyResumeAt'));

		$finalFilePath .= '/full_file.' . $this->getFileExtension($this->_uploadToken->getFileName());
		$uploadId = self::$sharedFsMgr->doCreateMultipartUpload($finalFilePath);
		if(!$uploadId)
		{
			throw new kUploadTokenException("multipart upload error during upload token closer", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
		}
		
		$copiedParts = $this->uploadParts($uploadChunks, $uploadId, $finalFilePath);
		$location = self::$fileSystemManager->doCompleteMultipartUpload($finalFilePath, $uploadId, $copiedParts);
		if(!$location)
		{
			throw new kUploadTokenException("multipart upload error during upload token closer", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
		}
		
		$this->_uploadToken->setUploadTempPath($finalFilePath);
	}
	
	/**
	 * upload part copy all multipart upload pats
	 *
	 * @param $uploadChunks
	 * @param $uploadId
	 * @param $finalFilePath
	 * @return mixed
	 * @throws kUploadTokenException
	 */
	protected function uploadParts($uploadChunks, $uploadId, $finalFilePath)
	{
		$partNumber = 1;
		foreach ($uploadChunks as $chunk)
		{
			$result = self::$fileSystemManager->doMultipartUploadPartCopy($uploadId, $partNumber, $chunk['Key'], $finalFilePath);
			if($result)
			{
				$copiedParts['Parts'][$partNumber] = array(
					'PartNumber' => $partNumber,
					'ETag' => $result['CopyPartResult']['ETag'],
				);
			}
			else
			{
				throw new kUploadTokenException("multipart upload error during upload token closer", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
			}
			$partNumber +=1;
		}
		self::$fileSystemManager->doCompleteMultipartUpload($finalFilePath, $uploadId, $copiedParts);
		$this->_uploadToken->setUploadTempPath($finalFilePath);
		return $copiedParts;
	}

	/**
	 * Returns the target upload path for upload token id and extension
	 *
	 * @param $uploadTokenId
	 * @param string $extension
	 * @return string
	 */
	protected function getUploadPath($uploadTokenId, $extension = '')
	{
		if (!$extension)
			$extension = self::NO_EXTENSION_IDENTIFIER;

		return myContentStorage::getFSUploadsPath().substr($uploadTokenId, -2).'/'.$uploadTokenId.'/0.'.$extension;
	}

	/**
	 * sorting function for uploadToken chunks
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 *
	 */
	function compareKeyResumeAt($a, $b)
	{
		$key_a = basename($a['Key']);
		$key_b = basename ($b['Key']);

		$time_a = (int)substr($key_a, 0, strrpos($key_a, "."));
		$time_b = (int)substr($key_b, 0, strrpos($key_b, "."));
		if ($time_a == $time_b)
		{
			return 0;
		}
		return ($time_a < $time_b) ? -1 : 1;
	}

}