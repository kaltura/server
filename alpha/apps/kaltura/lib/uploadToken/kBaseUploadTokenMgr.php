<?php
/**
 * Created by IntelliJ IDEA.
 * User: inbal.bendavid
 * Date: 6/20/2019
 * Time: 4:46 PM
 */

abstract class kBaseUploadTokenMgr
{
	const NO_EXTENSION_IDENTIFIER = 'noex';
	const AUTO_FINALIZE_CACHE_TTL = 2592000; //Thirty days in seconds
	const MAX_AUTO_FINALIZE_RETIRES = 5;
	const EICAR_MD5 = '44d88612fea8a8f36de82e1278abb02f';
	const EICAR_MIN_FILE_SIZE = 68;
	const EICAR_MAX_FILE_SIZE = 128;
	const BAT_FILE_EXTENSION = 'bat';

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
	 * kBaseUploadTokenMgr constructor.
	 * @param UploadToken $uploadToken
	 * @param bool $finalChunk
	 */
	public function __construct(UploadToken $uploadToken, $finalChunk = true)
	{
		$this->_uploadToken = $uploadToken;
		$this->_finalChunk = $finalChunk;
	}

	/**
	 * Resume the upload token with the uploaded file optionally at a given offset
	 *
	 * @param file $fileData
	 * @param float $resumeAt
	 */
	abstract protected function handleResume($fileData, $resumeAt);

	/**
	 * Returns the target upload path for upload token id and extension
	 *
	 * @param $uploadTokenId
	 * @param string $extension
	 * @return string
	 */
	abstract protected function getUploadPath($uploadTokenId, $extension = '');


	abstract protected function getFinalFilePath();

	/**
	 * get upload token manager by storage type
	 *
	 * @param $uploadToken
	 * @param bool $finalChunk
	 * @param null $type
	 * @return kS3UploadTokenMgr|kNfsUploadTokenMgr
	 */
	public static function getInstance($uploadToken, $finalChunk = true, $type = null)
	{
		$dc_config = kConf::getMap("dc_config");
		if(!$type)
		{
			$type = isset($dc_config['fileSystemType']) ? $dc_config['fileSystemType'] : kSharedFileSystemMgrType::NFS;
		}

		switch($type)
		{
			case kSharedFileSystemMgrType::S3:
				return new kS3UploadTokenMgr($uploadToken, $finalChunk);
			default:
				return new kNfsUploadTokenMgr($uploadToken, $finalChunk);
		}
	}

	/**
	 * @throws kUploadTokenException
	 */
	protected function initUploadTokenMemcache()
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
		$this->_uploadToken->setFinalFilePath($this->getFinalFilePath());
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
	 *
	 * @param $fileData
	 * @param bool $resume
	 * @param int $resumeAt
	 * @throws PropelException
	 * @throws kUploadTokenException
	 */
	public function uploadFileToToken($fileData, $resume = false, $resumeAt = -1)
	{
		KalturaLog::debug("TTT: Inside uploadFileToToken");
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
		
		try
		{
			if ($resume)
			{
				$fileSize = $this->handleResume($fileData, $resumeAt);
			}
			else
			{
				$fileSize = $this->handleMoveFile($fileData, $resumeAt);
			}
		}
		catch(Exception $ex)
		{
			throw new kUploadTokenException("Failed to save upload token file", kUploadTokenException::UPLOAD_TOKEN_PROCESSING_FAILURE);
		}


		if ($this->_finalChunk)
		{
			if (PermissionPeer::isValidForPartner(PermissionName::FEATURE_FILE_TYPE_RESTRICTION_PERMISSION, kCurrentContext::getCurrentPartnerId())
				&& !$this->checkIfFileIsAllowed())
			{
				kFlowHelper::handleUploadFailed($this->_uploadToken);
				throw new kUploadTokenException("Restricted upload token file type", kUploadTokenException::UPLOAD_TOKEN_FILE_TYPE_RESTRICTED);
			}
			$this->closeFullFileUpload();
			$this->_uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_FULL_UPLOAD);
		}
		else
		{
			$this->_uploadToken->setStatus(UploadToken::UPLOAD_TOKEN_PARTIAL_UPLOAD);
		}

		$this->_uploadToken->setUploadedFileSize($fileSize);
		$this->_uploadToken->setDc(kDataCenterMgr::getCurrentDcId());

		$this->_uploadToken->save();
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
	 * Validate the file data
	 *
	 * @param $fileData
	 * @throws kUploadTokenException
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
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function checkIfFileIsAllowed()
	{
		$uploadFilePath = $this->_uploadToken->getUploadTempPath();
		$fileType = kFileUtils::getMimeType($uploadFilePath);

		if ($fileType == 'text/plain')
		{
			if ( strtolower(pathinfo($uploadFilePath, PATHINFO_EXTENSION)) == self::BAT_FILE_EXTENSION)
			{
				return false;
			}
			else
			{
				if (file_exists($uploadFilePath)
					&& filesize($uploadFilePath) >= self::EICAR_MIN_FILE_SIZE
					&& filesize($uploadFilePath) <= self::EICAR_MAX_FILE_SIZE)
				{
					$content = file_get_contents($uploadFilePath);
					if (md5(file_get_contents($content)) == self::EICAR_MD5)
					{
						return false;
					}
				}
			}
		}
		
		$fileTypes = kConf::get('file_type');
		return in_array($fileType, $fileTypes['allowed']);
	}

	/**
	 * Updates the file name of the token (if empty) using the file name from the file data
	 *
	 * @param $fileData
	 * @throws PropelException
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
	 * @param $fileData
	 */
	protected function tryMoveToErrors($fileData)
	{
		if (kFile::checkFileExists($fileData['tmp_name']))
		{
			$errorFilePath = $this->getUploadPath('error-'.$this->_uploadToken->getId(), microtime(true), false);
			kFile::rename($fileData['tmp_name'], $errorFilePath);
		}
	}

	/**
	 * @param $currentFileSize
	 * @return bool
	 * @throws kUploadTokenException
	 */
	protected function checkIsFinalChunk($currentFileSize)
	{
		$cacheFileSizeValue = $this->_autoFinalizeCache->increment($this->_uploadToken->getId().".size", $currentFileSize);
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
	 *
	 * @param $fileData
	 * @throws kUploadTokenException
	 */
	protected function handleMoveFile($fileData, $resumeAt)
	{
		// get the upload path
		$extension = $this->getFileExtension($fileData['name']);
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

		kFile::chmod($uploadFilePath, 0600);
		$fileSize = kFile::fileSize($uploadFilePath);

		$updatedFileSize = $this->startFullFileUpload($uploadFilePath, $fileSize, $resumeAt);

		//If uplaodToken is set to AutoFinalize set file size into memcache
		if($this->_autoFinalize)
		{
			$this->_autoFinalizeCache->set($this->_uploadToken->getId().".size", $fileSize, self::AUTO_FINALIZE_CACHE_TTL);
			if($this->_uploadToken->getFileSize() == $fileSize)
				$this->_finalChunk = true;
		}
		return $updatedFileSize;
	}
	
	/**
	 * Return the full path of the upload token, if the token is not part of the new machanism, it will fallback to the old one (myUploadUtils)
	 *
	 * @param $uploadTokenId
	 * @return string
	 * @throws kUploadTokenException
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
	 *
	 * @param $uploadTokenId
	 * @throws PropelException
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

	/**
	 * get file extension for a given file
	 *
	 * @param $fileName
	 * @return string
	 */
	protected function getFileExtension($fileName)
	{
		// get the upload path
		$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

		// in firefox html5 upload the extension is missing (file name is "blob") so try fetching the extesion from
		// the original file name that was passed to the uploadToken
		if ($extension === "" || ($extension == "tmp" && $this->_uploadToken->getFileName()))
			$extension = strtolower(pathinfo($this->_uploadToken->getFileName(), PATHINFO_EXTENSION));

		return $extension;
	}

	protected function startFullFileUpload($uploadFilePath, $fileSize, $resumeAt)
	{

	}

	protected function closeFullFileUpload()
	{

	}
}