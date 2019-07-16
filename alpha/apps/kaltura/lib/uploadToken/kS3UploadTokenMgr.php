<?php
/**
 * Created by IntelliJ IDEA.
 * User: inbal.bendavid
 * Date: 6/20/2019
 * Time: 4:55 PM
 */

class kS3UploadTokenMgr extends kBaseUploadTokenMgr
{

	/**
	 * file system manager
	 * @var kSharedFileSystemMgr
	 */
	private static $sharedFsMgr;

	/**
	 * cache info about the uploadToken Multipart upload
	 * @kBaseCacheWrapper
	 */
	protected static $multipartCache;

	const MULTIPART_CACHE_POSTFIX = '.multipart';


	public function __construct(UploadToken $uploadToken, $finalChunk = true)
	{
		KalturaLog::info("Init for upload token id [{$uploadToken->getId()}]");
		self::$sharedFsMgr = kSharedFileSystemMgr::getInstance();
		parent::__construct($uploadToken, $finalChunk);
		$this->initMultipartMemcache();
	}

	/**
	 * Resume the upload token with the uploaded file optionally at a given offset
	 *
	 * @param file $fileData
	 * @param float $resumeAt
	 * @return string
	 * @throws PropelException
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
				return $this->handleOptimizedResume($fileData, $resumeAt, $chunkSize);
			}
			else
			{
				throw new kUploadTokenException("chunk size [$chunkSize] is less then minimum expected size [$minimumChunkSize]", kUploadTokenException::UPLOAD_TOKEN_INVALID_CHUNK_SIZE);
			}
		}
		*/
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
		{
			$extension = self::NO_EXTENSION_IDENTIFIER;
		}

		return myContentStorage::getFSUploadsPath().substr($uploadTokenId, -2).'/'.$uploadTokenId.'/0.'.$extension;
	}

	/**
	 * handle upload token resume when chunk size is at list in the size of minimumChunkSize
	 *
	 * @param $fileData
	 * @param $resumeAt
	 * @param $chunkSize
	 * @return string
	 * @throws PropelException
	 * @throws kUploadTokenException
	 */
	protected function handleOptimizedResume($fileData, $resumeAt, $chunkSize)
	{
		$uploadFilePath = dirname($this->_uploadToken->getUploadTempPath());
		$sourceFilePath = $fileData['tmp_name'];

		if ($resumeAt != -1)
		{
			$currentFileSize = $this->addChunkByPosition($sourceFilePath, $chunkSize, $resumeAt, $uploadFilePath);
		}
		else
		{
			$currentFileSize = $this->addChunkSequentially($sourceFilePath, $chunkSize, $uploadFilePath);
		}

		return $currentFileSize;
	}

	/**
	 * set initial values for uploadToken multipart upload record in cache
	 *
	 * @throws kUploadTokenException
	 */
	protected function initMultipartMemcache()
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_UPLOAD_TOKEN);
		if (!$cache)
		{
			throw new kUploadTokenException("Cache instance required for multipart upload functionality Could not initiated", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_CACHE_NOT_INITIALIZED);
		}
		self::$multipartCache = $cache;
		$multipartInfo = array('uploadedFileSize' => 0);
		self::$multipartCache->add($this->_uploadToken->getId() . self::MULTIPART_CACHE_POSTFIX, $multipartInfo);
	}

	/**
	 * allocate next part number in cache
	 *
	 * @return int
	 * @throws kUploadTokenException
	 */
	public function allocatePartNumber()
	{
		$multipartInfo = $this->getMultipartCache();
		$allocatedPart = ($multipartInfo && isset($multipartInfo['partNumberAllocation'])) ? $multipartInfo['partNumberAllocation'] : 1;
		$multipartInfo['partNumberAllocation'] = $allocatedPart + 1;
		$this->setMultipartCache($multipartInfo);
		return $allocatedPart;
	}

	/**
	 * update uploaded waiting multipart upload parts in cache
	 *
	 * @param $chunkFilePath
	 * @param $resumeAt
	 * @param $partSize
	 * @throws kUploadTokenException
	 */
	public function updateWaitingParts($chunkFilePath, $resumeAt, $partSize)
	{
		$multipartInfo = $this->getMultipartCache();
		$multipartInfo['waitingParts'][$resumeAt] = array('partPath' => $chunkFilePath, 'partSize' =>  $partSize);
		KalturaLog::debug("Setting partResumeAt [$resumeAt] & partPath [$chunkFilePath]  & partSize [$partSize]");
		$this->setMultipartCache($multipartInfo);
	}

	/**
	 * update the array for parts uploaded to the full file multipart upload in cache
	 *
	 * @param $partNumber
	 * @param $etag
	 * @throws kUploadTokenException
	 */
	public function updateFinalMultipartParts($partNumber, $etag)
	{
		$multipartInfo = $this->getMultipartCache();
		$multipartInfo['Parts'][$partNumber] = array(
			'PartNumber' => $partNumber,
			'ETag' => $etag,
		);
		$this->setMultipartCache($multipartInfo);
	}


	/**
	 * update multipart upload uploadedFileSize in cache
	 *
	 * @param $uploadedFileSize
	 * @throws kUploadTokenException
	 */
	public function updateUploadedFileSize($uploadedFileSize)
	{
		$multipartInfo = $this->getMultipartCache();
		if($multipartInfo['uploadedFileSize'] > $uploadedFileSize)
		{
			return;
		}

		$this->_uploadToken->setUploadedFileSize($uploadedFileSize);
		$multipartInfo['uploadedFileSize'] = $uploadedFileSize;
		KalturaLog::debug("Setting uploaded file size updated in cache to: [$uploadedFileSize]");
		$this->setMultipartCache($multipartInfo);
	}

	/**
	 * get multipart upload info from cache if exists
	 *
	 * @return mixed
	 * @throws kUploadTokenException
	 */
	protected function getMultipartCache()
	{
		$multipartInfo = self::$multipartCache->get($this->_uploadToken->getId() . self::MULTIPART_CACHE_POSTFIX);
		if (!$multipartInfo)
		{
			throw new kUploadTokenException("Failed to Update part in cache", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_CACHE_FAILURE);
		}
		return $multipartInfo;
	}

	/**
	 * set multipart upload info to cache
	 *
	 * @param $multipartInfo
	 * @return mixed
	 * @throws kUploadTokenException
	 */
	protected function setMultipartCache($multipartInfo)
	{
		$updatedMultipartInfo = self::$multipartCache->set($this->_uploadToken->getId() . self::MULTIPART_CACHE_POSTFIX, $multipartInfo);
		if (!$updatedMultipartInfo)
		{
			throw new kUploadTokenException("Failed to Update part in cache", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_CACHE_FAILURE);
		}
		return $updatedMultipartInfo;
	}

	/**
	 * append chunk at the end of existing multipart upload by uploading it and than copy
	 *
	 * @param $srcPath
	 * @param $resumeAt
	 * @param $chunkSize
	 * @param $chunkFilePath
	 * @param bool $shouldUpload
	 * @return mixed
	 * @throws kUploadTokenException
	 */
	protected function appendChunk($srcPath, $resumeAt, $chunkSize, $chunkFilePath, $shouldUpload = true)
	{
		$finalPath = $this->getFinalFilePath();

		if($shouldUpload)
		{
			KalturaLog::info("Upload file from [{$srcPath}] to [{$chunkFilePath}]");

			// upload part using new multipart upload to save original number of chunks uploaded by the user
			$uploadSuccess = self::$sharedFsMgr->getFileFromResource($srcPath, $chunkFilePath);
			if(!$uploadSuccess)
			{
				throw new kUploadTokenException("Failed to upload part [{$srcPath}] to [{$chunkFilePath}]", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
			}
		}

		$partNumber = kLock::runLocked($this->_uploadToken->getId(), array($this, 'allocatePartNumber'), array(), 5);
		// copy uploaded part and add it to the full file multipart upload as one part
		$copySuccess = self::$sharedFsMgr->multipartUploadPartCopy($this->_uploadToken->getUploadId(), $partNumber, $chunkFilePath, $finalPath);
		if(!$copySuccess)
		{
			throw new kUploadTokenException("Failed to copy part from [{$chunkFilePath}] to multipart in [{$finalPath}]", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
		}

		kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateFinalMultipartParts'), array($partNumber, $copySuccess['CopyPartResult']['ETag']), 5);
		$uploadedFileSize = $resumeAt + $chunkSize;
		KalturaLog::info("Updated file size: [{$uploadedFileSize}] resumeAt [$resumeAt] chunkSize [$chunkSize]");
		return $uploadedFileSize;
	}

	/**
	 * add chunk sequentially at the end of file
	 *
	 * @param $sourceFilePath
	 * @param $chunkSize
	 * @param $chunkFilePath
	 * @return string
	 * @throws kUploadTokenException
	 */
	protected function addChunkSequentially($sourceFilePath, $chunkSize, $chunkFilePath)
	{
		$multipartInfo = $this->getMultipartCache();
		$chunkFilePath = "$chunkFilePath/" . $multipartInfo['uploadedFileSize'];
		$currentFileSize = $this->appendChunk($sourceFilePath, $this->_uploadToken->getUploadedFileSize(), $chunkSize, $chunkFilePath);
		kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateUploadedFileSize'), array($currentFileSize), 5);

		if($this->_autoFinalize && $this->_uploadToken->getFileSize() >= $currentFileSize)
		{
			$this->_finalChunk = true;
		}
		return $currentFileSize;
	}

	/**
	 * add chunk to existing multipart upload by the resumeAt position given by the user
	 * support upload chunks in parallel
	 *
	 * @param $sourceFilePath
	 * @param $chunkSize
	 * @param $resumeAt
	 * @param $uploadFilePath
	 * @return mixed
	 * @throws PropelException
	 * @throws kUploadTokenException
	 */
	protected function addChunkByPosition($sourceFilePath, $chunkSize, $resumeAt, $uploadFilePath)
	{
		$chunkFilePath = "$uploadFilePath/$resumeAt";
		// support backwards compatibility of overriding a final chunk at the offset zero
		$verifyFinalChunk = $this->_finalChunk && $resumeAt > 0;

		// if this is the final chunk the expected file size would be the resume position + the last chunk size
		$expectedFileSize = $verifyFinalChunk ? ($resumeAt + $chunkSize) : 0;

		$multipartCacheInfo = $this->getMultipartCache();
		$currentFileSize = $multipartCacheInfo['uploadedFileSize'];

		if($this->_autoFinalize && $this->checkIsFinalChunk($chunkSize))
		{
			$verifyFinalChunk = true;
			$expectedFileSize = $this->_uploadToken->getFileSize();
		}

		if($resumeAt == $currentFileSize)
		{
			$currentFileSize = $this->appendChunk($sourceFilePath, $resumeAt, $chunkSize, $chunkFilePath);
		}
		else
		{
			$currentFileSize = $this->addUploadedChunksToFinalFile($chunkSize, $expectedFileSize, $verifyFinalChunk, $resumeAt);
			if($resumeAt == $currentFileSize)
			{
				$currentFileSize = $this->appendChunk($sourceFilePath, $resumeAt, $chunkSize, $chunkFilePath);
			}
			else
			{
				$this->uploadChunk($sourceFilePath, $chunkFilePath, $chunkSize, $resumeAt);
			}

		}

		kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateUploadedFileSize'), array($currentFileSize), 5);

		if ($verifyFinalChunk && $currentFileSize != $expectedFileSize)
		{
			throw new kUploadTokenException("final size $currentFileSize failed to match expected size $expectedFileSize", kUploadTokenException::UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE);
		}

		return $currentFileSize;
	}

	/**
	 * upload chunk and save its location to cache for future appending
	 *
	 * @param $srcPath
	 * @param $destPath
	 * @param $chunkSize
	 * @param $resumeAt
	 * @throws kUploadTokenException
	 */
	protected function uploadChunk($srcPath, $destPath, $chunkSize, $resumeAt)
	{
		KalturaLog::info("upload file from [{$srcPath}] to [{$destPath}]");
		$uploadSuccess = self::$sharedFsMgr->getFileFromResource($srcPath, $destPath);
		if(!$uploadSuccess)
		{
			throw new kUploadTokenException("Failed to upload part [{$srcPath}] to [{$destPath}]", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
		}
		kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateWaitingParts'), array($destPath, $resumeAt, $chunkSize), 5);
	}


	/**
	 * concat to the end of files chunks that were already uploaded and their resumeAt values conects
	 *
	 * @param $chunkFilePath
	 * @param $chunkSize
	 * @param $expectedFileSize
	 * @param $verifyFinalChunk
	 * @param $currentFileSize
	 * @return mixed
	 * @throws Exception
	 */
	protected function addUploadedChunksToFinalFile($chunkSize, $expectedFileSize, $verifyFinalChunk, $chunkResumeAt)
	{
		$expectedFileSize = ($expectedFileSize > 0) ? $expectedFileSize -  $chunkSize : $expectedFileSize;
		$uploadFinalChunkMaxAppendTime = kConf::get('upload_final_chunk_max_append_time', 'local', 30);

		// if finalChunk, try appending chunks till reaching expected file size for up to 30 seconds while sleeping for 1 second each iteration
		$count = 0;
		do
		{
			if ($count ++)
				Sleep(1);

			$multipartCacheInfo = $this->getMultipartCache();
			$waitingParts = $multipartCacheInfo['waitingParts'];
			$uploadedSize = $multipartCacheInfo['uploadedFileSize'];
			$currentFileSize = $uploadedSize;

			list($concatSize, $chunksToUpload) = $this->getWaitingChunks($chunkResumeAt, $waitingParts, $uploadedSize);
			if($concatSize == $chunkResumeAt)
			{
				$currentFileSize = $this->appendWaitingChunks($chunksToUpload, $chunkResumeAt);
			}

			KalturaLog::log("handleResume iteration: $count  chunkSize: $chunkSize finalChunk: {$this->_finalChunk} currentFileSize: $currentFileSize expectedFileSize: $expectedFileSize");
		}
		while ($verifyFinalChunk && $currentFileSize != $expectedFileSize && $count < $uploadFinalChunkMaxAppendTime);

		return $currentFileSize;
	}

	/**
	 * get the chunks waiting for upload to the full file multipartUpload
	 *
	 * @param $chunkResumeAt
	 * @param $parts
	 * @param $uploadedSize
	 * @return mixed
	 */
	protected function getWaitingChunks($chunkResumeAt, $parts, $uploadedSize)
	{
		$concatSize = $uploadedSize;
		$chunksToUpload = array();

		$currentPart = isset($parts[$uploadedSize]) ? $parts[$uploadedSize] : null;
		while($currentPart)
		{
			$chunksToUpload[] = $currentPart;
			$partSize = $currentPart['partSize'];
			$concatSize += $partSize;

			//we couldn't find the next concatenating chunk or we found the full concatenation until the given resumeAt position
			if(!isset( $parts[$concatSize]) || $concatSize == $chunkResumeAt)
			{
				break;
			}

			$currentPart = $parts[$concatSize];
		}
		KalturaLog::debug("Max size that can be appended is: [$concatSize]");
		KalturaLog::debug("Chunks to upload: " . print_r($chunksToUpload, true));
		return array($concatSize, $chunksToUpload);
	}

	/**
	 * append the chunks waiting for upload until $currentFileSize
	 *
	 * @param $chunksToUpload
	 * @param $currentFileSize
	 * @return mixed
	 * @throws kUploadTokenException
	 */
	protected function appendWaitingChunks($chunksToUpload, $currentFileSize)
	{
		foreach ($chunksToUpload as $nextChunks)
		{
			$currentFileSize = $this->appendChunk(null, $currentFileSize, $nextChunks['partSize'], $nextChunks['partPath'], false);
		}
		return $currentFileSize;
	}

	protected function startFullFileUpload($uploadFilePath, $fileSize, $resumeAt)
	{
		$finalFilePath = $finalFilePath = $this->getFinalFilePath();
		$uploadId = self::$sharedFsMgr->createMultipartUpload($finalFilePath);
		if(!$uploadId)
		{
			throw new kUploadTokenException("multipart upload error during upload token closer", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
		}
		$this->_uploadToken->setUploadId($uploadId);

		$currentFileSize = 0;
		if($resumeAt == -1 || $this->_finalChunk)
		{
			$currentFileSize = $this->appendChunk(null, $this->_uploadToken->getUploadedFileSize(), $fileSize, $uploadFilePath, false);
			kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateUploadedFileSize'), array($currentFileSize), 5);
		}
		else if ($resumeAt == 0)
		{
			kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateWaitingParts'), array($uploadFilePath, $resumeAt, $fileSize), 5);
		}

		return $currentFileSize;
	}

	protected function closeFullFileUpload()
	{
		$finalFilePath = $this->getFinalFilePath();
		$multipartCacheInfo = $this->getMultipartCache();
		$parts['Parts'] = $multipartCacheInfo['Parts'];

		self::$sharedFsMgr->completeMultipartUpload($finalFilePath, $this->_uploadToken->getUploadId(), $parts);
		$this->_uploadToken->setUploadTempPath($finalFilePath);
	}

	/*
	 * get the final path for the multipart full upload
	 *
	 */
	protected function getFinalFilePath()
	{
		$uploadFolderPath = dirname($this->_uploadToken->getUploadTempPath());
		$finalFilePath = $uploadFolderPath .  '/full_file.' . $this->getFileExtension($this->_uploadToken->getFileName());
		return $finalFilePath;
	}

}