<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kUploadedFileTokenResource extends kLocalFileResource
{
	/**
	 * Token that returned from upload.upload action or uploadToken.add action. 
	 * @var string
	 */
	private $token;
	
	public function getType()
	{
		return 'kLocalFileResource';
	}
	
	/* (non-PHPdoc)
	 * @see kLocalFileResource::attachCreatedObject()
	 */
	public function attachCreatedObject(BaseObject $object)
	{
		$dbUploadToken = UploadTokenPeer::retrieveByPK($this->token);
		if(is_null($dbUploadToken))
			return;
		
		$dbUploadToken->setObjectType(get_class($object));
		$dbUploadToken->setObjectId($object->getId());
		$dbUploadToken->save();
	}

	/**
	 * @param string $token
	 */
	public function setToken($token)
	{
		$this->token = $token;
	}

	public function getMediaType()
	{
		$dbUploadToken = UploadTokenPeer::retrieveByPK($this->token);
		if(is_null($dbUploadToken))
			return null;
		
		$fileName = $dbUploadToken->getFileName();
		if(!$fileName)
			return null;
		
		return myFileUploadService::getMediaTypeFromFileExt(pathinfo($fileName, PATHINFO_EXTENSION));
	}
}