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
	private $autoFinalize = null;
	
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

	public function save(PropelPDO $con = null, $skipReload = false)
	{
		if ($this->isNew())
		{
			$this->setId($this->calculateId());
			$this->setDc(kDataCenterMgr::getCurrentDcId());
			if($this->autoFinalize)
				$this->addAutoFinalizeToCache();
		}
		parent::save($con, $skipReload);
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
	
	public function setAutoFinalize($v)
	{
		$this->autoFinalize = $v;
	}
	
	public function getAutoFinalize()
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_UPLOAD_TOKEN);
		if (!$cache)
			throw new kUploadTokenException("Cache instance required for AutoFinalize functionality Could not initiated", kUploadTokenException::UPLOAD_TOKEN_AUTO_FINALIZE_CACHE_NOT_INITIALIZED);
		
		return $cache->get($this->getId().".autoFinalize");
	}
	
	private function addAutoFinalizeToCache()
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_UPLOAD_TOKEN);
		if (!$cache)
			throw new kUploadTokenException("Cache instance required for AutoFinalize functionality Could not initiated", kUploadTokenException::UPLOAD_TOKEN_AUTO_FINALIZE_CACHE_NOT_INITIALIZED);
		
		$cache->add($this->getId().".autoFinalize", true, kUploadTokenMgr::AUTO_FINALIZE_CACHE_TTL);
	}

	public function getMinimumChunkSize()
	{
		return $this->getFromCustomData('minimumChunkSize', null , 0);
	}

	public function setMinimumChunkSize($v)
	{
		$this->putInCustomData('minimumChunkSize', $v);
	}
}
