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
	
	/**
	 * @var boolean
	 */
	private static $disabled = false;
	
	
	
	private static function getObjectSpecificCacheValue(KalturaObject $apiObject, IBaseObject $object, $responseProfileKey)
	{
		return array(
			'type' => 'primaryObject',
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
		
		return "rp{$profileKey}_p{$partnerId}_o{$objectType}_i{$objectId}_h{$protocol}_k{$ksType}_u{$userRoles}_e{$entitlement}";
	}
	
	private static function getObjectTypeCacheValue(IBaseObject $object)
	{
		return array(
			'type' => 'relatedObject',
			'triggerKey' => self::getTriggerKey($object),
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
		
		$objectType = get_class(self::$cachedObject);
		$partnerId = self::$cachedObject->getPartnerId();
		$profileKey = self::$responseProfileKey;
		$protocol = infraRequestUtils::getProtocol();
		$ksType = kCurrentContext::getCurrentSessionType();
		$userRoles = implode('_', $userRoles);
		
		return "rp{$profileKey}_p{$partnerId}_o{$objectType}_h{$protocol}_k{$ksType}_u{$userRoles}";
	}
	
	private static function getResponseProfileCacheKey($responseProfileKey, $partnerId)
	{
		return "rp{$responseProfileKey}_p{$partnerId}";
	}
	
	public static function onPersistentObjectLoaded(IBaseObject $object)
	{
		if(!self::$cachedObject || self::$disabled)
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
			$value = self::get($key);
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
	
	public static function disable()
	{
		self::$cachedObject = null;
		self::$disabled = true;
	}
	
	public static function stop(IBaseObject $object, KalturaObject $apiObject)
	{
		if($object !== self::$cachedObject)
		{
			KalturaLog::debug("Object [" . get_class(self::$cachedObject) . "][" . self::$cachedObject->getId() . "] still caching");
			return;
		}

		if(self::$disabled)
		{
			self::$disabled = false;
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
		$data = $cache->getData();
		
		if(!$responseProfile)
		{
			$responseProfileCacheKey = self::getResponseProfileCacheKey($data->responseProfileKey, $data->partnerId);
			$value = self::get($responseProfileCacheKey);
			$responseProfile = unserialize($value);
			if(!$responseProfile)
			{
				KalturaLog::err("Response-Profile key [$responseProfileCacheKey] not found in cache");
				self::delete($cache->getId());
			}
		}
		
		$peer = $data->objectPeer;
		$object = $peer::retrieveByPK($data->objectId);
		/* @var $object IBaseObject */
		
		$apiObject = unserialize($data->apiObject);
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
		$results = new KalturaResponseProfileCacheRecalculateResults();
		
		$limit = 100;
		if($options->limit)
			$limit = min($limit, $options->limit);
			
		/* @var $object IBaseObject */
		$responseProfile = null;
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_SESSION_TYPE);
				$query->setLimit($limit);
				$query->addKey('sessionKey', $sessionKey);
				if($options->objectKey)
					$query->addKey('objectKey', $options->objectKey);
				if($options->startKeyId)
					$query->setStartKeyDocId($options->startKeyId);
				if($options->endKeyId)
					$query->setEndKeyDocId($options->endKeyId);

				$results->recalculated = 0;
				$list = $cacheStore->query($query);
				while($list->getCount())
				{
					foreach($list->getObjects() as $cache)
					{
						/* @var $cache kCouchbaseCacheListItem */
						KalturaLog::debug("Cache object [" . print_r($cache, true) . "]");
						self::recalculateCache($cache);
						$results->lastKeyId = $cache->getId();
						$results->recalculated++;
					}
					
					if($options->limit && $results->recalculated >= $options->limit)
						break;
						
					$list = $cacheStore->query($query);
				}
				if($results->recalculated)
					break;
			}
		}
		return $results;
	}
}