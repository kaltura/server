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
		$chunkSize = kFile::fileSize($fileData['tmp_name']);

		return $this->handleOptimizedResume($fileData, $resumeAt, $chunkSize);
		/*
		if($minimumChunkSize)
		{
			if(($minimumChunkSize >= kS3SharedFileSystemMgr::MIN_PART_SIZE && $chunkSize >= $minimumChunkSize) || $this->_finalChunk)
			{
				return $this->handleOptimizedResumev2($fileData, $resumeAt, $chunkSize);
			}
			else
			{
				throw new kUploadTokenException("chunk size [$chunkSize] is less then minimum expected size [$minimumChunkSize]", kUploadTokenException::UPLOAD_TOKEN_INVALID_CHUNK_SIZE);
			}
		}
		*/
	}

	/**
	 * upload chunk to s3
	 *
	 * @param $filePath
	 * @param $uploadToken
	 * @param $resumeAt
	 * @param $chunkSize
	 */
	protected function uploadChunk($filePath, $srcPath)
	{
		KalturaLog::info("upload file from [{$srcPath}] to [{$filePath}]");
		$fileContent = kFile::getFileContent($srcPath);
		kFile::filePutContents($filePath, $fileContent);
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

	protected function handleOptimizedResume($fileData, $resumeAt, $chunkSize)
	{
		$uploadFilePath = dirname($this->_uploadToken->getUploadTempPath());
		$extension = $this->getFileExtension($this->_uploadToken->getFileName());

		$sourceFilePath = $fileData['tmp_name'];

		if ($resumeAt != -1) // this may not be a sequential chunk added at the end of the file
		{
			// support backwards compatibility of overriding a final chunk at the offset zero
			$verifyFinalChunk = $this->_finalChunk && $resumeAt > 0;

			// if this is the final chunk the expected file size would be the resume position + the last chunk size
			$expectedFileSize = $verifyFinalChunk ? ($resumeAt + $chunkSize) : 0;

			$chunkFilePath = "$uploadFilePath/$resumeAt.$extension";
			if($resumeAt == $this->_uploadToken->getUploadedFileSize())
			{
				$this->appendChunk($sourceFilePath, $this->_uploadToken->getUploadedFileSize(), $chunkSize, false, $chunkFilePath);
			}
			else
			{
				$this->uploadChunk($chunkFilePath, $sourceFilePath);
			}

			if($this->_autoFinalize && $this->checkIsFinalChunk($chunkSize))
			{
				$verifyFinalChunk = true;
				$expectedFileSize = $this->_uploadToken->getFileSize();
			}

			$uploadFinalChunkMaxAppendTime = kConf::get('upload_final_chunk_max_append_time', 'local', 30);

			// if finalChunk, try appending chunks till reaching expected file size for up to 30 seconds while sleeping for 1 second each iteration
			$count = 0;
			do
			{
				if ($count ++)
					Sleep(1);

				$criteria = new Criteria();
				$criteria->add(UploadTokenPeer::ID, $this->_uploadToken->getId());
				$this->_uploadToken = UploadTokenPeer::doSelectOne($criteria);

				$currentFileSize = $this->appendAvailableChunks($uploadFilePath);
				KalturaLog::log("handleResume iteration: $count chunk: $chunkFilePath size: $chunkSize finalChunk: {$this->_finalChunk} filesize: $currentFileSize expected: $expectedFileSize");
			}
			while ($verifyFinalChunk && $currentFileSize != $expectedFileSize && $count < $uploadFinalChunkMaxAppendTime);

			if ($verifyFinalChunk && $currentFileSize != $expectedFileSize)
			{
				throw new kUploadTokenException("final size $currentFileSize failed to match expected size $expectedFileSize", kUploadTokenException::UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE);
			}
		}
		else
		{
			// add sequentially at the end of file
			$currentFileSize = $this->_uploadToken->getUploadedFileSize();
			$this->appendChunk($sourceFilePath, $currentFileSize, $chunkSize);
			$currentFileSize += $chunkSize;

			if($this->_autoFinalize && $this->_uploadToken->getFileSize() >= $currentFileSize)
			{
				$this->_finalChunk = true;
			}
		}

		return $currentFileSize;
	}

	protected function startFullFileUpload($uploadFilePath, $fileSize)
	{
		$finalFilePath = $finalFilePath = $this->getFinalFilePath();
		$uploadId = self::$sharedFsMgr->createMultipartUpload($finalFilePath);
		if(!$uploadId)
		{
			throw new kUploadTokenException("multipart upload error during upload token closer", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
		}
		$this->_uploadToken->setUploadId($uploadId);

		if(!$this->_finalChunk)
		{
			return 0;
		}

		$partNum = $this->_uploadToken->getUploadedPartsNumber() + 1;

		$result = self::$sharedFsMgr->multipartUploadPartCopy($uploadId, $partNum, $uploadFilePath, $finalFilePath);
		if($result)
		{
			$this->handlePartUploadSuccess($partNum, $result, $fileSize, true);
		}
		else
		{
			throw new kUploadTokenException("multipart upload error during upload token closer", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
		}

		return $this->_uploadToken->getUploadedFileSize();
	}

	protected function handlePartUploadSuccess($partNum, $result, $fileSize, $copy = false)
	{

		$etag = ($copy  ? $result['CopyPartResult']['ETag'] : $result['ETag']);
		$parts = $this->_uploadToken->getParts();
		$parts['Parts'][$partNum] = array(
			'PartNumber' => $partNum,
			'ETag' => $etag,
		);

		$this->_uploadToken->setParts($parts);
		$this->_uploadToken->setUploadedPartsNumber($partNum);
		$this->_uploadToken->setUploadedFileSize($fileSize);
	}

	protected function closeFullFileUpload()
	{
		$finalFilePath = $this->getFinalFilePath();
		self::$sharedFsMgr->completeMultipartUpload($this->getFinalFilePath(), $this->_uploadToken->getUploadId(), $this->_uploadToken->getParts());
		$this->_uploadToken->setUploadTempPath($finalFilePath);
	}

	protected function getFinalFilePath()
	{
		$uploadFolderPath = dirname($this->_uploadToken->getUploadTempPath());
		$finalFilePath = $uploadFolderPath .  '/full_file.' . $this->getFileExtension($this->_uploadToken->getFileName());
		return $finalFilePath;
	}

	protected function appendChunk($srcPath, $resumeAt, $chunkSize, $copy = false, $chunkFilePath = null)
	{
		$finalPath = $this->getFinalFilePath();
		KalturaLog::info("upload file from [{$srcPath}] to [{$finalPath}]");
		$partNum = $this->_uploadToken->getUploadedPartsNumber() + 1;
		if($copy)
		{
			$result = self::$sharedFsMgr->multipartUploadPartCopy($this->_uploadToken->getUploadId(), $partNum, $srcPath, $finalPath);
			if($result)
			{
				$uploadedSize = $resumeAt + $chunkSize;
				$this->_uploadToken->setUploadedFileSize($uploadedSize);
				$this->handlePartUploadSuccess($partNum, $result, $uploadedSize, true);
			}
		}
		else
		{
			stream_wrapper_restore('http');
			stream_wrapper_restore('https');

			$sourceFH = fopen($srcPath, 'rb');
			if(!$sourceFH)
			{
				KalturaLog::err("Could not open source file [$srcPath] for read");
				return false;
			}
			while (!feof($sourceFH))
			{
				$body = stream_get_contents($sourceFH, 32 * 1024 * 1024);
				$result = self::$sharedFsMgr->multipartUploadPartUpload($this->_uploadToken->getUploadId(), $partNum, $body, $finalPath);
				if($result)
				{
					$uploadedSize = $resumeAt + $chunkSize;
					$this->_uploadToken->setUploadedFileSize($uploadedSize);
					$this->handlePartUploadSuccess($partNum, $result, $uploadedSize);
					$partNum += 1;
				}
			}
			fclose($sourceFH);

			stream_wrapper_unregister('https');
			stream_wrapper_unregister('http');
		}
	}

	protected function appendAvailableChunks($uploadFilePath)
	{
		list($bucket, $filePath) = explode("/",ltrim($uploadFilePath,"/"),2);

		$uploadChunks = array();
		$listStart = microtime(true);
		$paginator = self::$sharedFsMgr->getListObjectsPaginator($uploadFilePath);
		foreach ($paginator as $page)
		{
			$uploadChunks = array_merge($uploadChunks, $page['Contents']);
		}
		$listTook = (microtime(true) - $listStart);
		KalturaLog::debug("list took - " . $listTook . " seconds");

		usort($uploadChunks,  array($this, 'compareKeyResumeAt'));

		while (1)
		{
			$currentFileSize = $this->_uploadToken->getUploadedFileSize();
			$validChunk = false;

			foreach($uploadChunks as $nextChunk)
			{
				$chunkKey = basename($nextChunk['Key']);
				$chunkResumeAt = (int)substr($chunkKey, 0, strrpos($chunkKey, "."));

				// dismiss chunks which won't enlarge the file or which are starting after the end of the file
				// support backwards compatibility of overriding a final chunk at the offset zero
				if ($chunkResumeAt == $currentFileSize && $currentFileSize != 0)
				{
					$validChunk = true;
					break;
				}
				else
				{
					KalturaLog::log("ignoring chunk: $chunkKey offset: $chunkResumeAt fileSize: $currentFileSize");
				}
			}

			if (!$validChunk || !isset($nextChunk))
			{
				break;
			}

			$this->appendChunk('/' . $bucket . '/' . $nextChunk['Key'], $this->_uploadToken->getUploadedFileSize(), $nextChunk['Size'], true);
		}

		return $this->_uploadToken->getUploadedFileSize();
	}

}