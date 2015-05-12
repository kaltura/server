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
		$userRole = implode('_', kPermissionManager::getCurrentRoleIds());
		
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
}