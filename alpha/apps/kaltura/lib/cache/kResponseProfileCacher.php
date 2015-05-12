<?php
class kResponseProfileCacher implements kObjectChangedEventConsumer, kObjectDeletedEventConsumer, kObjectAddedEventConsumer
{
	/**
	 * @var array
	 */
	private static $cacheStores = null;
	
	/**
	 * @return array<kBaseCacheWrapper>
	 */
	protected static function getStores()
	{
		if(is_array(self::$cacheStores))
			return self::$cacheStores;
			
		self::$cacheStores = array();
		$cacheSections = kCacheManager::getCacheSectionNames(kCacheManager::CACHE_TYPE_RESPONSE_PROFILE);
		foreach ($cacheSections as $cacheSection)
		{
			$cacheStore = kCacheManager::getCache($cacheSection);
			if ($cacheStore)
				self::$cacheStores[] = $cacheStore;
		}
		
		return self::$cacheStores;
	}
	
	protected static function set($key, $value)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			KalturaLog::debug("Save store [" . get_class($cacheStore) . "] key [$key] [" . json_encode($value) . "]");
			$cacheStore->set($key, $value);
		}
	}
	
	protected static function delete($key)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			KalturaLog::debug("Delete store [" . get_class($cacheStore) . "] key [$key]");
			$cacheStore->delete($key);
		}
	}
	
	protected static function get($key)
	{
		$cacheStores = self::getStores();
		$value = null;
		foreach ($cacheStores as $cacheStore)
		{
			KalturaLog::debug("Get store [" . get_class($cacheStore) . "] key [$key]");
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$value = $cacheStore->getAndTouch($key);
			}
			else
			{
				$value = $cacheStore->get($key);
			}
			
			if($value)
			{
				KalturaLog::debug("Get value [" . json_encode($value) . "]");
				return $value;
			}
		}
		
		return null;
	}
	
	protected static function query(kCouchbaseCacheQuery $query)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				return $cacheStore->query($query);
			}
		}
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof ResponseProfile)
		{
			$this->deleteResponseProfileCache($object);
			return true;
		}
			
		if($this->isCachedObject($object))
			$this->deleteCachedObjects($object);
			
		if($this->hasCachedRelatedObjects($object))
			$this->addRecalculateCacheJob($object);
			
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof ResponseProfile)
			return true;
			
		if($this->hasCachedRelatedObjects($object))
			return true;
			
		if($this->isCachedObject($object))
			return true;
			
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof ResponseProfile)
		{
			$this->deleteResponseProfileCache($object);
			return true;
		}
			
		if($this->isCachedObject($object))
			$this->deleteCachedObjects($object);
			
		if($this->hasCachedRelatedObjects($object))
			$this->addRecalculateCacheJob($object);
			
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof ResponseProfile)
			return true;
			
		if($this->hasCachedRelatedObjects($object))
			return true;
			
		if($this->isCachedObject($object))
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($this->hasCachedRelatedObjects($object))
			return $this->addRecalculateCacheJob($object);
			
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($this->hasCachedRelatedObjects($object))
			return true;
			
		return false;
	}
	
	protected function hasCachedRelatedObjects(BaseObject $object)
	{
		/* @var $object IBaseObject */
		$peer = $object->getPeer();
		if(!($peer instanceof IRelatedObjectPeer))
		{
			return false;
		}
		
		if(!$peer->isReferenced($object))
		{
			$roots = $peer->getRootObjects($object);
			if(count($roots))
				return true;
				
			return false;
		}
		
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RELATED_OBJECT);
				$query->addKey('partnerId', $object->getPartnerId());
				$query->addKey('objectType', get_class($object));
				$query->setLimit(1);
				
				$list = $cacheStore->query($query);
				if($list->getCount())
					return true;
			}
		}
		
		return false;
	}
	
	protected function isCachedObject(BaseObject $object)
	{
		/* @var $object IBaseObject */
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_OBJECT_SPECIFIC);
				$query->addKey('partnerId', $object->getPartnerId());
				$query->addKey('objectType', get_class($object));
				$query->addKey('objectId', $object->getId());
				$query->setLimit(1);
				
				$list = $cacheStore->query($query);
				if($list->getCount())
					return true;
			}
		}
		
		return false;
	}
	
	protected function addRecalculateCacheJob(BaseObject $object)
	{
		// TODO
	}
	
	protected function deleteCachedObjects(BaseObject $object)
	{
		/* @var $object IBaseObject */
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_OBJECT_SPECIFIC);
				$query->addKey('partnerId', $object->getPartnerId());
				$query->addKey('objectType', get_class($object));
				$query->addKey('objectId', $object->getId());
				$query->setLimit(100);
				
				$list = $cacheStore->query($query);
				while($list->getCount())
				{
					$keys = array();
					foreach($list->getObjects() as $cache)
					{
						/* @var $cache kCouchbaseCacheListItem */
						$keys[] = $cache->getId();
					}
					$cacheStore->multiDelete($keys);
				}
			}
		}
	}
}