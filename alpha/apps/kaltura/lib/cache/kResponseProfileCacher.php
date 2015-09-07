<?php
class kResponseProfileCacher implements kObjectChangedEventConsumer, kObjectDeletedEventConsumer, kObjectAddedEventConsumer
{
	const VIEW_RESPONSE_PROFILE_OBJECT_SPECIFIC = 'objectSpecific';
//function (doc, meta) {
//	if (meta.type == "json") {
//		if(doc.type == "primaryObject"){
//			emit(doc.objectKey, null);
//		}
//	}
//}

	const VIEW_RESPONSE_PROFILE_RELATED_OBJECT_SESSIONS = 'relatedObjectSessions';
//function (doc, meta) {
//	if (meta.type == "json") {
//		if(doc.type == "relatedObject"){
// 			emit([doc.triggerKey, doc.objectType, doc.sessionKey], null);
//		}
//	}
//}
	
	const VIEW_RESPONSE_PROFILE_RELATED_OBJECTS_TYPES = 'relatedObjectsTypes';
//function (doc, meta) {
//	if (meta.type == "json") {
//		if(doc.type == "relatedObject"){
// 			emit([doc.triggerKey, doc.objectType], null);
//		}
//	}
//}
	
	const VIEW_RESPONSE_PROFILE_OBJECT_SESSIONS = 'objectSessions';
// function (doc, meta) {
// 	if (meta.type == "json") {
// 		if(doc.type == "primaryObject"){
// 			emit([doc.objectKey, doc.sessionKey], null);
// 		}
// 	}
// }

	const VIEW_RESPONSE_PROFILE_OBJECT_TYPE_SESSIONS = 'objectTypeSessions';
// function (doc, meta) {
// 	if (meta.type == "json") {
// 		if(doc.type == "primaryObject"){
// 			emit([doc.objectType, doc.sessionKey], null);
// 		}
// 	}
// }
	
	const VIEW_RESPONSE_PROFILE_SESSION_TYPE = 'sessionType';
//function (doc, meta) {
//	if (meta.type == "json") {
//		if(doc.type == "primaryObject"){
//			emit([doc.sessionKey, doc.objectKey], doc);
//		}
//	}
//}

	const MAX_CACHE_KEYS_PER_JOB = 1000;
	
	const CACHE_ROOT_OBJECTS = 'CACHE_ROOT_OBJECTS';
	
	const VIEW_KEY_SESSION_KEY = 'sessionKey';
	const VIEW_KEY_TRIGGER_KEY = 'triggerKey';
	const VIEW_KEY_OBJECT_KEY = 'objectKey';
	const VIEW_KEY_OBJECT_TYPE = 'objectType';
	
	/**
	 * @var array
	 */
	private static $cacheStores = null;
	
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
		if(is_array(self::$cacheStores))
			return self::$cacheStores;
			
		self::$cacheStores = array();
		$cacheSections = kCacheManager::getCacheSectionNames($cacheType);
		if(is_array($cacheSections))
		{
			foreach ($cacheSections as $cacheSection)
			{
				$cacheStore = kCacheManager::getCache($cacheSection);
				if ($cacheStore)
					self::$cacheStores[] = $cacheStore;
			}
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
	
	/**
	 * @return string
	 */
	protected static function addCacheVersion($key)
	{
		if(is_null(self::$cacheVersion))
			self::$cacheVersion = kConf::get('response_profile_cache_version', 'local', 1);
			
		if(is_array($key))
			return array_map(array(self, 'addCacheVersion'), $key);
			
		return self::$cacheVersion . '_' . $key;
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
		$invalidationKey = self::addCacheVersion($invalidationKey);
		KalturaLog::debug("Invalidating key [$invalidationKey] now [$now]");
		
		$cacheStores = self::getInvalidationStores();
		foreach ($cacheStores as $cacheStore)
		{
			/* @var $cacheStore kBaseCacheWrapper */
			$cacheStore->set($invalidationKey, $now);
		}
	}
	
	protected static function set($key, $value)
	{
		$key = self::addCacheVersion($key);
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
		$key = self::addCacheVersion($key);
		KalturaLog::debug("Key [$key]");
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			$cacheStore->delete($key);
		}
	}
	
	protected static function getMulti(array $keys)
	{
		$key = self::addCacheVersion($key);
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
		$key = self::addCacheVersion($key);
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
			$invalidationKeys = self::addCacheVersion($invalidationKeys);
			$invalidationCacheStores = self::getInvalidationStores();
			foreach ($invalidationCacheStores as $store)
			{
				/* @var $store kBaseCacheWrapper */
			
				if($store instanceof kCouchbaseCacheWrapper)
				{
					$invalidationTimes = $cacheStore->multiGetAndTouch($invalidationKeys);
				}
				else
				{
					$invalidationTimes = $store->multiGet($invalidationKeys);
				}
				
				if($invalidationTimes)
				{
					$invalidationTime = max($invalidationTimes);
					$invalidationTime += kConf::get('cache_invalidation_threshold', 'local', 10);
					KalturaLog::debug("Invalidation keys [" . implode(', ', $invalidationKeys) . "] times [" . implode(', ', $invalidationTimes) . "] compare to value time [{$value->time}]");
					if(intval($invalidationTime) >= intval($value->time))
					{
						KalturaLog::debug("Invalidation time [$invalidationTime] >= value time [{$value->time}]");
						return null;
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
			
			$list = $cacheStore->query($query);
			if($list->getCount())
			{
				$this->queryCache[__METHOD__] = true;
				return true;
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
	
		if(isset($this->queryCache[kResponseProfileCacher::CACHE_ROOT_OBJECTS]))
		{
			return true;
		}
		
		$roots = $peer->getRootObjects($object);
		if(count($roots))
		{
			$this->queryCache[kResponseProfileCacher::CACHE_ROOT_OBJECTS] = $roots;
			return true;
		}
				
		return false;
	}
	
	protected function isCachedObject(IBaseObject $object)
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
			
			$list = $cacheStore->query($query);
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
	
	protected function addRecalculateObjectCacheJob(IBaseObject $object)
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
	
	protected function invalidateCachedRootObjects(IBaseObject $object)
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