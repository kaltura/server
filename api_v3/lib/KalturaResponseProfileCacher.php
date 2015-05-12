<?php
class KalturaResponseProfileCacher implements kObjectChangedEventConsumer, kObjectDeletedEventConsumer, kObjectAddedEventConsumer
{
	/**
	 * @var IBaseObject
	 */
	private static $cachedObject = null;
	
	/**
	 * @var KalturaDetachedResponseProfile
	 */
	private static $responseProfile = null;
	
	/**
	 * @var array
	 */
	private static $cacheStores = null;
	
	/**
	 * @return array<kBaseCacheWrapper>
	 */
	private static function getStores()
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
	
	private static function set($key, $value)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			KalturaLog::debug("Save store [" . get_class($cacheStore) . "] key [$key] [" . json_encode($value) . "]");
			$cacheStore->set($key, $value);
		}
	}
	
	private static function delete($key)
	{
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			KalturaLog::debug("Delete store [" . get_class($cacheStore) . "] key [$key]");
			$cacheStore->delete($key);
		}
	}
	
	private static function get($key)
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
	
	private static function query(kCouchbaseCacheQuery $query)
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
	
	private static function getObjectSpecificCacheValue(KalturaObject $object)
	{
		return array(
			'type' => 'primaryObject',
			'objectType' => get_class(self::$cachedObject),
			'objectPeer' => get_class(self::$cachedObject->getPeer()),
			'objectId' => self::$cachedObject->getPrimaryKey(),
			'partnerId' => self::$cachedObject->getPartnerId(),
			'responseProfileKey' => self::$responseProfile->getKey(),
			'apiObjectType' => get_class($object),
			'apiObject' => $object,
		);
	}
	
	private static function getObjectSpecificCacheKey()
	{
		$objectType = get_class(self::$cachedObject);
		$objectId = self::$cachedObject->getPrimaryKey();
		$partnerId = self::$cachedObject->getPartnerId();
		$profileKey = self::$responseProfile->getKey();
		$protocol = infraRequestUtils::getProtocol();
		$ksType = kCurrentContext::getCurrentSessionType();
		$userRole = kPermissionManager::getCurrentRoleIds();
		
		return "rp{$profileKey}_p{$partnerId}_o{$objectType}_i{$objectId}_h{$protocol}_k{$ksType}_u{$userRole}";
	}
	
	private static function getObjectTypeCacheValue(IBaseObject $object)
	{
		return array(
			'type' => 'relatedObject',
			'objectType' => get_class(self::$cachedObject),
			'objectPeer' => get_class(self::$cachedObject->getPeer()),
			'triggerObjectType' => get_class($object),
			'partnerId' => self::$cachedObject->getPartnerId(),
			'responseProfileKey' => self::$responseProfile->getKey()
		);
	}
	
	private static function getObjectTypeCacheKey(IBaseObject $object)
	{
		$objectType = get_class(self::$cachedObject);
		$partnerId = self::$cachedObject->getPartnerId();
		$profileKey = self::$responseProfile->getKey();
		
		return "rp{$profileKey}_p{$partnerId}_o{$objectType}";
	}
	
	private static function getResponseProfileCacheKey($responseProfileKey = null)
	{
		if(is_null($responseProfileKey))
			$responseProfileKey = self::$responseProfile->getKey();
			
		$partnerId = self::$cachedObject->getPartnerId();
		
		return "rp{$responseProfileKey}_p{$partnerId}";
	}
	
	public static function onPersistentObjectLoaded(IBaseObject $object)
	{
		if(!self::$cachedObject)
			return;
			
		KalturaLog::debug("Loaded " . get_class($object) . " [" . $object->getId() . "]");
		
		$peer = $object->getPeer();
		if($peer instanceof IRelatedObjectPeer)
		{
			if($peer->isReferenced($object))
			{
				$key = self::getObjectTypeCacheKey($object);
				$value = self::getObjectTypeCacheValue($object);
				
				self::set($key, $value);
			}
		}
	}
	
	public static function start(IBaseObject $object, KalturaDetachedResponseProfile $responseProfile)
	{
		if(self::$cachedObject)
			return null;
			
		KalturaLog::debug("Start " . get_class($object) . " [" . $object->getId() . "]");
		$setResponseProfile = (self::$responseProfile != $responseProfile);
		
		self::$cachedObject = $object;
		self::$responseProfile = $responseProfile;
		
		$responseProfileCacheKey = self::getResponseProfileCacheKey();
		if(self::get($responseProfileCacheKey))
		{
			$key = self::getObjectSpecificCacheKey();
			$value = self::get($key);
			if($value)
			{
				$apiObject = $value['apiObject'];
				if($apiObject instanceof KalturaObject)
					return $apiObject->relatedObjects;
			}
		}
		
		if($setResponseProfile)
			self::set($responseProfileCacheKey, $responseProfile);
	}
	
	public static function stop(IBaseObject $object, KalturaObject $apiObject)
	{
		if($object !== self::$cachedObject)
			return;
			
		KalturaLog::debug("Stop " . get_class($apiObject) . " [" . json_encode($apiObject) . "]");
		
		$key = self::getObjectSpecificCacheKey();
		$value = self::getObjectSpecificCacheValue($apiObject);
		
		self::set($key, $value);
		
		self::$cachedObject = null;
		self::$responseProfile = null;
	}
	
	protected function deleteResponseProfileCache(ResponseProfile $responseProfile)
	{
		$key = self::getResponseProfileCacheKey($responseProfile->getKey());
		self::delete($key);
		
		return true;
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