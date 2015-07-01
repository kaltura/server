<?php
class kResponseProfileCacher implements kObjectChangedEventConsumer, kObjectDeletedEventConsumer, kObjectAddedEventConsumer
{
	const MAX_CACHE_KEYS_PER_JOB = 1000;
	
	/**
	 * @var array
	 */
	private static $cacheStores = null;
	
	/**
	 * @return array<kBaseCacheWrapper>
	 */
	protected static function getStores($cacheType = kCacheManager::CACHE_TYPE_RESPONSE_PROFILE)
	{
		if(is_array(self::$cacheStores))
			return self::$cacheStores;
			
		self::$cacheStores = array();
		$cacheSections = kCacheManager::getCacheSectionNames($cacheType);
		foreach ($cacheSections as $cacheSection)
		{
			$cacheStore = kCacheManager::getCache($cacheSection);
			if ($cacheStore)
				self::$cacheStores[] = $cacheStore;
		}
		
		return self::$cacheStores;
	}
	
	/**
	 * @return array<kBaseCacheWrapper>
	 */
	protected static function getInvalidationStores()
	{
		return self::getStores(kCacheManager::CACHE_TYPE_RESPONSE_PROFILE_INVALIDATION);
	}
	
	protected static function invalidateRelated(IBaseObject $object)
	{
		KalturaLog::debug('Invalidating object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] related objects');
		
		$partnerId = $object->getPartnerId();
		$triggerKey = self::getRelatedObjectKey($object);
		$objectTypes = self::listObjectRelatedTypes($triggerKey);
		foreach($objectTypes as $objectType)
		{
			self::invalidate("{$partnerId}_{$objectType}");
		}
	}
	
	protected static function invalidate($invalidationKey)
	{
		$now = time();
		KalturaLog::debug("Invalidating key [$invalidationKey] now [$now]");
		self::set($invalidationKey, $now);
	}
	
	protected static function set($key, $value)
	{
		KalturaLog::debug("Key [$key]");
		
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			/* @var $cacheStore kBaseCacheWrapper */
			$cacheStore->set($key, $value);
		}
	}
	
	protected static function delete($key)
	{
		KalturaLog::debug("Key [$key]");
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			$cacheStore->delete($key);
		}
	}
	
	protected static function getMulti(array $keys)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				return $cacheStore->multiGetAndTouch($keys);
			}
			else
			{
				return $cacheStore->multiGet($keys);
			}
		}
		
		return false;
	}
	
	protected static function get($key, array $invalidationKeys = null, $touch = true)
	{
		KalturaLog::debug("Key [$key]");
		$cacheStores = self::getStores();
		$value = null;
		foreach ($cacheStores as $cacheStore)
		{
			if($touch && $cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$value = $cacheStore->getAndTouch($key);
			}
			else
			{
				$value = $cacheStore->get($key);
			}
			
			if($value)
			{
				break;
			}
		}
	
		if($value && $invalidationKeys)
		{
			$invalidationCacheStores = self::getInvalidationStores();
			foreach ($invalidationCacheStores as $store)
			{
				/* @var $store kBaseCacheWrapper */
			
				if($store instanceof kCouchbaseCacheWrapper)
				{
					$invalidationTimes = $cacheStore->multiGetAndTouch($invalidationTimes);
				}
				else
				{
					$invalidationTimes = $store->multiGet($invalidationTimes);
				}
				
				if($invalidationTimes)
				{
					foreach($invalidationTimes as $invalidationKey => $invalidationTime) 
					{
						if(!is_null($invalidationTime))
						{
							$invalidationTime += kConf::get('cache_invalidation_threshold', null, 0);
							KalturaLog::debug("Invalidation key [$invalidationKey] time [$invalidationTime] compare to value time [{$value->time}]");
							if(intval($invalidationTime) >= intval($value->time))
							{
								KalturaLog::debug("Invalidation time [$invalidationTime] >= value time [{$value->time}]");
								return null;
							}
						}
					}
				}	
			}
		}
			
		return $value;
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
	
	protected static function getRelatedObjectKey(IBaseObject $object)
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

		/* @var $object IBaseObject */
		if($this->isCachedObject($object))
		{
			KalturaLog::debug('Object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] is cached object');
			$this->invalidateCachedObject($object);
		}
			
		if($this->hasCachedRootObjects($object))
		{
			KalturaLog::debug('Object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] has cached root objects');
			$this->invalidateCachedRootObjects($object);
		}
			
		if($this->hasCachedRelatedObjects($object))
		{
			KalturaLog::debug('Object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] has cached related objects');
			$this->invalidateCachedRelatedObjects($object);
		}
			
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof ResponseProfile)
			return true;

		if($object instanceof IBaseObject)
		{
			if($this->hasCachedRelatedObjects($object))
				return true;
				
			if($this->hasCachedRootObjects($object))
				return true;
				
			if($this->isCachedObject($object))
				return true;
		}
			
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof ResponseProfile)
			return $this->deleteResponseProfileCache($object);

		/* @var $object IBaseObject */
		
		if($this->isCachedObject($object))
		{
			KalturaLog::debug('Object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] is cached object');
			$this->invalidateCachedObject($object);
		}
					
		if($this->hasCachedRelatedObjects($object))
		{
			KalturaLog::debug('Object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] has cached related objects');
			$this->invalidateCachedRelatedObjects($object);
		}
			
		if($this->hasCachedRootObjects($object))
		{
			KalturaLog::debug('Object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] has cached root objects');
			$this->invalidateCachedRootObjects($object);
		}
			
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof ResponseProfile)
			return true;

		if($object instanceof IBaseObject)
		{
			if($this->hasCachedRelatedObjects($object))
				return true;
				
			if($this->hasCachedRootObjects($object))
				return true;
				
			if($this->isCachedObject($object))
				return true;
		}
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		/* @var $object IBaseObject */
		if($this->hasCachedRelatedObjects($object))
		{
			KalturaLog::debug('Object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] has cached related objects');
			$this->invalidateCachedRelatedObjects($object);
		}
			
		if($this->hasCachedRootObjects($object))
		{
			KalturaLog::debug('Object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] has cached root objects');
			$this->invalidateCachedRootObjects($object);
		}
			
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof IBaseObject)
		{
			if($this->hasCachedRelatedObjects($object))
				return true;
				
			if($this->hasCachedRootObjects($object))
				return true;
		}
			
		return false;
	}
	
	protected function hasCachedRelatedObjects(IBaseObject $object)
	{
		$peer = $object->getPeer();
		if(!($peer instanceof IRelatedObjectPeer) || !$peer->isReferenced($object))
		{
			return false;
		}
		
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_RELATED_OBJECT_SESSIONS);
				if($query)
				{
					$query->addStartKey('triggerKey', self::getRelatedObjectKey($object));
					$query->addStartKey('objectType', 'A');
					$query->addStartKey('sessionKey', 'A');
					$query->setLimit(1);
					
					$list = $cacheStore->query($query);
					if($list->getCount())
						return true;
				}
			}
		}
		
		return false;
	}
	
	protected function hasCachedRootObjects(IBaseObject $object)
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
				
		return false;
	}
	
	protected function isCachedObject(IBaseObject $object)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_OBJECT_SPECIFIC);
				if($query)
				{
					$query->setKey(self::getObjectKey($object));
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
	
	protected function listObjectKeys($objectType, $sessionKey)
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$objectKey = "{$partnerId}_{$objectType}_";
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				// TODO optimize using elastic search query
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_SESSION_TYPE);
				if(!$query)
					continue;
					
				$query->addStartKey('sessionKey', $sessionKey);
				$query->addStartKey('objectKey', $objectKey);
				$query->setLimit(1);

				$list = $cacheStore->query($query);
				$query->setLimit(2);
				$offset = -1;
				$array = array();
				$startKey = null;
				while(count($list->getObjects()))
				{
					$objects = $list->getObjects();
					if(count($objects) == 1)
					{
						$startCacheObject = reset($objects);
						/* @var $startCacheObject kCouchbaseCacheListItem */
						list($cachedSessionKey, $cachedObjectKey) = $startCacheObject->getKey();
						$startKey = $cachedObjectKey;
					}
					else
					{
						list($endCacheObject, $startCacheObject) = $objects;
						/* @var $endCacheObject kCouchbaseCacheListItem */
						/* @var $startCacheObject kCouchbaseCacheListItem */
						list($cachedSessionKey, $cachedEndObjectKey) = $endCacheObject->getKey();
						list($cachedSessionKey, $cachedStartObjectKey) = $startCacheObject->getKey();
						$array[] = array($startKey, $cachedEndObjectKey);
						$startKey = $cachedStartObjectKey;
					}
					$offset += self::MAX_CACHE_KEYS_PER_JOB;
					$query->setOffset($offset);
					$list = $cacheStore->query($query);
				}
				return $array;
			}
		}
	
		return array();
	}
	
	protected static function listObjectRelatedSessions($triggerKey)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				// TODO optimize using elastic search query
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_RELATED_OBJECT_SESSIONS);
				if($query)
				{
					$query->addStartKey('triggerKey', $triggerKey);
					$query->addStartKey('objectType', 'A');
					$query->addStartKey('sessionKey', 'A');
					$query->addEndKey('triggerKey', $triggerKey);
					$query->addEndKey('objectType', 'z');
					$query->addEndKey('sessionKey', 'z');
					$query->setLimit(self::MAX_CACHE_KEYS_PER_JOB);

					$offset = 0;
					$array = array();
					$list = $cacheStore->query($query);
					KalturaLog::debug('Found [' . count($list->getObjects()) . '/' . $list->getCount() . '] items');
					while(count($list->getObjects()))
					{
						foreach ($list->getObjects() as $cacheObject)
						{
							/* @var $cacheObject kCouchbaseCacheListItem */
							list($cacheTriggerKey, $cacheObjectType, $cacheSessionKey) = $cacheObject->getKey();
							if(!isset($array[$cacheObjectType]))
							{
								$array[$cacheObjectType] = array();
							}
							if(isset($array[$cacheObjectType][$cacheSessionKey]))
							{
								$array[$cacheObjectType][$cacheSessionKey]++;
							}
							else
							{
								$array[$cacheObjectType][$cacheSessionKey] = 1;
							}
						}
						
						$offset += count($list->getObjects());
						$query->setOffset($offset);
						$list = $cacheStore->query($query);
						KalturaLog::debug('Found [' . count($list->getObjects()) . '/' . $list->getCount() . '] items');
					}
					return $array;
				}
			}
		}
	
		return array();
	}

	protected static function listObjectRelatedTypes($triggerKey)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				// TODO optimize using elastic search query
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_RELATED_OBJECTS_TYPES);
				if($query)
				{
					$query->addStartKey('triggerKey', $triggerKey);
					$query->addStartKey('objectType', 'A');
					$query->addEndKey('triggerKey', $triggerKey);
					$query->addEndKey('objectType', 'z');
					$query->setLimit(self::MAX_CACHE_KEYS_PER_JOB);

					$offset = 0;
					$array = array();
					$list = $cacheStore->query($query);
					KalturaLog::debug('Found [' . count($list->getObjects()) . '/' . $list->getCount() . '] items');
					while(count($list->getObjects()))
					{
						foreach ($list->getObjects() as $cacheObject)
						{
							/* @var $cacheObject kCouchbaseCacheListItem */
							list($cacheTriggerKey, $cacheObjectType) = $cacheObject->getKey();
							$array[$cacheObjectType] = true;
						}
						
						$offset += count($list->getObjects());
						$query->setOffset($offset);
						$list = $cacheStore->query($query);
						KalturaLog::debug('Found [' . count($list->getObjects()) . '/' . $list->getCount() . '] items');
					}
					return array_keys($array);
				}
			}
		}
	
		return array();
	}

	protected static function listObjectSessionTypes(BaseObject $object)
	{
		$objectKey = self::getObjectKey($object);
		if($object instanceof IBaseObject)
		{
			$cacheStores = self::getStores();
			foreach ($cacheStores as $cacheStore)
			{
				if($cacheStore instanceof kCouchbaseCacheWrapper)
				{
					// TODO optimize using elastic search query
					$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_OBJECT_SESSIONS);
					if($query)
					{
						$query->addStartKey('objectKey', $objectKey);
						$query->addStartKey('sessionKey', 'A');
						$query->addEndKey('objectKey', $objectKey);
						$query->addEndKey('sessionKey', 'z');
						$query->setLimit(self::MAX_CACHE_KEYS_PER_JOB);
	
						$list = $cacheStore->query($query);
						KalturaLog::debug('Found [' . count($list->getObjects()) . '/' . $list->getCount() . '] items');
						$array = array();
						foreach ($list->getObjects() as $cacheObject)
						{
							/* @var $cacheObject kCouchbaseCacheListItem */
							list($cacheObjectKey, $cacheSessionKey) = $cacheObject->getKey();
							$array[$cacheSessionKey] = $cacheSessionKey;
						}
						return $array;
					}
				}
			}
		}
	
		return array();
	}

	protected function addRecalculateRelatedObjectsCacheJob(IBaseObject $object)
	{
		KalturaLog::debug('Recalculating object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] related objects');
		
		$partnerId = $object->getPartnerId();
		$triggerKey = self::getRelatedObjectKey($object);
		$objectTypes = self::listObjectRelatedSessions($triggerKey);
		foreach($objectTypes as $objectType => $sessionKeys)
		{
			foreach($sessionKeys as $sessionKey => $count)
			{
				list($protocol, $ksType, $userRoles) = explode('_', $sessionKey, 3);
				$userRoles = explode('_', $userRoles);
				if($count > self::MAX_CACHE_KEYS_PER_JOB)
				{
					$startEndObjectKeys = self::listObjectKeys($objectType, $sessionKey);
					foreach($startEndObjectKeys as $startEndObjectKey)
					{
						list($startObjectKey, $endObjectKey) = $startEndObjectKey;
						kJobsManager::addRecalculateResponseProfileCacheJob($partnerId, $protocol, $ksType, $userRoles, $objectType, null, $startObjectKey, $endObjectKey);
					}
				}
				else
				{
					kJobsManager::addRecalculateResponseProfileCacheJob($partnerId, $protocol, $ksType, $userRoles, $objectType);
				}
			}
		}
		return true;
	}
	
	protected function addRecalculateObjectCacheJob(IBaseObject $object)
	{
		KalturaLog::debug('Recalculating object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] cache');
		$objectType = get_class($object);
		$objectKey = self::getObjectKey($object);
		$partnerId = $object->getPartnerId();
		
		$sessionTypes = self::listObjectSessionTypes($object);
		foreach($sessionTypes as $sessionKey)
		{
			list($protocol, $ksType, $userRoles) = explode('_', $sessionKey, 3);
			$userRoles = explode('_', $userRoles);
			kJobsManager::addRecalculateResponseProfileCacheJob($partnerId, $protocol, $ksType, $userRoles, $objectType, $object->getPrimaryKey());
		}
		return true;
	}
	
	protected function invalidateCachedRootObjects(IBaseObject $object)
	{
		KalturaLog::debug('Invalidating object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] roots');
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
		self::invalidate(self::getObjectKey($object));
		
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_RECALCULATE_RESPONSE_PROFILE_CACHE, $object->getPartnerId()))
		{
			$this->addRecalculateObjectCacheJob($object);
		}
		
		return true;
	}
		
	protected function invalidateCachedRelatedObjects(IBaseObject $object)
	{
		self::invalidateRelated($object);
		
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_RECALCULATE_RESPONSE_PROFILE_CACHE, $object->getPartnerId()))
		{
			$this->addRecalculateRelatedObjectsCacheJob($object);
		}
		
		return true;
	}
}