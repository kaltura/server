<?php
class KalturaResponseProfileCacher extends kResponseProfileCacher
{
	/**
	 * @var IRelatedObject
	 */
	private static $cachedObject = null;
	
	/**
	 * @var string
	 */
	private static $responseProfileKey = null;
	
	/**
	 * @var boolean
	 */
	private static $cachePerUser = false;
	
	/**
	 * @var array
	 */
	private static $storedObjectTypeKeys = array();
	
	private static function getObjectSpecificCacheValue(KalturaObject $apiObject, IRelatedObject $object, $responseProfileKey)
	{
		return array(
			'type' => 'primaryObject',
			'time' => time(),
			'objectKey' => self::getObjectKey($object),
			'sessionKey' => self::getSessionKey(),
			'objectType' => get_class($object),
			'objectPeer' => get_class($object->getPeer()),
			'objectId' => $object->getPrimaryKey(),
			'partnerId' => $object->getPartnerId(),
			'responseProfileKey' => $responseProfileKey,
			'apiObject' => serialize($apiObject)
		);
	}
	
	private static function getObjectSpecificCacheKey(IRelatedObject $object, $responseProfileKey)
	{
		$userRoles = kPermissionManager::getCurrentRoleIds();
		sort($userRoles);
		
		$objectType = get_class($object);
		$objectId = $object->getPrimaryKey();
		$partnerId = $object->getPartnerId();
		$profileKey = $responseProfileKey;
		$protocol = infraRequestUtils::getProtocol();
		$ksType = kCurrentContext::getCurrentSessionType();
		$userRoles = implode('-', $userRoles);
		$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$entitlement = (int) kEntitlementUtils::getEntitlementEnforcement();
		
		if(self::$cachePerUser)
		{
			$user = kCurrentContext::getCurrentKsKuserId();
			return "obj_rp{$profileKey}_p{$partnerId}_o{$objectType}_i{$objectId}_h{$protocol}_k{$ksType}_u{$userRoles}_w{$host}_e{$entitlement}_us{$user}";
		}
		else
		{
			return "obj_rp{$profileKey}_p{$partnerId}_o{$objectType}_i{$objectId}_h{$protocol}_k{$ksType}_u{$userRoles}_w{$host}_e{$entitlement}";
		}
	}
	
	private static function getObjectTypeCacheValue(IRelatedObject $object)
	{
		return array(
			'type' => 'relatedObject',
			'triggerKey' => self::getRelatedObjectKey($object),
			'objectType' => get_class(self::$cachedObject) . '_' . self::$responseProfileKey,
			'objectPeer' => get_class(self::$cachedObject->getPeer()),
			'triggerObjectType' => get_class($object),
			'partnerId' => self::$cachedObject->getPartnerId(),
			'responseProfileKey' => self::$responseProfileKey,
			'sessionKey' => self::getSessionKey(),
		);
	}
	
	private static function getObjectTypeCacheKey(IRelatedObject $object)
	{
		$userRoles = kPermissionManager::getCurrentRoleIds();
		sort($userRoles);
		
		$objectType = get_class($object);
		$partnerId = self::$cachedObject->getPartnerId();
		$profileKey = self::$responseProfileKey;
		$protocol = infraRequestUtils::getProtocol();
		$ksType = kCurrentContext::getCurrentSessionType();
		$userRoles = implode('-', $userRoles);
		$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		
		return "relate_rp{$profileKey}_p{$partnerId}_o{$objectType}_h{$protocol}_k{$ksType}_u{$userRoles}_w{$host}";
	}
	
	public static function onPersistentObjectLoaded(IRelatedObject $object)
	{
		if(!self::$cachedObject)
			return;
			
		KalturaLog::debug("Loaded " . get_class($object) . " [" . $object->getId() . "]");
		
		$peer = $object->getPeer();
		if($peer instanceof IRelatedObjectPeer)
		{
			$key = self::getObjectTypeCacheKey($object);
			if(isset(self::$storedObjectTypeKeys[$key]))
			{
				return;
			}
			self::$storedObjectTypeKeys[$key] = true;
			$value = self::getObjectTypeCacheValue($object);
			KalturaLog::debug("Set [$key]");
			
			self::set($key, $value);
		}
	}
	
	public static function useUserCache()
	{
		self::$cachePerUser = true;
	}
	
	/**
	 * @param KalturaObject $apiObject
	 * @param IRelatedObject $object
	 * @param KalturaDetachedResponseProfile $responseProfile
	 * @return boolean
	 */
	public static function start(KalturaObject $apiObject, IRelatedObject $object, KalturaDetachedResponseProfile $responseProfile)
	{
		if(self::$cachedObject)
		{
			KalturaLog::debug("Object [" . get_class(self::$cachedObject) . "][" . self::$cachedObject->getId() . "] still caching");
			return false;
		}
			
		$responseProfileKey = $responseProfile->getKey();
		$key = self::getObjectSpecificCacheKey($object, $responseProfileKey);
		$responseProfileCacheKey = self::getResponseProfileCacheKey($responseProfileKey, $object->getPartnerId());
		
		list($value, $responseProfileCache) = self::get(array($key, $responseProfileCacheKey));
		
		$invalidationKeys = array(
			self::getObjectKey($object),
			self::getRelatedObjectKey($object, $responseProfileKey),
		);
		
		if($value && self::areKeysValid($invalidationKeys, $value->{self::CACHE_VALUE_TIME}))
		{
			$cachedApiObject = unserialize($value->apiObject);
			if($cachedApiObject instanceof KalturaObject)
			{
				$properties = get_object_vars($cachedApiObject);
				foreach ($properties as $propertyName => $propertyValue)
				{
					$apiObject->$propertyName = $propertyValue;
				}
				return true;
			}
			KalturaLog::err("Object [" . get_class($object) . "][" . $object->getId() . "] - invalid object cached");
		}
		KalturaLog::debug("Start " . get_class($object) . " [" . $object->getId() . "]");
		
		if(self::$responseProfileKey != $responseProfileKey && !$responseProfileCache)
		{
			self::set($responseProfileCacheKey, array('responseProfile' => serialize($responseProfile)));
		}
		
		self::$cachedObject = $object;
		self::$responseProfileKey = $responseProfileKey;
		
		return false;
	}
	
	public static function stop(IRelatedObject $object, KalturaObject $apiObject)
	{
		if($object !== self::$cachedObject)
		{
			KalturaLog::debug("Object [" . get_class(self::$cachedObject) . "][" . self::$cachedObject->getId() . "] still caching");
			return;
		}

		if($apiObject->relatedObjects)
		{
			KalturaLog::debug("Stop " . get_class($apiObject));
			
			$key = self::getObjectSpecificCacheKey(self::$cachedObject, self::$responseProfileKey);
			$value = self::getObjectSpecificCacheValue($apiObject, self::$cachedObject, self::$responseProfileKey);
			
			self::set($key, $value);
		}
		else
		{
			KalturaLog::debug("API Object [" . get_class($apiObject) . "] has no related objects");
		}
		
		self::$cachedObject = null;
		self::$cachePerUser = false;
	}
	
	protected static function recalculateCache(kCouchbaseCacheListItem $cache, KalturaDetachedResponseProfile $responseProfile = null)
	{
		KalturaLog::debug("Cache object id [" . $cache->getId() . "]");
		$data = $cache->getData();
		if(!$data)
		{
			KalturaLog::err("Cache object contains no data for id [" . $cache->getId() . "]");
			self::delete($cache->getId());
			return;
		}
		
		if(!$responseProfile)
		{
			$responseProfileCacheKey = self::getResponseProfileCacheKey($data['responseProfileKey'], $data['partnerId']);
			$value = self::get($responseProfileCacheKey);
			$responseProfile = unserialize($value['responseProfile']);
			if(!$responseProfile)
			{
				KalturaLog::err("Response-Profile key [$responseProfileCacheKey] not found in cache");
				self::delete($cache->getId());
				return;
			}
		}
		
		$peer = $data['objectPeer'];
		$object = $peer::retrieveByPK($data['objectId']);
		if(!$object)
		{
			KalturaLog::err("Object $peer [" . $data['objectId'] . "] not found");
			self::delete($cache->getId());
			return;
		}
		/* @var $object IRelatedObject */
		
		$apiObject = unserialize($data['apiObject']);
		$apiObject->fromObject($object, $responseProfile);
		
		$key = self::getObjectSpecificCacheKey($object, $responseProfile->getKey());
		$value = self::getObjectSpecificCacheValue($apiObject, $object, $responseProfile->getKey());
		
		self::set($key, $value);
	}
	
	/**
	 * @param KalturaResponseProfileCacheRecalculateOptions $options
	 * @return KalturaResponseProfileCacheRecalculateResults
	 */
	public static function recalculateCacheBySessionType(KalturaResponseProfileCacheRecalculateOptions $options)
	{
		$sessionKey = self::getSessionKey();
		
		$uniqueKey = "recalc_{$sessionKey}_{$options->cachedObjectType}";
		if($options->objectId)
			$uniqueKey .= "_{$options->objectId}";
			
		$lastRecalculateCache = self::get($uniqueKey, false);
		$lastRecalculateTime = $lastRecalculateCache->{self::CACHE_VALUE_TIME};
		if($options->isFirstLoop)
		{
			if($lastRecalculateTime >= $options->jobCreatedAt)
				throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_CACHE_ALREADY_RECALCULATED);
				
			$lastRecalculateTime = time();
			self::set($uniqueKey);
		}
		
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$results = new KalturaResponseProfileCacheRecalculateResults();
		
		/* @var $object IRelatedObject */
		$responseProfile = null;
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if(!($cacheStore instanceof kCouchbaseCacheWrapper))
			{
				continue;
			}
			
			$query = $cacheStore->getNewQuery(kResponseProfileCacher::VIEW_RESPONSE_PROFILE_SESSION_TYPE);
			if(!$query)
			{
				continue;
			}
				
			$query->setLimit($options->limit + 1);
			if($options->objectId)
			{
				$objectKey = "{$partnerId}_{$options->cachedObjectType}_{$options->objectId}";
				KalturaLog::debug("Serach for key [$sessionKey, $objectKey]");
				$query->addKey(kResponseProfileCacher::VIEW_KEY_SESSION_KEY, $sessionKey);
				$query->addKey(kResponseProfileCacher::VIEW_KEY_OBJECT_KEY, $objectKey);
			}
			else 
			{
				$objectKey = "{$partnerId}_{$options->cachedObjectType}_";
				if($options->startObjectKey)
					$objectKey = $options->startObjectKey;
					
				KalturaLog::debug("Serach for start key [$sessionKey, $objectKey]");
				$query->addStartKey(kResponseProfileCacher::VIEW_KEY_SESSION_KEY, $sessionKey);
				$query->addStartKey(kResponseProfileCacher::VIEW_KEY_OBJECT_KEY, $objectKey);
				
				if($options->endObjectKey)
				{
					$query->addEndKey(kResponseProfileCacher::VIEW_KEY_SESSION_KEY, $sessionKey);
					$query->addEndKey(kResponseProfileCacher::VIEW_KEY_OBJECT_KEY, $options->endObjectKey);
				}
			}

			$results->recalculated = 0;
			$list = $cacheStore->query($query);
			if(!$list->getCount())
				continue;
				
			$cachedObjects = $list->getObjects();
			$exitCount = count($cachedObjects) > $options->limit ? 1 : 0;
			
			do
			{
				self::recalculateCache(array_shift($cachedObjects));
				$results->recalculated++;
			} while(count($cachedObjects) > $exitCount);
			
			if(!count($cachedObjects)){
				continue;
			}
			
			$cache = reset($cachedObjects);
			/* @var $cache kCouchbaseCacheListItem */
			list($cachedSessionKey, $cachedObjectKey) = $cache->getKey();
			$results->lastObjectKey = $cachedObjectKey;
			
			$newRecalculateCache = self::get($uniqueKey, false);
			$newRecalculateTime = $lastRecalculateCache->{self::CACHE_VALUE_TIME};
			if($newRecalculateTime > $lastRecalculateTime)
				throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_CACHE_RECALCULATE_RESTARTED);
		}
		return $results;
	}
}