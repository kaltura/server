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
				KalturaLog::debug("Get value [" . print_r($value, true) . "]");
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

	protected static function getSessionKey($protocol = null, $ksType = null, array $userRoles = null)
	{
		if(!$protocol)
			$protocol = infraRequestUtils::getProtocol();
		if(!$ksType)
			$ksType = kCurrentContext::getCurrentSessionType();
		if(!$userRoles)
			$userRoles = kPermissionManager::getCurrentRoleIds();
			
		sort($userRoles);
		$userRole = implode('_', $userRoles);
		return "{$protocol}_{$ksType}_{$userRole}";
	}
	
	protected static function getObjectKey(IBaseObject $object)
	{
		$partnerId = $object->getPartnerId();
		$objectType = get_class($object);
		$objectId = $object->getPrimaryKey();
		return "{$partnerId}_{$objectType}_{$objectId}";
	}
	
	protected static function getTriggerKey(IBaseObject $object)
	{
		$partnerId = $object->getPartnerId();
		$objectType = get_class($object);
		return "{$partnerId}_{$objectType}";
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof ResponseProfile)
			return $this->deleteResponseProfileCache($object);
			
		if($this->isCachedObject($object))
			$this->invalidateCachedObject($object);
			
		if($this->hasCachedRootObjects($object))
			$this->invalidateCachedRootObjects($object);
			
		if($this->hasCachedRelatedObjects($object))
			$this->addRecalculateRelatedObjectsCacheJob($object);
			
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
			
		if($this->hasCachedRootObjects($object))
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
			return $this->deleteResponseProfileCache($object);
			
		if($this->isCachedObject($object))
			$this->invalidateCachedObject($object);
			
		if($this->hasCachedRelatedObjects($object))
			$this->addRecalculateRelatedObjectsCacheJob($object);
			
		if($this->hasCachedRootObjects($object))
			$this->invalidateCachedRootObjects($object);
			
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
			
		if($this->hasCachedRootObjects($object))
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
			return $this->addRecalculateRelatedObjectsCacheJob($object);
			
		if($this->hasCachedRootObjects($object))
			return $this->invalidateCachedRootObjects($object);
			
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($this->hasCachedRelatedObjects($object))
			return true;
			
		if($this->hasCachedRootObjects($object))
			return true;
			
		return false;
	}
	
	protected function hasCachedRelatedObjects(BaseObject $object)
	{
		if($object instanceof IBaseObject)
		{
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
					$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_RELATED_OBJECT);
					$query->addKey('triggerKey', self::getTriggerKey($object));
					$query->setLimit(1);
					
					$list = $cacheStore->query($query);
					if($list->getCount())
						return true;
				}
			}
		}
		
		return false;
	}
	
	protected function hasCachedRootObjects(BaseObject $object)
	{
		if($object instanceof IBaseObject)
		{
			$peer = $object->getPeer();
			if(!($peer instanceof IRelatedObjectPeer))
			{
				return false;
			}
			
			if($peer->isReferenced($object))
			{
				return false;
			}
			
			$roots = $peer->getRootObjects($object);
			if(count($roots))
				return true;
		}
				
		return false;
	}
	
	protected function isCachedObject(BaseObject $object)
	{
		if($object instanceof IBaseObject)
		{
			$cacheStores = self::getStores();
			foreach ($cacheStores as $cacheStore)
			{
				if($cacheStore instanceof kCouchbaseCacheWrapper)
				{
					$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_OBJECT_SPECIFIC);
					$query->addKey('objectKey', self::getObjectKey($object));
					$query->setLimit(1);
					
					$list = $cacheStore->query($query);
					if($list->getCount())
						return true;
				}
			}
		}
		
		return false;
	}
	
	protected function deleteResponseProfileCache(ResponseProfile $responseProfile)
	{
		$key = self::getResponseProfileCacheKey($responseProfile->getKey());
		self::delete($key);
		
		return true;
	}
	
	protected function addRecalculateRelatedObjectsCacheJob(IBaseObject $object)
	{
		$triggerType = get_class($object);
		$objectTypes = self::listObjectTypes($triggerType);
		foreach($objectTypes as $objectType)
		{
//			TODO
//			$sessionTypes = self::listObjectSessionTypes($object);
//			foreach($sessionTypes as $sessionType)
//			{
//				list($protocol, $ksType, $userRole, $count) = $sessionType;
//				if($count < self::MAX_CACHE_KEYS_PER_JOB)
//				{
//					kJobsManager::addRecalculateCacheJob($partnerId, $protocol, $ksType, $userRoles, $objectType);
//				}
//				else
//				{
//					kJobsManager::addRecalculateCacheJob($partnerId, $protocol, $ksType, $userRoles, $objectType);
//				}
//			}
		}
		// TODO list all ksType, roldIds, protocol that related to this object and create few jobs for each
		return true;
	}
	
	protected function addRecalculateObjectCacheJob(IBaseObject $object)
	{
		$objectType = get_class($object);
		$objectKey = self::getObjectKey($object);
		
//		TODO
//		$sessionTypes = self::listObjectSessionTypes($object);
//		foreach($sessionTypes as $sessionType)
//		{
//			list($protocol, $ksType, $userRoles, $count) = $sessionType;
//			if($count < self::MAX_CACHE_KEYS_PER_JOB)
//			{
//				$sessionKey = self::getSessionKey($protocol, $ksType, $userRoles);
//				$startEndKeys = self::listStartEndKeys($object, $sessionKey);
//				foreach($startEndKeys)
//					kJobsManager::addRecalculateCacheJob($partnerId, $protocol, $ksType, $userRoles, $objectType, $objectKey);
//			}
//			else
//			{
//				kJobsManager::addRecalculateCacheJob($partnerId, $protocol, $ksType, $userRoles, $objectType, $objectKey);
//			}
//		}
		return true;
	}
	
	protected function invalidateCachedRootObjects(IBaseObject $object)
	{
		$peer = $object->getPeer();
		if($peer instanceof IRelatedObjectPeer)
		{
			$roots = $peer->getRootObjects($object);
			if(is_array($roots))
			{
				foreach($roots as $root)
				{
					$this->invalidateCachedObject($root);
				}
			}
		}
		return true;
	}
	
	protected function invalidateCachedObject(IBaseObject $object)
	{
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_RECALCULATE_RESPONSE_PROFILE_CACHE, $object->getPartnerId()))
		{
			$this->addRecalculateObjectCacheJob($object);
		}
		else
		{
			$this->deleteCachedObjects($object);
		}
		
		return true;
	}
		
	protected function deleteCachedObjects(IBaseObject $object)
	{
		/* @var $object IBaseObject */
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_OBJECT_SPECIFIC);
				$query->addKey('objectKey', self::getObjectKey($object));
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
					$list = $cacheStore->query($query);
				}
			}
		}
	}
}