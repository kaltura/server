<?php

require_once(dirname(__FILE__) . '/../general/infraRequestUtils.class.php');
require_once(dirname(__FILE__) . '/kCacheManager.php');

/**
 * @package infra
 * @subpackage cache
 */
class kApiCache
{
	// extra cache fields
	const ECF_REFERRER = 'referrer';
	const ECF_USER_AGENT = 'userAgent';
	const ECF_COUNTRY = 'country';

	// extra cache fields conditions
	const COND_NONE = '';
	const COND_MATCH = 'match';
	const COND_REGEX = 'regex';
	
	const EXTRA_KEYS_PREFIX = 'extra-keys-';

	// cache statuses
	const CACHE_STATUS_ACTIVE = 0;				// cache was not explicitly disabled
	const CACHE_STATUS_ANONYMOUS_ONLY = 1;		// conditional cache was explicitly disabled by calling DisableConditionalCache (e.g. a database query that is not handled by the query cache was issued)
	const CACHE_STATUS_DISABLED = 2;			// cache was explicitly disabled by calling DisableCache (e.g. getContentData for an entry with access control)
	
	const CONDITIONAL_CACHE_EXPIRY = 86400;		// 1 day, must not be greater than the expiry of the query cache keys
	
	// cache instances
	protected $_instanceId = 0;
	protected static $_activeInstances = array();		// active class instances: instanceId => instanceObject
	protected static $_nextInstanceId = 0;

	// cache key
	protected $_params = array();	
	protected $_cacheKey = "";
	protected $_cacheKeyPrefix = '';	
	protected $_cacheKeyDirty = true;
	protected $_originalCacheKey = null;

	// ks
	protected $_ks = "";
	protected $_ksObj = null;
	protected $_ksPartnerId = null;
	
	// status
	protected $_expiry = 600;
	protected $_cacheStatus = self::CACHE_STATUS_DISABLED;	// enabled after the KalturaResponseCacher initializes
	
	// conditional cache fields
	protected $_conditionalCacheExpiry = 0;				// the expiry used for conditional caching, if 0 CONDITIONAL_CACHE_EXPIRY will be used 
	protected $_invalidationKeys = array();				// the list of query cache invalidation keys for the current request
	protected $_invalidationTime = 0;					// the last invalidation time of the invalidation keys

	// extra fields
	protected $_extraFields = array();
	protected $_referrers = array();
	protected static $_country = null;
	protected static $_hasExtraFields = false;
	
	protected function __construct()
	{
		$this->_instanceId = self::$_nextInstanceId;  
		self::$_nextInstanceId++;
	}

	protected function addKSData($ks)
	{
		$this->_ks = $ks;
		$this->_ksObj = kSessionBase::getKSObject($ks);
		$this->_ksPartnerId = ($this->_ksObj ? $this->_ksObj->partner_id : null);
		$this->_params["___cache___partnerId"] =  $this->_ksPartnerId;
		$this->_params["___cache___ksType"] = 	  ($this->_ksObj ? $this->_ksObj->type		 : null);
		$this->_params["___cache___userId"] =     ($this->_ksObj ? $this->_ksObj->user		 : null);
		$this->_params["___cache___privileges"] = ($this->_ksObj ? $this->_ksObj->privileges : null);
	}
	
	// enable / disable functions
	protected function enableCache()
	{
		self::$_activeInstances[$this->_instanceId] = $this;
		$this->_cacheStatus = self::CACHE_STATUS_ACTIVE;
	}
	
	public static function disableCache()
	{
		foreach (self::$_activeInstances as $curInstance)
		{
			$curInstance->_cacheStatus = self::CACHE_STATUS_DISABLED;
		}
		self::$_activeInstances = array();
	}

	public static function disableConditionalCache()
	{
		foreach (self::$_activeInstances as $curInstance)
		{
			// no need to check for CACHE_STATUS_DISABLED, since the instances are removed from the list when they get this status
			$curInstance->_cacheStatus = self::CACHE_STATUS_ANONYMOUS_ONLY;
		}
	}
	
	protected function removeFromActiveList()
	{
		unset(self::$_activeInstances[$this->_instanceId]);
	}
	
	// expiry control functions
	public static function setExpiry($expiry)
	{
		foreach (self::$_activeInstances as $curInstance)
		{
			if ($curInstance->_expiry && $curInstance->_expiry < $expiry)
				continue;
			$curInstance->_expiry = $expiry;
		}
	}
	
	public static function setConditionalCacheExpiry($expiry)
	{
		foreach (self::$_activeInstances as $curInstance)
		{
			if ($curInstance->_conditionalCacheExpiry && $curInstance->_conditionalCacheExpiry < $expiry)
				continue;
			$curInstance->_conditionalCacheExpiry = $expiry;
		}
	}
	
	// conditional cache
	public static function addInvalidationKeys($invalidationKeys, $invalidationTime)
	{
		foreach (self::$_activeInstances as $curInstance)
		{
			$curInstance->_invalidationKeys = array_merge($curInstance->_invalidationKeys, $invalidationKeys);
			$curInstance->_invalidationTime = max($curInstance->_invalidationTime, $invalidationTime);
		}
	}
	
	// extra fields functions
	static public function hasExtraFields()
	{
		return self::$_hasExtraFields;
	}
	
	static public function addExtraField($extraField, $condition = self::COND_NONE, $refValue = null)
	{
		foreach (self::$_activeInstances as $curInstance)
		{
			$curInstance->addExtraFieldInternal($extraField, $condition, $refValue);
		}
	}

	static protected function getCountry()
	{
		if (is_null(self::$_country))
		{
			require_once(dirname(__FILE__) . '/../../alpha/apps/kaltura/lib/myIPGeocoder.class.php');
			$ipAddress = infraRequestUtils::getRemoteAddress();
			$geoCoder = new myIPGeocoder();
			self::$_country = $geoCoder->getCountry($ipAddress);
		}
		return self::$_country;
	}
	
	protected function getFieldValues($extraField)
	{
		switch ($extraField)
		{
		case self::ECF_REFERRER:
			$values = array();
			foreach ($this->_referrers as $referrer)
			{
				$values[] = infraRequestUtils::parseUrlHost($referrer);
			}
			return $values;

		case self::ECF_USER_AGENT:
			if (isset($_SERVER['HTTP_USER_AGENT']))
				return array(isset($_SERVER['HTTP_USER_AGENT']));
			break;
		
		case self::ECF_COUNTRY:
			return array(self::getCountry());
		}
		
		return array();
	}
	
	protected function applyCondition($fieldValue, $condition, $refValue)
	{
		switch ($condition)
		{			
		case self::COND_MATCH:
			if (!count($refValue))
				return null;
			return in_array($fieldValue, $refValue);
			
		case self::COND_REGEX:
			if (!count($refValue))
				return null;
			foreach($refValue as $curRefValue)
			{
				if ($fieldValue === $curRefValue || 
					preg_match("/$curRefValue/i", $fieldValue))
					return true;
			}
			return false;			
		}
		return $fieldValue;
	}
	
	protected function getConditionKey($condition, $refValue)
	{
		switch ($condition)
		{			
		case self::COND_REGEX:
		case self::COND_MATCH:
			return "_{$condition}_" . implode(',', $refValue);
		}
		return '';
	}
	
	protected function addExtraFieldInternal($extraField, $condition, $refValue)
	{
		$extraFieldParams = array($extraField, $condition, $refValue);
		if (in_array($extraFieldParams, $this->_extraFields))
			return;			// already added
		$this->_extraFields[] = $extraFieldParams;
		self::$_hasExtraFields = true;
		
		foreach ($this->getFieldValues($extraField) as $valueIndex => $fieldValue)
		{
			$conditionResult = $this->applyCondition($fieldValue, $condition, $refValue);
			$key = "___cache___{$extraField}_{$valueIndex}" . $this->getConditionKey($condition, $refValue);
			$this->_params[$key] = $conditionResult;
		}
		
		$this->_cacheKeyDirty = true;
	}

	protected function addExtraFields()
	{
		$extraFieldsCache = kCacheManager::getCache(kCacheManager::APC);
		if (!$extraFieldsCache)
			return;
		
		$extraFields = $extraFieldsCache->get(self::EXTRA_KEYS_PREFIX . $this->_cacheKey);
		if (!$extraFields)
			return;
		
		foreach ($extraFields as $extraFieldParams)
			call_user_func_array(array($this, 'addExtraFieldInternal'), $extraFieldParams);
		
		$this->finalizeCacheKey();
	}
	
	protected function storeExtraFields()
	{
		if (!$this->_cacheKeyDirty)
			return;			// no extra fields were added to the cache

		$extraFieldsCache = kCacheManager::getCache(kCacheManager::APC);
		if (!$extraFieldsCache)
		{
			self::disableCache();
			return;
		}
		
		if ($extraFieldsCache->set(self::EXTRA_KEYS_PREFIX . $this->_originalCacheKey, $this->_extraFields, self::CONDITIONAL_CACHE_EXPIRY) === false)
		{
			self::disableCache();
			return;
		}
		
		$this->finalizeCacheKey();			// update the cache key to include the extra fields
	}
	
	protected function finalizeCacheKey()
	{
		if (!$this->_cacheKeyDirty)
			return;
		$this->_cacheKeyDirty = false;
	
		ksort($this->_params);
		$this->_cacheKey = $this->_cacheKeyPrefix . md5( http_build_query($this->_params) );
		if (is_null($this->_originalCacheKey))
			$this->_originalCacheKey = $this->_cacheKey;
	}

	/**
	 * @return int
	 */
	public static function getTime()
	{
		self::setConditionalCacheExpiry(600);
		return time();
	}
}
