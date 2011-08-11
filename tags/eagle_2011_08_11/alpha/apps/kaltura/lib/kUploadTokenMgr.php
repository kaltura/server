<?php
class kUploadTokenMgr
{
	/**
	 * @var UploadToken
	 */
	protected $_uploadToken;
	
	/**
	 * Construct new upload token manager for the upload token object
	 * @param UploadToken $uploadToken
	 */
	public function __construct(UploadToken $uploadToken)
	{
		KalturaLog::info("Init for upload token id [{$uploadToken->getId()}]");
		$this->_uploadToken = $uploadToken;
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
	 */
	public function uploadFileToToken($fileData, $resume = false, $finalChunk = true, $resumeAt = -1)
	{
		KalturaLog::debug(__METHOD__ . print_r(func_get_args(), true));
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
			$this->tryMoveToErrors($fileData);
			throw $ex;
		}
		
		if ($resume)
			$this->handleResume($fileData, $resumeAt);
		else
			$this->handleMoveFile($fileData);
		
		$fileSize = filesize($this->_uploadToken->getUploadTempPath());
		
		if ($finalChunk)
		{
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
		$te->setDescription(__METHOD__.":".__LINE__);
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
			$extension = 'noext';
			
		return myContentStorage::getFSUploadsPath().$uploadTokenId.'.'.$extension;
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
	 * @param file $fileData
	 * @param int $resumeAt
	 */
	protected function handleResume($fileData, $resumeAt = -1)
	{
		KalturaLog::info("Trying to resume the uploaded file");
		$uploadFilePath = $this->_uploadToken->getUploadTempPath();
		if (!file_exists($uploadFilePath))
			throw new kUploadTokenException("Temp file [$uploadFilePath] was not found when trying to resume", kUploadTokenException::UPLOAD_TOKEN_FILE_NOT_FOUND_FOR_RESUME);
			
		if ($resumeAt > filesize($uploadFilePath))
			throw new kUploadTokenException("Temp file [$uploadFilePath] attempted to resume at invalid position $resumeAt", kUploadTokenException::UPLOAD_TOKEN_RESUMING_INVALID_POSITION);
			
		$this->resumeFile($fileData['tmp_name'], $uploadFilePath, $resumeAt);
		KalturaLog::info("The file resumed successfully");
		
		$fileWasDeleted = unlink($fileData['tmp_name']);
		if ($fileWasDeleted)
			KalturaLog::info("Temp file was deleted successfully");
		else
			KalturaLog::err("Failed to delete temp file [{$fileData['tmp_name']}");
	}
	
	/**
	 * Move the uploaded file
	 * @param unknown_type $fileData
	 */
	protected function handleMoveFile($fileData)
	{
		KalturaLog::info("Moving the uploaded file");
		
		// get the upload path
		$extension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
		$uploadFilePath = $this->getUploadPath($this->_uploadToken->getId(), $extension);
		$this->_uploadToken->setUploadTempPath($uploadFilePath);
		myContentStorage::fullMkdir($uploadFilePath);
		
		$moveFileSuccess = move_uploaded_file($fileData['tmp_name'], $uploadFilePath);
		if (!$moveFileSuccess)
		{
			$msg = "Failed to move uploaded file for token id [{$this->_uploadToken->getId()}]";
			KalturaLog::log($msg . ' ' . print_r($fileData, true));
			throw new kUploadTokenException($msg, kUploadTokenException::UPLOAD_TOKEN_FAILED_TO_MOVE_UPLOADED_FILE);
		}
		else 
		{
			KalturaLog::info("The file was moved successfully");
		}
		
		chmod($uploadFilePath, 0777);
	}
	
	/**
	 * Resumes the target file using the data from source file
	 * @param resource $sourceFilePath
	 * @param resource $targetFilePath
	 */
	protected function resumeFile($sourceFilePath, $targetFilePath, $resumeAt = -1)
	{
		$sourceFileResource = fopen($sourceFilePath, 'rb');
		$targetFileResource = fopen($targetFilePath, 'r+b');

        if ($resumeAt != -1)
			fseek($targetFileResource, $resumeAt, SEEK_SET);
        else
			fseek($targetFileResource, 0, SEEK_END);
						
		while(!feof($sourceFileResource))
		{
			$data = fread($sourceFileResource, 1024*100);
			fwrite($targetFileResource, $data);
		}
		
		fclose($sourceFileResource);
		fclose($targetFileResource);
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
		$uploadToken = uploadTokenPeer::retrieveByPK($uploadTokenId);
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