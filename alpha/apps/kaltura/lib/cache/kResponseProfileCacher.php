<?php
class kResponseProfileCacher implements kObjectChangedEventConsumer, kObjectDeletedEventConsumer, kObjectAddedEventConsumer
{
	const VIEW_RESPONSE_PROFILE_OBJECT_SPECIFIC = 'objectSpecific';
	const VIEW_RESPONSE_PROFILE_RELATED_OBJECT_SESSIONS = 'relatedObjectSessions';
	const VIEW_RESPONSE_PROFILE_RELATED_OBJECTS_TYPES = 'relatedObjectsTypes';
	const VIEW_RESPONSE_PROFILE_OBJECT_SESSIONS = 'objectSessions';
	const VIEW_RESPONSE_PROFILE_OBJECT_TYPE_SESSIONS = 'objectTypeSessions';
	const VIEW_RESPONSE_PROFILE_SESSION_TYPE = 'sessionType';

	const MAX_CACHE_KEYS_PER_JOB = 1000;
	
	const CACHE_ROOT_OBJECTS = 'CACHE_ROOT_OBJECTS';
	
	const VIEW_KEY_SESSION_KEY = 'sessionKey';
	const VIEW_KEY_TRIGGER_KEY = 'triggerKey';
	const VIEW_KEY_OBJECT_KEY = 'objectKey';
	const VIEW_KEY_OBJECT_TYPE = 'objectType';
	
	const CACHE_VALUE_HOSTNAME = 'X-Me';
	const CACHE_VALUE_TIME = 'X-Time';
	const CACHE_VALUE_SESSION = 'X-Kaltura-Session';
	const CACHE_VALUE_VERSION = 'X-Cache-Version';
	
	/**
	 * @var array
	 */
	private static $cacheStores = array();
	
	/**
	 * @var int
	 */
	private static $cacheVersion = null;
	
	/**
	 * @var array
	 */
	private $queryCache = array();
	
	/**
	 * @return array<kBaseCacheWrapper>
	 */
	protected static function getStores($cacheType = kCacheManager::CACHE_TYPE_RESPONSE_PROFILE)
	{
		if(isset(self::$cacheStores[$cacheType]))
			return self::$cacheStores[$cacheType];
			
		self::$cacheStores[$cacheType] = array();
		$cacheSections = kCacheManager::getCacheSectionNames($cacheType);
		if(is_array($cacheSections))
		{
			foreach ($cacheSections as $cacheSection)
			{
				$cacheStore = kCacheManager::getCache($cacheSection);
				if ($cacheStore)
					self::$cacheStores[$cacheType][] = $cacheStore;
			}
		}
		
		return self::$cacheStores[$cacheType];
	}
	
	/**
	 * @return array<kBaseCacheWrapper>
	 */
	protected static function getInvalidationStores()
	{
		return self::getStores(kCacheManager::CACHE_TYPE_RESPONSE_PROFILE_INVALIDATION);
	}
	
	protected static function getCacheVersion()
	{
		if(is_null(self::$cacheVersion))
			self::$cacheVersion = kConf::get('response_profile_cache_version', 'local', 1);
			
		return self::$cacheVersion;
	}
	
	/**
	 * @return string
	 */
	protected static function addCacheVersion($key)
	{
		if(is_array($key))
			return array_map(array('self', 'addCacheVersion'), $key);
			
		return self::getCacheVersion() . '_' . $key;
	}
	
	protected static function invalidateRelated(IRelatedObject $object)
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
		$value = array(
			self::CACHE_VALUE_HOSTNAME => infraRequestUtils::getHostname(),
			self::CACHE_VALUE_TIME => time(),
			self::CACHE_VALUE_SESSION => UniqueId::get(),
			self::CACHE_VALUE_VERSION => self::getCacheVersion(),
		);
		
		$invalidationKey = self::addCacheVersion($invalidationKey);
		KalturaLog::debug("Invalidating key [$invalidationKey] now [" . date('Y-m-d H:i:s', $value[self::CACHE_VALUE_TIME]) . "]");
		
		$cacheStores = self::getInvalidationStores();
		foreach ($cacheStores as $cacheStore)
		{
			/* @var $cacheStore kBaseCacheWrapper */
			$queryStart = microtime(true);
			$cacheStore->set($invalidationKey, $value);
			KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds key=$invalidationKey");
		}
	}
	
	protected static function set($key, array $value = array())
	{
		$value[self::CACHE_VALUE_HOSTNAME] = infraRequestUtils::getHostname();
		$value[self::CACHE_VALUE_TIME] = time();
		$value[self::CACHE_VALUE_SESSION] = UniqueId::get();
		$value[self::CACHE_VALUE_VERSION] = self::getCacheVersion();
		
		$key = self::addCacheVersion($key);
		//KalturaLog::debug("Key [$key]");
		
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			/* @var $cacheStore kBaseCacheWrapper */
			$queryStart = microtime(true);
			$cacheStore->set($key, $value);
			KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds key=$key");
		}
	}
	
	protected static function delete($key)
	{
		$key = self::addCacheVersion($key);
		//KalturaLog::debug("Key [$key]");
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			$queryStart = microtime(true);
			$cacheStore->delete($key);
			KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds key=$key");
		}
	}
	
	protected static function get($keys, $touch = true)
	{
		$keys = self::addCacheVersion($keys);
		$cacheStores = self::getStores();
		$value = null;
		foreach ($cacheStores as $cacheStore)
		{
			$queryStart = microtime(true);
			if($touch && $cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$value = is_array($keys) ? $cacheStore->multiGetAndTouch($keys) : $cacheStore->getAndTouch($keys);
			}
			else
			{
				$value = is_array($keys) ? $cacheStore->multiGet($keys) : $cacheStore->get($keys);
			}
			KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds keys=".print_r($keys, true));

			if($value)
			{
				if(is_array($keys))
				{
					foreach($value as $key => $item)
					{
						KalturaLog::debug("Key [$key] Server[" . $item->{self::CACHE_VALUE_HOSTNAME} . "] Session[" . $item->{self::CACHE_VALUE_SESSION} . "] Time[" . date('Y-m-d H:i:s', $item->{self::CACHE_VALUE_TIME}) . "]");
					}
				}
				else
				{
					KalturaLog::debug("Key [$keys] Server[" . $value->{self::CACHE_VALUE_HOSTNAME} . "] Session[" . $value->{self::CACHE_VALUE_SESSION} . "] Time[" . date('Y-m-d H:i:s', $value->{self::CACHE_VALUE_TIME}) . "]");
				}
				return $value;
			}
		}
		KalturaLog::debug("Key [$keys] not found");
			
		return null;
	}
	
	protected static function areKeysValid(array $invalidationKeys, $time)
	{
		$invalidationKeys = self::addCacheVersion($invalidationKeys);
		$invalidationCacheStores = self::getInvalidationStores();
		foreach ($invalidationCacheStores as $store)
		{
			/* @var $store kBaseCacheWrapper */
		
			if($store instanceof kCouchbaseCacheWrapper)
			{
				$invalidationCaches = $store->multiGetAndTouch($invalidationKeys);
			}
			else
			{
				$invalidationCaches = $store->multiGet($invalidationKeys);
			}
			
			if($invalidationCaches)
			{
				$invalidationTimes = array();
				foreach($invalidationKeys as $invalidationKey)
				{
					if(isset($invalidationCaches[$invalidationKey]))
					{
						$value = $invalidationCaches[$invalidationKey];
						KalturaLog::debug("Invalidation key [$invalidationKey] Server[" . $value->{self::CACHE_VALUE_HOSTNAME} . "] Session[" . $value->{self::CACHE_VALUE_SESSION} . "] Time[" . date('Y-m-d H:i:s', $value->{self::CACHE_VALUE_TIME}) . "]");
						$invalidationTimes[] = $value->{self::CACHE_VALUE_TIME};
					}
					else
					{
						KalturaLog::debug("Invalidation key [$invalidationKey] not found");
					}
				}
				
				$invalidationTime = max($invalidationTimes);
				$invalidationTime += kConf::get('cache_invalidation_threshold', 'local', 10);
				if(intval($invalidationTime) >= intval($time))
				{
					KalturaLog::debug("Invalidation times [" . implode(', ', $invalidationTimes) . "] >= [{$time}]");
					return false;
				}
			}	
		}
		
		return true;
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

	protected static function getSessionKey($protocol = null, $ksType = null, array $userRoles = null, $host = null)
	{
		if(!$protocol)
			$protocol = infraRequestUtils::getProtocol();
		if(!$ksType)
			$ksType = kCurrentContext::getCurrentSessionType();
		if(!$userRoles)
			$userRoles = kPermissionManager::getCurrentRoleIds();
		if(!$host)
			$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
			
		sort($userRoles);
		$userRole = implode('-', $userRoles);
		return "{$protocol}_{$ksType}_{$host}_{$userRole}";
	}
	
	protected static function getObjectKey(IRelatedObject $object)
	{
		$partnerId = $object->getPartnerId();
		$objectType = get_class($object);
		$objectId = $object->getPrimaryKey();
		return "{$partnerId}_{$objectType}_{$objectId}";
	}
	
	protected static function getRelatedObjectKey(IRelatedObject $object, $responseProfileKey = null)
	{
		$partnerId = $object->getPartnerId();
		$objectType = get_class($object);
		if($responseProfileKey)
			return "{$partnerId}_{$objectType}_{$responseProfileKey}";
			
		return "{$partnerId}_{$objectType}";
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof ResponseProfile)
			return $this->deleteResponseProfileCache($object);

		/* @var $object IRelatedObject */
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

		if($object instanceof IRelatedObject)
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

		/* @var $object IRelatedObject */
		
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

		if($object instanceof IRelatedObject)
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
		/* @var $object IRelatedObject */
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
		if($object instanceof IRelatedObject)
		{
			if($this->hasCachedRelatedObjects($object))
				return true;
				
			if($this->hasCachedRootObjects($object))
				return true;
		}
			
		return false;
	}
	
	protected function hasCachedRelatedObjects(IRelatedObject $object)
	{
		if(isset($this->queryCache[__METHOD__]))
		{
			return true;
		}
			
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if(!($cacheStore instanceof kCouchbaseCacheWrapper))
			{
				continue;
			}

			$query = $cacheStore->getNewQuery(kResponseProfileCacher::VIEW_RESPONSE_PROFILE_RELATED_OBJECT_SESSIONS);
			if(!$query)
			{
				continue;
			}
			
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_TRIGGER_KEY, self::getRelatedObjectKey($object));
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_OBJECT_TYPE, 'A');
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_SESSION_KEY, 'A');
			$query->setLimit(1);

			$queryStart = microtime(true);
			$list = $cacheStore->query($query);
			KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds objectClass=".get_class($object));
			if($list->getCount())
			{
				$this->queryCache[__METHOD__] = true;
				return true;
			}
		}
		
		return false;
	}
	
	protected function hasCachedRootObjects(IRelatedObject $object)
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
	
		if(isset($this->queryCache[kResponseProfileCacher::CACHE_ROOT_OBJECTS]))
		{
			return true;
		}
		
		$roots = $peer->getRootObjects($object);
		foreach($roots as $index => $root)
		{
			if(!$this->isCachedObject($root))
				unset($roots[$index]);
		}
		
		if(count($roots))
		{
			$this->queryCache[kResponseProfileCacher::CACHE_ROOT_OBJECTS] = $roots;
			return true;
		}
				
		return false;
	}
	
	protected function isCachedObject(IRelatedObject $object)
	{
		if(isset($this->queryCache[__METHOD__]))
		{
			return true;
		}
		
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if(!($cacheStore instanceof kCouchbaseCacheWrapper))
			{
				continue;
			}
			
			$query = $cacheStore->getNewQuery(kResponseProfileCacher::VIEW_RESPONSE_PROFILE_OBJECT_SPECIFIC);
			if(!$query)
			{
				continue;
			}
			
			$query->setKey(self::getObjectKey($object));
			$query->setLimit(1);
			
			$queryStart = microtime(true);
			$list = $cacheStore->query($query);
			KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds objectClass=".get_class($object));
			if($list->getCount())
			{
				$this->queryCache[__METHOD__] = true;
				return true;
			}
		}
		
		return false;
	}
	
	protected static function getResponseProfileCacheKey($responseProfileKey, $partnerId)
	{
		return "rp_rp{$responseProfileKey}_p{$partnerId}";
	}
	
	protected function deleteResponseProfileCache(ResponseProfile $responseProfile)
	{
		$key = self::getResponseProfileCacheKey($responseProfile->getKey(), $responseProfile->getPartnerId());
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
			if(!($cacheStore instanceof kCouchbaseCacheWrapper))
			{
				continue;
			}
			
			// TODO optimize using elastic search query
			$query = $cacheStore->getNewQuery(kResponseProfileCacher::VIEW_RESPONSE_PROFILE_SESSION_TYPE);
			if(!$query)
				continue;
				
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_SESSION_KEY, $sessionKey);
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_OBJECT_KEY, $objectKey);
			$query->setLimit(1);

			$queryStart = microtime(true);
			$list = $cacheStore->query($query);
			KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds objectType=".$objectType);
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
				$queryStart = microtime(true);
				$list = $cacheStore->query($query);
				KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds objectType=".$objectType);
			}
			return $array;
		}
	
		return array();
	}
	
	protected static function listObjectRelatedSessions($triggerKey)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if(!($cacheStore instanceof kCouchbaseCacheWrapper))
			{
				continue;
			}
			
			// TODO optimize using elastic search query
			$query = $cacheStore->getNewQuery(kResponseProfileCacher::VIEW_RESPONSE_PROFILE_RELATED_OBJECT_SESSIONS);
			if(!$query)
			{
				continue;
			}
			
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_TRIGGER_KEY, $triggerKey);
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_OBJECT_TYPE, 'A');
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_SESSION_KEY, 'A');
			$query->addEndKey(kResponseProfileCacher::VIEW_KEY_TRIGGER_KEY, $triggerKey);
			$query->addEndKey(kResponseProfileCacher::VIEW_KEY_OBJECT_TYPE, 'z');
			$query->addEndKey(kResponseProfileCacher::VIEW_KEY_SESSION_KEY, 'z');
			$query->setLimit(self::MAX_CACHE_KEYS_PER_JOB);

			$offset = 0;
			$array = array();
			$queryStart = microtime(true);
			$list = $cacheStore->query($query);
			KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds triggerKey=".$triggerKey);

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
				$queryStart = microtime(true);
				$list = $cacheStore->query($query);
				KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds triggerKey=".$triggerKey);

				KalturaLog::debug('Found [' . count($list->getObjects()) . '/' . $list->getCount() . '] items');
			}
			return $array;
		}
	
		return array();
	}

	protected static function listObjectRelatedTypes($triggerKey)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if(!($cacheStore instanceof kCouchbaseCacheWrapper))
			{
				continue;
			}
		
			// TODO optimize using elastic search query
			$query = $cacheStore->getNewQuery(kResponseProfileCacher::VIEW_RESPONSE_PROFILE_RELATED_OBJECTS_TYPES);
			if(!$query)
			{
				continue;
			}
			
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_TRIGGER_KEY, $triggerKey);
			$query->addStartKey(kResponseProfileCacher::VIEW_KEY_OBJECT_TYPE, 'A');
			$query->addEndKey(kResponseProfileCacher::VIEW_KEY_TRIGGER_KEY, $triggerKey);
			$query->addEndKey(kResponseProfileCacher::VIEW_KEY_OBJECT_TYPE, 'z');
			$query->setLimit(self::MAX_CACHE_KEYS_PER_JOB);

			$offset = 0;
			$array = array();
			$queryStart = microtime(true);
			$list = $cacheStore->query($query);
			KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds triggerKey=".$triggerKey);

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
				$queryStart = microtime(true);
				$list = $cacheStore->query($query);
				KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds triggerKey=".$triggerKey);

				KalturaLog::debug('Found [' . count($list->getObjects()) . '/' . $list->getCount() . '] items');
			}
			return array_keys($array);
		}
	
		return array();
	}

	protected static function listObjectSessionTypes(BaseObject $object)
	{
		$objectKey = self::getObjectKey($object);
		if($object instanceof IRelatedObject)
		{
			$cacheStores = self::getStores();
			foreach ($cacheStores as $cacheStore)
			{
				if(!($cacheStore instanceof kCouchbaseCacheWrapper))
				{
					continue;
				}
			
				// TODO optimize using elastic search query
				$query = $cacheStore->getNewQuery(kResponseProfileCacher::VIEW_RESPONSE_PROFILE_OBJECT_SESSIONS);
				if(!$query)
				{
					continue;
				}
				
				$query->addStartKey(kResponseProfileCacher::VIEW_KEY_OBJECT_KEY, $objectKey);
				$query->addStartKey(kResponseProfileCacher::VIEW_KEY_SESSION_KEY, 'A');
				$query->addEndKey(kResponseProfileCacher::VIEW_KEY_OBJECT_KEY, $objectKey);
				$query->addEndKey(kResponseProfileCacher::VIEW_KEY_SESSION_KEY, 'z');
				$query->setLimit(self::MAX_CACHE_KEYS_PER_JOB);

				$queryStart = microtime(true);
				$list = $cacheStore->query($query);
				KalturaLog::debug("query took " . (microtime(true) - $queryStart) . " seconds objectKey = " . $objectKey);

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
	
		return array();
	}

	protected function addRecalculateRelatedObjectsCacheJob(IRelatedObject $object)
	{
		KalturaLog::debug('Recalculating object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] related objects');
		
		$partnerId = $object->getPartnerId();
		$triggerKey = self::getRelatedObjectKey($object);
		$objectTypes = self::listObjectRelatedSessions($triggerKey);
		foreach($objectTypes as $objectType => $sessionKeys)
		{
			foreach($sessionKeys as $sessionKey => $count)
			{
				list($protocol, $ksType, $host, $userRoles) = explode('_', $sessionKey, 4);
				$userRoles = explode('-', $userRoles);
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
	
	protected function addRecalculateObjectCacheJob(IRelatedObject $object)
	{
		KalturaLog::debug('Recalculating object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] cache');
		$objectType = get_class($object);
		$objectKey = self::getObjectKey($object);
		$partnerId = $object->getPartnerId();
		
		$sessionTypes = self::listObjectSessionTypes($object);
		foreach($sessionTypes as $sessionKey)
		{
			list($protocol, $ksType, $host, $userRoles) = explode('_', $sessionKey, 4);
			$userRoles = explode('-', $userRoles);
			kJobsManager::addRecalculateResponseProfileCacheJob($partnerId, $protocol, $ksType, $userRoles, $objectType, $object->getPrimaryKey());
		}
		return true;
	}
	
	protected function invalidateCachedRootObjects(IRelatedObject $object)
	{
		KalturaLog::debug('Invalidating object [' . get_class($object) . '] id [' . $object->getPrimaryKey() . '] roots');
		
		$roots = null;
		if(isset($this->queryCache[kResponseProfileCacher::CACHE_ROOT_OBJECTS]))
		{
			$roots = $this->queryCache[kResponseProfileCacher::CACHE_ROOT_OBJECTS];
		}
		else
		{
			$peer = $object->getPeer();
			if($peer instanceof IRelatedObjectPeer)
			{
				$roots = $peer->getRootObjects($object);
			}
		}
	
		if(is_array($roots))
		{
			foreach($roots as $root)
			{
				if(!is_null($root))
				{
					$this->invalidateCachedObject($root);
				}
			}
		}
		
		return true;
	}
	
	protected function invalidateCachedObject(IRelatedObject $object)
	{
		self::invalidate(self::getObjectKey($object));
		
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_RECALCULATE_RESPONSE_PROFILE_CACHE, $object->getPartnerId()))
		{
			$this->addRecalculateObjectCacheJob($object);
		}
		
		return true;
	}
		
	protected function invalidateCachedRelatedObjects(IRelatedObject $object)
	{
		self::invalidateRelated($object);
		
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_RECALCULATE_RESPONSE_PROFILE_CACHE, $object->getPartnerId()))
		{
			$this->addRecalculateRelatedObjectsCacheJob($object);
		}
		
		return true;
	}
}