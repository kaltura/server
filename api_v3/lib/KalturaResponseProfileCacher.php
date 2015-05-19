<?php
class KalturaResponseProfileCacher extends kResponseProfileCacher
{
	/**
	 * @var IBaseObject
	 */
	private static $cachedObject = null;
	
	/**
	 * @var KalturaDetachedResponseProfile
	 */
	private static $responseProfile = null;
	
	private static function getObjectSpecificCacheValue(KalturaObject $apiObject, IBaseObject $object, KalturaDetachedResponseProfile $responseProfile)
	{
		return array(
			'type' => 'primaryObject',
			'objectKey' => self::getObjectKey($object),
			'sessionKey' => self::getSessionKey(),
			'objectType' => get_class($object),
			'objectPeer' => get_class($object->getPeer()),
			'objectId' => $object->getPrimaryKey(),
			'partnerId' => $object->getPartnerId(),
			'responseProfileKey' => $responseProfile->getKey(),
			'apiObject' => self::toArray($apiObject)
		);
	}
	
	private static function getObjectSpecificCacheKey(IBaseObject $object, KalturaDetachedResponseProfile $responseProfile)
	{
		$userRoles = kPermissionManager::getCurrentRoleIds();
		sort($userRoles);
		
		$objectType = get_class($object);
		$objectId = $object->getPrimaryKey();
		$partnerId = $object->getPartnerId();
		$profileKey = $responseProfile->getKey();
		$protocol = infraRequestUtils::getProtocol();
		$ksType = kCurrentContext::getCurrentSessionType();
		$userRoles = implode('_', $userRoles);
		
		return "rp{$profileKey}_p{$partnerId}_o{$objectType}_i{$objectId}_h{$protocol}_k{$ksType}_u{$userRoles}";
	}
	
	private static function getObjectTypeCacheValue(IBaseObject $object)
	{
		$userRoles = kPermissionManager::getCurrentRoleIds();
		sort($userRoles);
		
		return array(
			'type' => 'relatedObject',
			'triggerKey' => self::getTriggerKey($object),
			'objectType' => get_class(self::$cachedObject),
			'objectPeer' => get_class(self::$cachedObject->getPeer()),
			'triggerObjectType' => get_class($object),
			'partnerId' => self::$cachedObject->getPartnerId(),
			'responseProfileKey' => self::$responseProfile->getKey(),
			'protocol' => infraRequestUtils::getProtocol(),
			'ksType' => kCurrentContext::getCurrentSessionType(),
			'userRole' => implode('_', $userRoles)
		);
	}
	
	private static function getObjectTypeCacheKey(IBaseObject $object)
	{
		$userRoles = kPermissionManager::getCurrentRoleIds();
		sort($userRoles);
		
		$objectType = get_class(self::$cachedObject);
		$partnerId = self::$cachedObject->getPartnerId();
		$profileKey = self::$responseProfile->getKey();
		$protocol = infraRequestUtils::getProtocol();
		$ksType = kCurrentContext::getCurrentSessionType();
		$userRoles = implode('_', $userRoles);
		
		return "rp{$profileKey}_p{$partnerId}_o{$objectType}_h{$protocol}_k{$ksType}_u{$userRoles}";
	}
	
	private static function getResponseProfileCacheKey($responseProfileKey = null, $partnerId = null)
	{
		if(is_null($responseProfileKey))
			$responseProfileKey = self::$responseProfile->getKey();
			
		if(is_null($partnerId))
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
			$key = self::getObjectSpecificCacheKey(self::$cachedObject, self::$responseProfile);
			$value = self::get($key);
			if($value)
			{
				$apiObject = self::toApiObject($value->apiObject);
				if($apiObject instanceof KalturaObject)
					return $apiObject->relatedObjects;
			}
		}
		
		if($setResponseProfile)
			self::set($responseProfileCacheKey, self::toArray($responseProfile));
	}
	
	public static function stop(IBaseObject $object, KalturaObject $apiObject)
	{
		if($object !== self::$cachedObject)
			return;
			
		KalturaLog::debug("Stop " . get_class($apiObject) . " [" . print_r($apiObject, true) . "]");
		
		$key = self::getObjectSpecificCacheKey(self::$cachedObject, self::$responseProfile);
		$value = self::getObjectSpecificCacheValue($apiObject, self::$cachedObject, self::$responseProfile);
		
		self::set($key, $value);
		
		self::$cachedObject = null;
		self::$responseProfile = null;
	}
	
	/**
	 * @param array $array
	 * @return KalturaObject
	 */
	protected static function toApiObject(stdClass $jsonObject)
	{
		if(!isset($jsonObject->objectType) || !class_exists($jsonObject->objectType))
			return null;
			
		$objectType = $jsonObject->objectType;
		$object = new $objectType();
		/* @var $object KalturaObject */
		
		$jsonAttributes = get_object_vars($jsonObject);
		foreach($jsonAttributes as $key => $value)
		{
			if(is_object($value) && isset($value->objectType))
			{
				$object->$key = self::toKalturaObject($value);
			}
			else
			{
				$object->$key = $value;
			}
		}
		
		return $object;
	}
	
	/**
	 * @param KalturaObject $object
	 * @return array
	 */
	protected static function toArray($object)
	{
		if(!is_array($object) && !is_object($object))
			return $object;
		
		$array = (array) $object;
		if(is_object($object) && $object instanceof KalturaObject)
		{
			if($object instanceof KalturaTypedArray)
			{
				return self::toArray($object->toArray());
			}
			$array['objectType'] = get_class($object);
		}
		
		foreach($array as $key => $value)
		{
			if(is_null($value))
			{
				unset($array[$key]);
			}
			else 
			{
				$array[$key] = self::toArray($value);
			}
		}
		
		return $array;
	}
	
	protected static function recalculateCache(kCouchbaseCacheListItem $cache, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$data = $cache->getData();
		
		if(!$responseProfile)
		{
			$responseProfileCacheKey = self::getResponseProfileCacheKey($data->responseProfileKey, $data->partnerId);
			$value = self::get($responseProfileCacheKey);
			$responseProfile = self::toApiObject($value);
			if(!$responseProfile)
			{
				KalturaLog::err("Response-Profile key [$responseProfileCacheKey] not found in cache");
				self::delete($cache->getId());
			}
		}
		
		$peer = $data->objectPeer;
		$object = $peer::retrieveByPK($data->objectId);
		/* @var $object IBaseObject */
		
		$apiObject = self::toApiObject($data->apiObject);
		$apiObject->fromObject($object, $responseProfile);
		
		$key = self::getObjectSpecificCacheKey($object, $responseProfile);
		$value = self::getObjectSpecificCacheValue($apiObject, $object, $responseProfile);
		
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
		
		/* @var $object IBaseObject */
		$responseProfile = null;
		$cacheStores = self::getStores();
		foreach ($cacheStores as $cacheStore)
		{
			if($cacheStore instanceof kCouchbaseCacheWrapper)
			{
				$query = $cacheStore->getNewQuery(kCouchbaseCacheQuery::VIEW_RESPONSE_PROFILE_SESSION_TYPE);
				$query->addKey('sessionKey', $sessionKey);
				if($options->objectKey)
					$query->addKey('objectKey', $options->objectKey);
				if($options->startKeyId)
					$query->setStartKeyDocId($options->startKeyId);
				$query->setLimit(min(100, $options->limit));

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
					
					if($results->recalculated >= $options->limit)
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