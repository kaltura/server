<?php
class kUploadTokenMgr
{
	const NO_EXTENSION_IDENTIFIER = 'noext';
	const AUTO_FINALIZE_CACHE_TTL = dateUtils::MONTH;
	const MAX_AUTO_FINALIZE_RETIRES = 5;
	
	/**
	 * @var UploadToken
	 */
	protected $_uploadToken;
	
	/**
	 * @var bool
	 */
	protected $_autoFinalize;
	
	/**
	 * @var bool
	 */
	protected $_finalChunk;
	
	/**
	 * @var kBaseCacheWrapper
	 */
	protected $_autoFinalizeCache;
	
	/**
	 * Construct new upload token manager for the upload token object
	 * @param UploadToken $uploadToken
	 */
	public function __construct(UploadToken $uploadToken, $finalChunk = true)
	{
		KalturaLog::info("Init for upload token id [{$uploadToken->getId()}]");
		$this->_uploadToken = $uploadToken;
		$this->_finalChunk = $finalChunk;
	}
	
	private function initUploadTokenMemcache()
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_UPLOAD_TOKEN);
		if (!$cache)
			throw new kUploadTokenException("Cache instance required for AutoFinalize functionality Could not initiated", kUploadTokenException::UPLOAD_TOKEN_AUTO_FINALIZE_CACHE_NOT_INITIALIZED);
		
		$this->_autoFinalizeCache = $cache;
		$this->_autoFinalizeCache->add($this->_uploadToken->getId() . ".retries", self::MAX_AUTO_FINALIZE_RETIRES);
	}
	
	/**
	 * Set default values and save the new upload token
	 */
	public function saveAsNewUploadToken()
	{
		$this->_uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_PENDING);
		$this->_uploadToken->setUploadedFileSize(null);
		$this->_uploadToken->setUploadTempPath(null);
		$this->_uploadToken->setUserIp(requestUtils::getRemoteAddress());
		$this->_uploadToken->setDc(kDataCenterMgr::getCurrentDcId());
		$this->_uploadToken->save();
	}
	
	/**
	 * Get the current upload token used by the manager
	 */
	public function getUploadToken()
	{
		return $this->_uploadToken;
	}
	
	/**
	 * Upload a file to the current upload token
	 * @param file $fileData
	 * @param bool $resume
	 * @param bool $finalChunk
	 * @param int $resumeAt
	 * @throw kUploadTokenException
	 */
	public function uploadFileToToken($fileData, $resume = false, $resumeAt = -1)
	{
		$this->_autoFinalize = $this->_uploadToken->getAutoFinalize();
		if($this->_autoFinalize)
			$this->initUploadTokenMemcache();
		
		$allowedStatuses = array(UploadToken::UPLOAD_TOKEN_PENDING, UploadToken::UPLOAD_TOKEN_PARTIAL_UPLOAD);
		if (!in_array($this->_uploadToken->getStatus(), $allowedStatuses, true))
			throw new kUploadTokenException("Invalid upload token status", kUploadTokenException::UPLOAD_TOKEN_INVALID_STATUS);

		$this->updateFileName($fileData);
			
		try
		{
			$this->checkIfFileIsValid($fileData);
		}
		catch(kUploadTokenException $ex)
		{
			if(!$resume && $this->_finalChunk)
				kFlowHelper::handleUploadFailed($this->_uploadToken);
			
			$this->tryMoveToErrors($fileData);
			throw $ex;
		}
		
		if ($resume)
			$fileSize = $this->handleResume($fileData, $resumeAt);
		else
		{
			$this->handleMoveFile($fileData);
			$fileSize = kFile::fileSize($this->_uploadToken->getUploadTempPath());
		}
		
		if ($this->_finalChunk)
		{
			if (PermissionPeer::isValidForPartner(PermissionName::FEATURE_FILE_TYPE_RESTRICTION_PERMISSION, kCurrentContext::getCurrentPartnerId())
				&& !$this->checkIfFileIsAllowed())
			{
				kFlowHelper::handleUploadFailed($this->_uploadToken);
				throw new kUploadTokenException("Restricted upload token file type", kUploadTokenException::UPLOAD_TOKEN_FILE_TYPE_RESTRICTED);
			}
			$this->_uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_FULL_UPLOAD);
		}
		else 
		{
			$this->_uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_PARTIAL_UPLOAD);
		}
		
		$this->_uploadToken->setUploadedFileSize($fileSize);
		$this->_uploadToken->setDc(kDataCenterMgr::getCurrentDcId());
		
		$this->_uploadToken->save();
		
		$this->addTrackEntry();
	}
	
	/**
	 * Delete the current upload token
	 */
	public function deleteUploadToken()
	{
		$this->_uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_DELETED);
		$this->_uploadToken->save();
	}
	
	/**
	 * Adds track entry for investigations
	 */
	protected function addTrackEntry()
	{
		$te = new TrackEntry();
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_UPLOADED_FILE);
		$te->setParam1Str($this->_uploadToken->getId());
		$te->setParam2Str($this->_uploadToken->getFileName());
		$te->setParam3Str($this->_uploadToken->getUploadTempPath());
		$te->setDescription(__METHOD__ . ":" . __LINE__);
		TrackEntry::addTrackEntry($te);
	}
	
	/**
	 * Validate the file data
	 * @param file $fileData
	 */
	protected function checkIfFileIsValid($fileData)
	{
		// check file name
		$fileName = isset($fileData['name']) ? $fileData['name'] : null;
		if (!$fileName)
		{
			$msg = "The file name is missing for the uploaded file for token id [{$this->_uploadToken->getId()}]";
			KalturaLog::log($msg . ' ' . print_r($fileData, true));
			throw new kUploadTokenException($msg, kUploadTokenException::UPLOAD_TOKEN_FILE_NAME_IS_MISSING_FOR_UPLOADED_FILE);
		}

		// check for errors
		$error = isset($fileData['error']) ? $fileData['error'] : null;
		if ($error !== UPLOAD_ERR_OK)
		{
			$msg = "An uploaded error occurred for token id [{$this->_uploadToken->getId()}]";
			KalturaLog::log($msg . ' ' . print_r($fileData, true));
			throw new kUploadTokenException($msg, kUploadTokenException::UPLOAD_TOKEN_UPLOAD_ERROR_OCCURRED);
		}
		
		// check if is a real uploaded file
		$tempPath = isset($fileData['tmp_name']) ? $fileData['tmp_name'] : null; 
		if (!is_uploaded_file($tempPath))
		{
			$msg = "The uploaded file not valid for token id [{$this->_uploadToken->getId()}]";
			KalturaLog::log($msg . ' ' . print_r($fileData, true));
			throw new kUploadTokenException($msg, kUploadTokenException::UPLOAD_TOKEN_FILE_IS_NOT_VALID);
		}
	}

	/**
	 * Validate the file type
	 */
	protected function checkIfFileIsAllowed()
	{
		$uploadFilePath = $this->_uploadToken->getUploadTempPath();
		$fileType = kFile::mimeType($uploadFilePath);
		if ($fileType == 'application/octet-stream')//stream of byte - can be media or executable
		{
			$fileType = kFile::getMediaInfoFormat($uploadFilePath);
			if (empty($fileType))
				$fileType = kFile::findFileTypeByFileCmd($uploadFilePath);
		}

		$fileTypes = kConf::get('file_type');
		return in_array($fileType, $fileTypes['allowed']);
	}

	/**
	 * Updates the file name of the token (if empty) using the file name from the file data 
	 */
	protected function updateFileName($fileData)
	{
		if (!$this->_uploadToken->getFileName())
		{
			$this->_uploadToken->setFileName(isset($fileData['name']) ? $fileData['name'] : null);
			$this->_uploadToken->save();
		}
		
	}
	
	/**
	 * Returns the target upload path for upload token id and extension
	 * @param string $uploadTokenId
	 * @param string $extension
	 */
	protected function getUploadPath($uploadTokenId, $extension = '')
	{
		if (!$extension)
			$extension = self::NO_EXTENSION_IDENTIFIER;
			
		return myContentStorage::getFSUploadsPath().substr($uploadTokenId, -2).'/'.$uploadTokenId.'.'.$extension;
	}
	
	protected function tryMoveToErrors($fileData)
	{
		if (file_exists($fileData['tmp_name']))
		{
			$errorFilePath = $this->getUploadPath('error-'.$this->_uploadToken->getId(), microtime(true));
			rename($fileData['tmp_name'], $errorFilePath);
		}
	}

	/**
	 * Resume the upload token with the uploaded file optionally at a given offset
	 * 
	 * @param file $fileData        	
	 * @param bool $finalChunk        	
	 * @param float $resumeAt        	
	 */
	protected function handleResume($fileData, $resumeAt)
	{
		$uploadFilePath = $this->_uploadToken->getUploadTempPath();
		if (!file_exists($uploadFilePath))
			throw new kUploadTokenException("Temp file [$uploadFilePath] was not found when trying to resume", kUploadTokenException::UPLOAD_TOKEN_FILE_NOT_FOUND_FOR_RESUME);
		
		$sourceFilePath = $fileData['tmp_name'];
		
		if ($resumeAt != -1) // this may not be a sequential chunk added at the end of the file
		{
			// support backwards compatibility of overriding a final chunk at the offset zero  
			$verifyFinalChunk = $this->_finalChunk && $resumeAt > 0;
			
			// if this is the final chunk the expected file size would be the resume position + the last chunk size
			$chunkSize = filesize($sourceFilePath);
			$expectedFileSize = $verifyFinalChunk ? ($resumeAt + $chunkSize) : 0;

			$chunkFilePath = "$uploadFilePath.chunk.$resumeAt";
			$succeeded = rename($sourceFilePath, $chunkFilePath);
			
			if($this->_autoFinalize && $this->checkIsFinalChunk($chunkSize) && $succeeded)
			{
				$verifyFinalChunk = true;
				$expectedFileSize = $this->_uploadToken->getFileSize();
			}
			
			$uploadFinalChunkMaxAppendTime = kConf::get('upload_final_chunk_max_append_time', 'local', 30);
			
			// if finalChunk, try appending chunks till reaching expected file size for up to 30 seconds while sleeping for 1 second each iteration
			$count = 0;
			do {
				if ($count ++)
					Sleep(1);
				
				$currentFileSize = self::appendAvailableChunks($uploadFilePath);
				KalturaLog::log("handleResume iteration: $count chunk: $chunkFilePath size: $chunkSize finalChunk: {$this->_finalChunk} filesize: $currentFileSize expected: $expectedFileSize");
			} while ($verifyFinalChunk && $currentFileSize != $expectedFileSize && $count < $uploadFinalChunkMaxAppendTime);

			if ($verifyFinalChunk && $currentFileSize != $expectedFileSize)
				throw new kUploadTokenException("final size $currentFileSize failed to match expected size $expectedFileSize", kUploadTokenException::UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE);
		} else {
			$uploadFileResource = fopen($uploadFilePath, 'r+b');
			fseek($uploadFileResource, 0, SEEK_END);
			
			self::appendChunk($sourceFilePath, $uploadFileResource);
			
			$currentFileSize = ftell($uploadFileResource);
			
			if($this->_autoFinalize && $this->_uploadToken->getFileSize() >= $currentFileSize)
				$this->_finalChunk = true;
			
			fclose($uploadFileResource);
		}
		
		return $currentFileSize; 
	}
	
	private function checkIsFinalChunk($currentFileSize)
	{
		$cacheFileSizeValue = $this->_autoFinalizeCache->increment($this->_uploadToken->getId(), $currentFileSize);	
		if($cacheFileSizeValue >= $this->_uploadToken->getFileSize())
		{
			if($this->_autoFinalizeCache->add($this->_uploadToken->getId().".lock", "true", 30))
			{
				if($this->_autoFinalizeCache->decrement($this->_uploadToken->getId() . ".retries") == 0)
					throw new kUploadTokenException("Max retires reached when trying to auto finalize uploadToken", kUploadTokenException::UPLOAD_TOKEN_MAX_AUTO_FINALIZE_RETRIES_REACHED);
				
				$this->_finalChunk = true;
					return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Move the uploaded file
	 * @param unknown_type $fileData
	 */
	protected function handleMoveFile($fileData)
	{
		// get the upload path
		$extension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));

		// in firefox html5 upload the extension is missing (file name is "blob") so try fetching the extesion from
		// the original file name that was passed to the uploadToken
		if ($extension === "" || ($extension == "tmp" && $this->_uploadToken->getFileName()))
			$extension = strtolower(pathinfo($this->_uploadToken->getFileName(), PATHINFO_EXTENSION));

		$uploadFilePath = $this->getUploadPath($this->_uploadToken->getId(), $extension);
		$this->_uploadToken->setUploadTempPath($uploadFilePath);
		kFile::fullMkdir($uploadFilePath, 0700);
		
		$moveFileSuccess = kFile::moveFile($fileData['tmp_name'], $uploadFilePath);
		if (!$moveFileSuccess)
		{
			$msg = "Failed to move uploaded file for token id [{$this->_uploadToken->getId()}]";
			KalturaLog::log($msg . ' ' . print_r($fileData, true));
			throw new kUploadTokenException($msg, kUploadTokenException::UPLOAD_TOKEN_FAILED_TO_MOVE_UPLOADED_FILE);
		}
		
		chmod($uploadFilePath, 0600);
		
		//If uplaodToken is set to AutoFinalize set file size into memcache
		if($this->_autoFinalize)
		{
			$fileSize = filesize($uploadFilePath);
			$this->_autoFinalizeCache->set($this->_uploadToken->getId(), $fileSize, self::AUTO_FINALIZE_CACHE_TTL);
			if($this->_uploadToken->getFileSize() == $fileSize)
				$this->_finalChunk = true;
		}
	}

	static protected function appendChunk($sourceFilePath, $targetFileResource)
	{
		$sourceFileResource = fopen($sourceFilePath, 'rb');
		
		while (! feof($sourceFileResource)) {
			$data = fread($sourceFileResource, 1024 * 100);
			fwrite($targetFileResource, $data);
		}
		
		fclose($sourceFileResource);
		unlink($sourceFilePath);
	}

	static protected function appendAvailableChunks($targetFilePath)
	{
		$targetFileResource = fopen($targetFilePath, 'r+b');
		
		fseek($targetFileResource, 0, SEEK_END);
		
		// use glob to find existing chunks and append ones which start within or at the end of the file and will increase its size
		// in order to prevent race conditions, rename the chunk to "{chunkname}.{random}.locked" before appending it   
		// the code should handle the following rare scenarios:
		// 1. parallel procesess trying to add the same chunk
		// 2. append failing half way and recovered by the client resneding the same chunk. The random part in the locked file name
		// will prevent the re-uploaded chunk from coliding with the failed one
		while (1) {
			$currentFileSize = ftell($targetFileResource);
			
			$validChunk = false;
			
			$globStart = microtime(true);
			$chunks = glob("$targetFilePath.chunk.*", GLOB_NOSORT);
			$globTook = (microtime(true) - $globStart);
			KalturaLog::debug("glob took - " . $globTook . " seconds");
						
			foreach($chunks as $nextChunk)
			{
				$parts = explode(".", $nextChunk);
				if (count($parts))
				{
					$chunkOffset = $parts[count($parts) - 1];
					if ($chunkOffset == "locked") // don't touch chunks that were locked and may have failed appending half way
						continue;
					
					// dismiss chunks which won't enlarge the file or which are starting after the end of the file
					// support backwards compatibility of overriding a final chunk at the offset zero
					if ($chunkOffset == 0 || ($chunkOffset <= $currentFileSize && $chunkOffset + filesize($nextChunk) > $currentFileSize))
					{
						fseek($targetFileResource, $chunkOffset, SEEK_SET);
						$validChunk = true;
						break; 
					}
					else
					{
						KalturaLog::log("ignoring chunk: $nextChunk offset: $chunkOffset fileSize: $currentFileSize");
					}
				}
			}
			
			if (!$validChunk)
				break;
			
			$lockedFile = "$nextChunk.".microtime(true).".locked";
			if (! rename($nextChunk, $lockedFile)) // another process is already appending this file
			{
				KalturaLog::log("rename($nextChunk, $lockedFile) failed");
				break;
			}
			
			KalturaLog::debug("Testing:: lockedFile filesize = [" . filesize($lockedFile) . "]");
			KalturaLog::debug("Testing:: Eran filesize = [" . max(0,$chunkOffset+filesize($lockedFile)-$currentFileSize) . "] chunkOffset [$chunkOffset] filesize of nextChunk [" . filesize($lockedFile) . " currentFileSize [$currentFileSize]");
			self::appendChunk($lockedFile, $targetFileResource);
		}
		
		fclose($targetFileResource);
		
		return $currentFileSize;
	}
	
	/**
	 * Return the full path of the upload token, if the token is not part of the new machanism, it will fallback to the old one (myUploadUtils)
	 * @param string $uploadTokenId
	 */
	public static function getFullPathByUploadTokenId($uploadTokenId)
	{
		// first check if the upload token exists in db
		$uploadToken = UploadTokenPeer::retrieveByPK($uploadTokenId);
		if ($uploadToken)
		{
			if ($uploadToken->getStatus() !== UploadToken::UPLOAD_TOKEN_FULL_UPLOAD)
				throw new kUploadTokenException("Invalid upload token status", kUploadTokenException::UPLOAD_TOKEN_INVALID_STATUS);
				
			return $uploadToken->getUploadTempPath();
		}
		else
		{
			$fileExtension = strtolower(pathinfo($uploadTokenId, PATHINFO_EXTENSION));
			return myUploadUtils::getUploadPath($uploadTokenId, "", null , $fileExtension);
		}
	}
	
	/**
	 * get DC host for remote upload token
	 *
	 * @param $uploadTokenId
	 * @param $localDcId
	 * @return string
	 */
	public static function getRemoteHostForUploadToken($uploadTokenId, $localDcId = null)
	{
		$uploadToken = UploadTokenPeer::retrieveByPK($uploadTokenId);
		if(!$uploadToken)
		{
			return FALSE;
		}
		if ($localDcId !== null && $localDcId == $uploadToken->getDc())
		{
			// return FALSE if token's DC is not remote, but the same as $localDcId
			return FALSE;
		}
		return kDataCenterMgr::getRemoteDcExternalUrlByDcId($uploadToken->getDc());
	}
	
	
	/**
	 * Marks the token as used (Status is changed to CLOSED)
	 * @param string $uploadTokenId
	 */
	public static function closeUploadTokenById($uploadTokenId)
	{
		$uploadToken = UploadTokenPeer::retrieveByPK($uploadTokenId);
		if (!is_null($uploadToken)) // because we might get the old upload token which doesn't exists in db
		{
			$uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_CLOSED);
			$uploadToken->save();
		}
	}
}
