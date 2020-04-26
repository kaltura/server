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

	/**
	 * do we need to make inner concatenation of chunks to get to AWS minimum supported chunk size
	 * @var bool
	 */
	protected static $optimized;

	/**
	 * remaining data we need to concatenate to final chunk in order to avoid breaking chunk limit
	 * @var array
	 */
	protected static $lastChunk;

	const MULTIPART_CACHE_POSTFIX = '.multipart';

	const WAITING_PARTS_CACHE_POSTFIX = '.waitingParts';


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
	 * @throws kUploadTokenException
	 */
	protected function handleResume($fileData, $resumeAt)
	{
		$uploadFilePath = dirname($this->_uploadToken->getUploadTempPath());
		$sourceFilePath = $fileData['tmp_name'];
		$chunkSize = kFile::fileSize($fileData['tmp_name']);

		$this->isOptimizedResume($chunkSize);

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

		return kFile::createUniqueFilePath(myContentStorage::getFSTempUploadsPath(), true) . '0.' .$extension;
	}

	/**
	 * check if minimum chunk size is set on the upload token and if the uploaded chunk is at least in this size
	 *
	 * @param $chunkSize
	 * @throws kUploadTokenException
	 */
	protected function isOptimizedResume($chunkSize)
	{
		$minimumChunkSize = $this->_uploadToken->getMinimumChunkSize();
		if($minimumChunkSize)
		{
			if(($minimumChunkSize >= kS3SharedFileSystemMgr::MIN_PART_SIZE && $chunkSize >= $minimumChunkSize) || $this->_finalChunk)
			{
				self::$optimized = true;
				return;
			}
			else
			{
				throw new kUploadTokenException("chunk size [$chunkSize] is less then minimum expected size [$minimumChunkSize]", kUploadTokenException::UPLOAD_TOKEN_INVALID_CHUNK_SIZE);
			}
		}

		self::$optimized = false;
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
		self::$multipartCache->add($this->_uploadToken->getId() . self::WAITING_PARTS_CACHE_POSTFIX, array('waitingParts' => array()));
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
		$multipartInfo = $this->getWaitingPartsCache();
		$multipartInfo['waitingParts'][$resumeAt] = array('partPath' => $chunkFilePath, 'partSize' =>  $partSize);
		KalturaLog::debug("Setting partResumeAt [$resumeAt] & partPath [$chunkFilePath]  & partSize [$partSize]");
		$this->setWaitingPartsCache($multipartInfo);
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
	 * @return array
	 * @throws kUploadTokenException
	 */
	protected function getMultipartCache()
	{
		$multipartInfo = self::$multipartCache->get($this->_uploadToken->getId() . self::MULTIPART_CACHE_POSTFIX);
		if (!$multipartInfo)
		{
			throw new kUploadTokenException("Failed to get part from cache", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_CACHE_FAILURE);
		}
		return $multipartInfo;
	}

	/**
	 * get multipart waiting parts info from cache if exists
	 *
	 * @return array
	 * @throws kUploadTokenException
	 */
	protected function getWaitingPartsCache()
	{
		$multipartInfo = self::$multipartCache->get($this->_uploadToken->getId() . self::WAITING_PARTS_CACHE_POSTFIX);
		if (!$multipartInfo)
		{
			throw new kUploadTokenException("Failed to get part from cache", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_CACHE_FAILURE);
		}
		return $multipartInfo;
	}

	/**
	 * set multipart waiting parts info to cache
	 *
	 * @param $multipartInfo
	 * @return bool
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
	 * set multipart upload info to cache
	 *
	 * @param $multipartInfo
	 * @return bool
	 * @throws kUploadTokenException
	 */
	protected function setWaitingPartsCache($multipartInfo)
	{
		$updatedMultipartInfo = self::$multipartCache->set($this->_uploadToken->getId() . self::WAITING_PARTS_CACHE_POSTFIX, $multipartInfo);
		if (!$updatedMultipartInfo)
		{
			throw new kUploadTokenException("Failed to Update waiting parts in cache", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_CACHE_FAILURE);
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

		$partNumber = kLock::runLocked($this->_uploadToken->getId(), array($this, 'allocatePartNumber'));

		// copy uploaded part and add it to the full file multipart upload as one part
		$copySuccess = self::$sharedFsMgr->multipartUploadPartCopy($this->_uploadToken->getUploadId(), $partNumber, $chunkFilePath, $finalPath);
		if(!$copySuccess)
		{
			throw new kUploadTokenException("Failed to copy part from [{$chunkFilePath}] to multipart in [{$finalPath}]", kUploadTokenException::UPLOAD_TOKEN_MULTIPART_UPLOAD_ERROR);
		}

		kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateFinalMultipartParts'), array($partNumber, $copySuccess['CopyPartResult']['ETag']));
		$uploadedFileSize = $resumeAt + $chunkSize;
		KalturaLog::info("Updated file size: [{$uploadedFileSize}] resumeAt [$resumeAt] chunkSize [$chunkSize]");
		return $uploadedFileSize;
	}

	/**
	 * add chunk sequentially at the end of file
	 *
	 * @param $sourceFilePath
	 * @param $chunkSize
	 * @param $originalChunkFilePath
	 * @return string
	 * @throws kUploadTokenException
	 */
	protected function addChunkSequentially($sourceFilePath, $chunkSize, $originalChunkFilePath)
	{
		$multipartInfo = $this->getMultipartCache();
		$chunkFilePath = "$originalChunkFilePath/" . $multipartInfo['uploadedFileSize'];


		if(self::$optimized)
		{
			$currentFileSize = $this->appendChunk($sourceFilePath, $this->_uploadToken->getUploadedFileSize(), $chunkSize, $chunkFilePath);
		}
		else
		{
			list($concatSize, $chunksToUpload) = $this->getWaitingChunks(-1, $multipartInfo['uploadedFileSize']);
			$chunkFilePath = "$originalChunkFilePath/" . $concatSize;
			$currentFileSize = $this->addUploadedChunksToFinalFile($chunkSize, 0, false, $concatSize);

			// handle the chunk given in the request after appending available existing chunks
			if($chunkSize >= kS3SharedFileSystemMgr::MIN_PART_SIZE || $this->_finalChunk)
			{
				$currentFileSize = $this->appendAfterWaitingPartsConcat($sourceFilePath, $chunkSize, $chunkFilePath, $multipartInfo['uploadedFileSize']);
			}
			else
			{
				$this->uploadChunk($sourceFilePath, $chunkFilePath, $chunkSize, $concatSize);
			}
		}

		kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateUploadedFileSize'), array($currentFileSize));

		if($this->_autoFinalize && $this->_uploadToken->getFileSize() <= $currentFileSize)
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
	 * @return int
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
		$uploadedFileSize = $multipartCacheInfo['uploadedFileSize'];

		if($this->_autoFinalize && $this->checkIsFinalChunk($chunkSize))
		{
			$verifyFinalChunk = true;
			$expectedFileSize = $this->_uploadToken->getFileSize();
		}

		if($resumeAt == $uploadedFileSize && $chunkSize >= kS3SharedFileSystemMgr::MIN_PART_SIZE)
		{
			$currentFileSize = $this->appendChunk($sourceFilePath, $resumeAt, $chunkSize, $chunkFilePath);
		}
		else
		{
			$currentFileSize = $this->addUploadedChunksToFinalFile($chunkSize, $expectedFileSize, $verifyFinalChunk, $resumeAt);

			// handle the chunk given in the request after appending available existing chunks
			if($resumeAt == $currentFileSize && ($chunkSize >= kS3SharedFileSystemMgr::MIN_PART_SIZE || $this->_finalChunk))
			{
				$currentFileSize = $this->appendAfterWaitingPartsConcat($sourceFilePath, $chunkSize, $chunkFilePath, $resumeAt);
			}
			else
			{
				$this->uploadChunk($sourceFilePath, $chunkFilePath, $chunkSize, $resumeAt);
			}
		}

		kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateUploadedFileSize'), array($currentFileSize));

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
		kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateWaitingParts'), array($destPath, $resumeAt, $chunkSize));
	}


	/**
	 * concat to the end of files chunks that were already uploaded and their resumeAt values conects
	 *
	 * @param $chunkSize
	 * @param $expectedFileSize
	 * @param $verifyFinalChunk
	 * @param $chunkResumeAt
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
			$uploadedSize = $multipartCacheInfo['uploadedFileSize'];

			list($concatSize, $chunksToUpload) = $this->getWaitingChunks($chunkResumeAt, $uploadedSize);
			if($concatSize == $chunkResumeAt)
			{
				$uploadedSize = $this->appendWaitingChunks($chunksToUpload, $uploadedSize);
				if(self::$lastChunk)
				{
					$uploadedSize += self::$lastChunk['partSize'];
				}
			}

			KalturaLog::log("handleResume iteration: $count  chunkSize: $chunkSize finalChunk: {$this->_finalChunk} currentFileSize: $uploadedSize expectedFileSize: $expectedFileSize");
		}
		while ($verifyFinalChunk && $uploadedSize != $expectedFileSize && $count < $uploadFinalChunkMaxAppendTime);

		return $uploadedSize;
	}

	/**
	 * get the chunks waiting for upload to the full file multipartUpload
	 *
	 * @param $chunkResumeAt send -1 for max concat value
	 * @param $uploadedSize
	 * @return array
	 * @throws kUploadTokenException
	 */
	protected function getWaitingChunks($chunkResumeAt, $uploadedSize)
	{
		$waitingPartsCacheInfo = $this->getWaitingPartsCache();
		$parts = $waitingPartsCacheInfo['waitingParts'];
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
		if(!self::$optimized)
		{
			$chunksToUpload = $this->getConcatenatedChunksToUpload($chunksToUpload, $currentFileSize);
		}

		foreach ($chunksToUpload as $nextChunk)
		{
			$currentFileSize = $this->appendChunk(null, $currentFileSize, $nextChunk['partSize'], $nextChunk['partPath'], false);
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
		if(($resumeAt == -1 && $fileSize >= kS3SharedFileSystemMgr::MIN_PART_SIZE) || $this->_finalChunk)
		{
			$currentFileSize = $this->appendChunk(null, $this->_uploadToken->getUploadedFileSize(), $fileSize, $uploadFilePath, false);
			kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateUploadedFileSize'), array($currentFileSize));
		}
		else if ($resumeAt == -1 && $fileSize < kS3SharedFileSystemMgr::MIN_PART_SIZE || $resumeAt == 0)
		{
			$resumeAt = max($resumeAt, 0);
			kLock::runLocked($this->_uploadToken->getId(), array($this, 'updateWaitingParts'), array($uploadFilePath, $resumeAt, $fileSize));
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

	/**
	 * get the final path for the multipart full upload
	 *
	 * @return string
	 */
	protected function getFinalFilePath()
	{
		$finalPath = $this->_uploadToken->getFinalFilePath();
		if($finalPath)
		{
			return $finalPath;
		}

		return kFile::createUniqueFilePath(myContentStorage::getFSUploadsPath()) . $this->getFileExtension($this->_uploadToken->getFileName());
	}

	/**
	 * check if we can concat chunks and get the current uploaded part size (resumeAt) and if so return array containing valid chunks to upload
	 *
	 * @param $chunksToUpload
	 * @param $currentFileSize
	 * @return array
	 */
	protected function getConcatenatedChunksToUpload($chunksToUpload, $currentFileSize)
	{
		$partsToConcat = array();
		$partNum = 1;
		$concatenatedChunkSize = 0;

		foreach ($chunksToUpload as $nextChunk)
		{
			if($concatenatedChunkSize >= kS3SharedFileSystemMgr::MIN_PART_SIZE && !$this->reachedMaxPartsLimit())
			{
				$partNum++;
				$concatenatedChunkSize = 0;
			}

			$partsToConcat[$partNum][] = $nextChunk;
			$concatenatedChunkSize += $nextChunk['partSize'];
		}
		KalturaLog::debug("concatenated Chunks To Upload: " . print_r($partsToConcat, true));

		if($concatenatedChunkSize < kS3SharedFileSystemMgr::MIN_PART_SIZE && !$this->_finalChunk)
		{
			return array();
		}

		return $this->createChunksFromConcatenatedSmallParts($partsToConcat, $currentFileSize);
	}

	/**
	 * create array of chunks to upload that their size is bigger then s3 chunk size for upload minimum
	 *
	 * @param $partsToConcat
	 * @param $currentFileSize
	 * @return array
	 */
	protected function createChunksFromConcatenatedSmallParts($partsToConcat, $currentFileSize)
	{
		$chunksToUpload = array();
		foreach ($partsToConcat as $partNum => $innerParts)
		{
			if(sizeof($innerParts) == 1)
			{
				$uploadFilePath = $innerParts[0]['partPath'];
				$partSize = $innerParts[0]['partSize'];
				KalturaLog::debug("Part size: $partSize partNum:  $partNum");
			}
			else
			{
				$tmpFilePath = '/tmp/' . $this->_uploadToken->getId() . '_' . $currentFileSize . '_part_' . $partNum;
				$uploadFilePath = dirname($this->_uploadToken->getUploadTempPath()) . '/' . $currentFileSize . '_part_' . $partNum;
				$partSize = $this->createLocalChunk($tmpFilePath, $innerParts);
				KalturaLog::debug("Part size: $partSize partNum:  $partNum tmpPath: $tmpFilePath");
				self::$sharedFsMgr->getFileFromResource($tmpFilePath, $uploadFilePath);
			}

			if($partSize < kS3SharedFileSystemMgr::MIN_PART_SIZE)
			{
				self::$lastChunk =  array('partPath' => $uploadFilePath, 'partSize' =>  $partSize);
			}
			else
			{
				$chunksToUpload[$partNum] = array('partPath' => $uploadFilePath, 'partSize' =>  $partSize);
			}
		}

		return $chunksToUpload;
	}

	/**
	 * init stream wrapper and retrieve resource for chunk url
	 *
	 * @param $path
	 * @return bool|resource
	 */
	protected function getChunkResource($path)
	{
		stream_wrapper_restore('http');
		stream_wrapper_restore('https');
		if(kFile::isSharedPath($path))
		{
			$path = self::$sharedFsMgr->realPath($path);
		}

		return fopen($path, 'rb');
	}

	/**
	 * write the content of small chunks to one concatenated local file
	 *
	 *
	 * @param $path
	 * @param $destFH
	 */
	protected function increaseLocalTmpChunk($path, $destFH)
	{
		$sourceFH = $this->getChunkResource($path);
		if($sourceFH)
		{
			while (!feof($sourceFH))
			{
				$body = stream_get_contents($sourceFH, 16 * 1024 * 1024);
				fwrite($destFH, $body);
			}
			fclose($sourceFH);
		}
	}

	/**
	 * append the chunk given in the request after appending available existing chunks in waiting parts array
	 *
	 * @param $sourceFilePath
	 * @param $chunkSize
	 * @param $chunkFilePath
	 * @param $resumeAt
	 * @return int
	 * @throws kUploadTokenException
	 */
	protected function appendAfterWaitingPartsConcat($sourceFilePath, $chunkSize, $chunkFilePath, $resumeAt)
	{
		if (!self::$optimized && self::$lastChunk)
		{
			$tmpFilePath = '/tmp/' . $this->_uploadToken->getId() . '_lastPart';

			$destFH = fopen($tmpFilePath, "w");
			$this->increaseLocalTmpChunk(self::$lastChunk['partPath'], $destFH);
			$this->increaseLocalTmpChunk($sourceFilePath, $destFH);
			self::$sharedFsMgr->getFileFromResource($tmpFilePath, $chunkFilePath);

			$partSize = self::$lastChunk['partSize'] + $chunkSize;
			KalturaLog::debug("Part size: $partSize");
			$lastChunkResumeAt = $resumeAt - self::$lastChunk['partSize'];

			return $this->appendChunk(null, $lastChunkResumeAt, $partSize, $chunkFilePath, false);
		}

		return $this->appendChunk($sourceFilePath, $resumeAt, $chunkSize, $chunkFilePath);
	}

	/**
	 * create local chunk from concatenated small parts
	 *
	 * @param $tmpFilePath
	 * @param $part
	 * @return int
	 */
	protected function createLocalChunk($tmpFilePath, $part)
	{
		$partSize = 0;
		$destFH = fopen($tmpFilePath, "w");
		foreach ($part as $smallChunk)
		{
			$this->increaseLocalTmpChunk($smallChunk['partPath'], $destFH);
			$partSize += $smallChunk['partSize'];
		}
		fclose($destFH);
		return $partSize;
	}

	/**
	 * check if we reached the max upload parts for the multipart
	 *
	 * @return bool
	 * @throws kUploadTokenException
	 */
	protected function reachedMaxPartsLimit()
	{
		// we will need max of 2 more parts to upload for the full remaining part and for the final upload part
		$maxParts = kS3SharedFileSystemMgr::MAX_PARTS_NUMBER - 2;
		$multipartInfo = $this->getMultipartCache();

		if($multipartInfo['partNumberAllocation'] >= $maxParts)
		{
			return true;
		}
		return false;
	}

}