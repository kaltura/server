<?php

/**
 * Subclass for representing a row from the 'upload_token' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class UploadToken extends BaseUploadToken implements IBaseObject
{
	/**
	 * Token created but no upload has been started yet
	 */
	const UPLOAD_TOKEN_PENDING = 0;
	
	/**
	 * Upload didn't include the whole file 
	 */
	const UPLOAD_TOKEN_PARTIAL_UPLOAD = 1;
	
	/**
	 * Uploaded full file
	 */
	const UPLOAD_TOKEN_FULL_UPLOAD = 2;
	
	/**
	 * The entry was added
	 * @var int
	 */
	const UPLOAD_TOKEN_CLOSED = 3;
	
	/**
	 * The token timed out after a certain period of time
	 */
	const UPLOAD_TOKEN_TIMED_OUT = 4;
	
	/**
	 * Deleted via api
	 */
	const UPLOAD_TOKEN_DELETED = 5;

	/**
	* The value for url.
	* @var string
	*/
	protected $url = null;

	/**
	 * Get the [url] value.
	 *
	 * @return     string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Set the [url] value.
	 * @param string
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function save(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$this->setId($this->calculateId());
			$this->setDc(kDataCenterMgr::getCurrentDcId());
		}
		parent::save($con);
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseUploadToken#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectDeleted = false;
		if($this->isColumnModified(UploadTokenPeer::STATUS) && $this->getStatus() == self::UPLOAD_TOKEN_DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
	public function calculateId()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		for ($i = 0; $i < 10; $i++)
		{
			$id = $dc["id"].'_'.md5(microtime(true) . getmypid() . uniqid(rand(),true));
			$existingObject = UploadTokenPeer::retrieveByPKNoFilter($id);
			
			if (!$existingObject)
				return $id;
		}
		
		throw new Exception("Could not calculate unique id for upload token");
	}
	
	public function getPuserId()
	{
		$kuser = $this->getkuser();
		return $kuser ? $kuser->getPuserId() : null;
	}

	public function getCacheInvalidationKeys()
	{
		return array("uploadToken:id=".strtolower($this->getId()));
	}
}
