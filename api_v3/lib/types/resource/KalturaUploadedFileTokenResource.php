<?php
/**
 * Used to ingest media that uploaded to the system and represented by token that returned from upload.upload action or uploadToken.add action.
 * 
 * @package api
 * @subpackage objects
 * @see api/services/UploadService#uploadAction()
 * @see api/services/UploadTokenService#addAction()
 */
class KalturaUploadedFileTokenResource extends KalturaGenericDataCenterContentResource
{
	/**
	 * Token that returned from upload.upload action or uploadToken.add action. 
	 * @var string
	 */
	public $token;
	
	/* (non-PHPdoc)
	 * @see KalturaDataCenterContentResource::getDc()
	 */
	public function getDc()
	{
		$dbUploadToken = UploadTokenPeer::retrieveByPK($this->token);
		if(is_null($dbUploadToken))
			throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_NOT_FOUND);
		
		return $dbUploadToken->getDc();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaDataCenterContentResource::validateForUsage()
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		
		$this->validatePropertyNotNull('token');
	}
	
	/* (non-PHPdoc)
	 * @see KalturaResource::entryHandled()
	 */
	public function entryHandled(entry $dbEntry)
	{
		parent::entryHandled($dbEntry);
		
		$dbUploadToken = UploadTokenPeer::retrieveByPK($this->token);
		if(is_null($dbUploadToken))
			throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_NOT_FOUND);
		
		if($dbUploadToken->getStatus() == UploadToken::UPLOAD_TOKEN_FULL_UPLOAD)
			kUploadTokenMgr::closeUploadTokenById($this->token);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->validateForUsage($object_to_fill, $props_to_skip);
		
		$dbUploadToken = UploadTokenPeer::retrieveByPK($this->token);
		if(is_null($dbUploadToken))
			throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_NOT_FOUND);
		
		if(!$object_to_fill)
			$object_to_fill = new kUploadedFileTokenResource();
		
		$object_to_fill->setToken($this->token);
		
		if($dbUploadToken->getStatus() != UploadToken::UPLOAD_TOKEN_FULL_UPLOAD)
		{
			$object_to_fill->setIsReady(false);
			return $object_to_fill;
		}
		
		try
		{
			$entryFullPath = kUploadTokenMgr::getFullPathByUploadTokenId($this->token);
		}
		catch(kCoreException $ex)
		{
			if($ex->getCode() == kUploadTokenException::UPLOAD_TOKEN_INVALID_STATUS)
				;
			throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY);
			
			throw $ex;
		}
		
		if(!file_exists($entryFullPath))
		{
			$remoteDCHost = kUploadTokenMgr::getRemoteHostForUploadToken($this->token, kDataCenterMgr::getCurrentDcId());
			if($remoteDCHost)
			{
				kFileUtils::dumpApiRequest($remoteDCHost);
			}
			else
			{
				throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN);
			}
		}
		
		$object_to_fill->setLocalFilePath($entryFullPath);
		return $object_to_fill;
	}
}