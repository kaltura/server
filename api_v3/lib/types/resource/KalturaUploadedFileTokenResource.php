<?php
/**
 * Used to ingest media that uploaded to the system and represented by token that returned from upload.upload action or uploadToken.add action.
 * 
 * @package api
 * @subpackage objects
 * @see api/services/UploadService#uploadAction()
 * @see api/services/UploadTokenService#addAction()
 */
class KalturaUploadedFileTokenResource extends KalturaContentResource 
{
	/**
	 * Token that returned from upload.upload action or uploadToken.add action. 
	 * @var string
	 */
	public $token;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('token');
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kLocalFileResource();
			
		try
		{
		    $entryFullPath = kUploadTokenMgr::getFullPathByUploadTokenId($this->token);
		}
		catch(kCoreException $ex)
		{
		    if ($ex->getCode() == kUploadTokenException::UPLOAD_TOKEN_INVALID_STATUS);
			    throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY);
			    
		    throw $ex;
		}
				
		if(!file_exists($entryFullPath))
		{
			$remoteDCHost = kUploadTokenMgr::getRemoteHostForUploadToken($this->token, kDataCenterMgr::getCurrentDcId());
			if($remoteDCHost)
			{
				kFile::dumpApiRequest($remoteDCHost);
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