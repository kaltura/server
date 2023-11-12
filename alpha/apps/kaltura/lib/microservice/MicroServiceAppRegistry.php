<?php
/**
 * App Registry Micro Service
 * This represents the 'app-registry' service under 'plat-app-registry' repo
 */
class MicroServiceAppRegistry extends MicroServiceBaseService
{
	const APP_GUID_CACHE_KEY_PREFIX = 'appGuid_';
	const APP_GUID_CACHE_TTL = 86400; // cached for 1 day
	
	public function __construct()
	{
		parent::__construct(MicroServiceHost::APP_REGISTRY, MicroServiceService::APP_REGISTRY);
	}
	
	public function get($partnerId, $appGuid)
	{
		return $this->serve($partnerId,'get', array('id' => $appGuid));
	}
	
	public function list($partnerId, $filter, $pager = array())
	{
		return $this->serve($partnerId,'list', array('filter' => $filter, 'pager' => $pager));
	}
	
	public static function getExistingAppGuid($partnerId, $appGuid)
	{
		$appGuidExists = MicroServiceBaseService::getFromCache(self::APP_GUID_CACHE_KEY_PREFIX . $appGuid);
		if ($appGuidExists)
		{
			return $appGuid;
		}
		
		$appRegistryClient = new MicroServiceAppRegistry();
		$appRegistry = $appRegistryClient->get($partnerId, $appGuid);
		
		if (isset($appRegistry->code) && $appRegistry->code == 'OBJECT_NOT_FOUND')
		{
			return false;
		}
		
		if (!isset($appRegistry->id) || !kString::isValidMongoId($appRegistry->id))
		{
			return false;
		}
		
		MicroServiceBaseService::addToCache(self::APP_GUID_CACHE_KEY_PREFIX . $appGuid, true, self::APP_GUID_CACHE_TTL);
		return $appRegistry->id;
	}
}
