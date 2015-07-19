<?php
class KalturaResponseProfileCacher extends kResponseProfileCacher
{
	/**
	 * @var IBaseObject
	 */
	private static $cachedObject = null;
	
	/**
	 * @var string
	 */
	private static $responseProfileKey = null;
	
	private static function getObjectSpecificCacheValue(KalturaObject $apiObject, IBaseObject $object, $responseProfileKey)
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
	
	private static function getObjectSpecificCacheKey(IBaseObject $object, $responseProfileKey)
	{
		$userRoles = kPermissionManager::getCurrentRoleIds();
		sort($userRoles);
		
		$objectType = get_class($object);
		$objectId = $object->getPrimaryKey();
		$partnerId = $object->getPartnerId();
		$profileKey = $responseProfileKey;
		$protocol = infraRequestUtils::getProtocol();
		$ksType = kCurrentContext::getCurrentSessionType();
		$userRoles = implode('_', $userRoles);
		$entitlement = (int) kEntitlementUtils::getEntitlementEnforcement();
		
		return "obj_rp{$profileKey}_p{$partnerId}_o{$objectType}_i{$objectId}_h{$protocol}_k{$ksType}_u{$userRoles}_e{$entitlement}";
	}
	
	private static function getObjectTypeCacheValue(IBaseObject $object)
	{
		return array(
			'type' => 'relatedObject',
			'triggerKey' => self::getRelatedObjectKey($object),
			'objectType' => get_class(self::$cachedObject),
			'objectPeer' => get_class(self::$cachedObject->getPeer()),
			'triggerObjectType' => get_class($object),
			'partnerId' => self::$cachedObject->getPartnerId(),
			'responseProfileKey' => self::$responseProfileKey,
			'sessionKey' => self::getSessionKey(),
		);
	}
	
	private static function getObjectTypeCacheKey(IBaseObject $object)
	{
		$userRoles = kPermissionManager::getCurrentRoleIds();
		sort($userRoles);
		
		$objectType = get_class($object);
		$partnerId = self::$cachedObject->getPartnerId();
		$profileKey = self::$responseProfileKey;
		$protocol = infraRequestUtils::getProtocol();
		$ksType = kCurrentContext::getCurrentSessionType();
		$userRoles = implode('_', $userRoles);
		
		return "relate_rp{$profileKey}_p{$partnerId}_o{$objectType}_h{$protocol}_k{$ksType}_u{$userRoles}";
	}
	
	private static function getResponseProfileCacheKey($responseProfileKey, $partnerId)
	{
		return "rp_rp{$responseProfileKey}_p{$partnerId}";
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
				KalturaLog::debug("Set [$key] value [" . print_r($value, true) . "]");
				
				self::set($key, $value);
			}
		}
	}
	
	public static function start(IBaseObject $object, KalturaDetachedResponseProfile $responseProfile)
	{
		if(self::$cachedObject)
		{
			KalturaLog::debug("Object [" . get_class(self::$cachedObject) . "][" . self::$cachedObject->getId() . "] still caching");
			return null;
		}
			
		KalturaLog::debug("Start " . get_class($object) . " [" . $object->getId() . "]");
		$responseProfileKey = $responseProfile->getKey();
		$setResponseProfile = (self::$responseProfileKey != $responseProfileKey);
		
		$responseProfileCacheKey = self::getResponseProfileCacheKey($responseProfileKey, $object->getPartnerId());
		if(self::get($responseProfileCacheKey))
		{
			$key = self::getObjectSpecificCacheKey($object, $responseProfileKey);
			$invalidationKeys = array(
				self::getObjectKey($object),
				self::getRelatedObjectKey($object),
			);
			
			$value = self::get($key, $invalidationKeys);
			if($value)
			{
				$apiObject = unserialize($value->apiObject);
				KalturaLog::debug("Returned object: [" . print_r($apiObject, true) . "]");
				if($apiObject instanceof KalturaObject)
					return $apiObject->relatedObjects;
			}
			KalturaLog::debug("Object [" . get_class($object) . "][" . $object->getId() . "] - response profile found but object not cached");
		}
		else 
		{
			KalturaLog::debug("Object [" . get_class($object) . "][" . $object->getId() . "] - response profile not found");
		}
		self::$cachedObject = $object;
		self::$responseProfileKey = $responseProfileKey;
		
		if($setResponseProfile)
			self::set($responseProfileCacheKey, serialize($responseProfile));
	}
	
	public static function stop(IBaseObject $object, KalturaObject $apiObject)
	{
		if($object !== self::$cachedObject)
		{
			KalturaLog::debug("Object [" . get_class(self::$cachedObject) . "][" . self::$cachedObject->getId() . "] still caching");
			return;
		}

		KalturaLog::debug("Stop " . get_class($apiObject) . " [" . print_r($apiObject, true) . "]");
		
		$key = self::getObjectSpecificCacheKey(self::$cachedObject, self::$responseProfileKey);
		$value = self::getObjectSpecificCacheValue($apiObject, self::$cachedObject, self::$responseProfileKey);
		
		self::set($key, $value);
		
		self::$cachedObject = null;
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
			$responseProfile = unserialize($value);
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
		/* @var $object IBaseObject */
		
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
			
		$lastRecalculateTime = self::get($uniqueKey, null, false);
		if($options->isFirstLoop)
		{
			if($lastRecalculateTime >= $options->jobCreatedAt)
				throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_CACHE_ALREADY_RECALCULATED);
				
			$lastRecalculateTime = time();
			self::set($uniqueKey, $lastRecalculateTime);
		}
		
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$results = new KalturaResponseProfileCacheRecalculateResults();
		
		/* @var $object IBaseObject */
		$responseProfile = null;
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$query = $cacheStore->getNewQuery(kResponseProfileCacher::VIEW_RESPONSE_PROFILE_SESSION_TYPE);
				if(!$query)
					continue;
					
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
				
				$newRecalculateTime = self::get($uniqueKey, null, false);
				if($newRecalculateTime > $lastRecalculateTime)
					throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_CACHE_RECALCULATE_RESTARTED);
			}
		}
		return $results;
	}
}