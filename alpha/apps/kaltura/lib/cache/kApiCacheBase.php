<?php

/**
 * @package server-infra
 * @subpackage cache
 */
class kApiCacheBase
{
	// extra cache fields
	const ECF_REFERRER = 'referrer';
	const ECF_USER_AGENT = 'userAgent';
	const ECF_COUNTRY = 'country';
	const ECF_IP = 'ip';
	const ECF_COORDINATES = 'coordinates';
	const ECF_ANONYMOUS_IP = 'anonymousIp';
	
	// extra cache fields data
	const ECFD_IP_HTTP_HEADER = 'httpHeader';
	const ECFD_IP_ACCEPT_INTERNAL_IPS = 'acceptInternalIps';
	const ECFD_GEO_CODER_TYPE = 'geoCoderType';
	
	// extra cache fields conditions
	// 	the conditions will be applied on the extra fields when generating the cache key
	//	for example, when using country restriction of US allowed, we can take country==US
	//	in the cache key instead of taking the whole country (2 possible cache key values for
	//	the entry, instead of 200)
	const COND_NONE = '';
	const COND_MATCH = 'match';					// used by kCountryCondition
	const COND_MATCH_ALL = 'matchAll';
	const COND_REGEX = 'regex';					// used by kUserAgentCondition
	const COND_SITE_MATCH = 'siteMatch';		// used by kSiteCondition
	const COND_IP_RANGE = 'ipRange';			// used by kIpAddressCondition
	const COND_GEO_DISTANCE = 'geoDistance';	// used by kGeoDistanceCondition
	
	// cache statuses
	const CACHE_STATUS_ACTIVE = 0;				// cache was not explicitly disabled
	const CACHE_STATUS_ANONYMOUS_ONLY = 1;		// conditional cache was explicitly disabled by calling DisableConditionalCache (e.g. a database query that is not handled by the query cache was issued)
	const CACHE_STATUS_DISABLED = 2;			// cache was explicitly disabled by calling DisableCache (e.g. getContentData for an entry with access control)

	// conditional cache constants
	const MAX_INVALIDATION_KEYS = 250;
	const MAX_SQL_CONDITION_CONNS = 1;			// max number of connections required to verify the SQL conditions
	const MAX_SQL_CONDITION_QUERIES = 4;		// max number of queries per connection required to verify the SQL conditions
	const STALE_RESPONSE_EXPIRY = 5;
		
	// cache instances
	protected $_instanceId = 0;
	protected static $_activeInstances = array();		// active class instances: instanceId => instanceObject
	protected static $_nextInstanceId = 0;

	// status
	protected $_expiry = 0;								// the expiry used for anonymous caching, if 0 ANONYMOUS_CACHE_EXPIRY will be used
	protected $_cacheStatus = self::CACHE_STATUS_DISABLED;	// enabled after the cacher initializes
	protected static $_lockEnabled = false;				// when enabled, if multiple requests are issued for the same cache key at the same time
														// only one will enter, and the rest will wait for it to complete

	// conditional cache fields
	protected $_conditionalCacheExpiry = 0;				// the expiry used for conditional caching, if 0 CONDITIONAL_CACHE_EXPIRY will be used
	protected $_invalidationKeys = array();				// the list of query cache invalidation keys for the current request
	protected $_invalidationTime = 0;					// the last invalidation time of the invalidation keys
	protected $_sqlConditions = array();				// list of sql queries that the api depends on
	protected static $_allowStaleResponse = false;		// when enabled, sql conditions will not be used and database access will not disable the cache
														// instead, the cache expiration time will be shortene
	
	// response post processor fields
	protected static $_responsePostProcessor = null;			// Response post processor object
	protected static $_enableResponsePostProcessor = false;			// Response post processor object

	// extra fields
	protected $_extraFields = array();
	protected static $_usesHttpReferrer = false;	// enabled if the request is dependent on the http referrer field (opposed to an API parameter referrer)
	protected static $_hasExtraFields = false;		// set to true if the response depends on http headers and should not return caching headers to the user / cdn

	// debug
	protected static $_debugMode = false;
	
	protected function __construct()
	{
		// Uncomment to support debug mode
		//if (self::$_nextInstanceId == 0)
		//	self::$_debugMode = isset($_REQUEST['__debugcache']);
		
		$this->_instanceId = self::$_nextInstanceId;
		self::$_nextInstanceId++;

		if (!$this->init())
		{
			if (self::$_debugMode)
				$this->debugLog('kApiCacheBase::init returned false');
			self::disableCache();
			return;
		}

		if (self::$_debugMode)
			$this->debugLog('enabling cache');
		$this->enableCache();
	}

	protected function init()			// overridable
	{
		return true;
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
			if (self::$_debugMode)
				$curInstance->debugLog('disableCache called');
			
			$curInstance->_cacheStatus = self::CACHE_STATUS_DISABLED;
		}
		self::$_activeInstances = array();
	}

	public static function disableConditionalCache()
	{
		if (self::$_allowStaleResponse)
		{
			self::setConditionalCacheExpiry(self::STALE_RESPONSE_EXPIRY);
			return;
		}

		foreach (self::$_activeInstances as $curInstance)
		{
			// no need to check for CACHE_STATUS_DISABLED, since the instances are removed from the list when they get this status
			$curInstance->disableConditionalCacheInternal();
		}
	}
	
	protected function disableConditionalCacheInternal()
	{
		if (self::$_debugMode && $this->_cacheStatus != self::CACHE_STATUS_ANONYMOUS_ONLY)
			$this->debugLog('disableConditionalCache called');
		
		$this->_cacheStatus = self::CACHE_STATUS_ANONYMOUS_ONLY;
		$this->_invalidationKeys = array();
		$this->_sqlConditions = array();	
	}

	protected function removeFromActiveList()
	{
		unset(self::$_activeInstances[$this->_instanceId]);
	}

	public static function isCacheEnabled()
	{
		return count(self::$_activeInstances);
	}

	public static function enableLock()
	{
		self::$_lockEnabled = true;
	}
	
	public static function setResponsePostProcessor($postProcessor)
	{
		self::$_responsePostProcessor = $postProcessor;
	}
	
	public static function getResponsePostProcessor()
	{
		return self::$_responsePostProcessor;
	}
	
	public static function enableResponsePostProcessor()
	{
		self::$_enableResponsePostProcessor = true;
	}
	
	public static function getEnableResponsePostProcessor()
	{
		return self::$_enableResponsePostProcessor;
	}

	// expiry control functions
	public static function setExpiry($expiry)
	{		
		foreach (self::$_activeInstances as $curInstance)
		{			
			if ($curInstance->_expiry && $curInstance->_expiry < $expiry)
				continue;
			if (self::$_debugMode)
				$curInstance->debugLog("setExpiry called with [$expiry]");
			$curInstance->_expiry = $expiry;
		}
	}

	public static function setConditionalCacheExpiry($expiry)
	{		
		foreach (self::$_activeInstances as $curInstance)
		{
			if ($curInstance->_conditionalCacheExpiry && $curInstance->_conditionalCacheExpiry < $expiry)
				continue;
			if (self::$_debugMode)
				$curInstance->debugLog("setConditionalCacheExpiry called with [$expiry]");
			$curInstance->_conditionalCacheExpiry = $expiry;
		}
	}

	// conditional cache
	public static function addInvalidationKeys($invalidationKeys, $invalidationTime)
	{
		foreach (self::$_activeInstances as $curInstance)
		{
			if ($curInstance->_cacheStatus == self::CACHE_STATUS_ANONYMOUS_ONLY)
				continue;
			
			foreach ($invalidationKeys as $invalidationKey)
			{
				$curInstance->_invalidationKeys[$invalidationKey] = true;
			}
			$curInstance->_invalidationTime = max($curInstance->_invalidationTime, $invalidationTime);
			
			if (count($curInstance->_invalidationKeys) > self::MAX_INVALIDATION_KEYS)
			{
				$curInstance->disableConditionalCacheInternal();
			}
		}
	}
	
	public static function addSqlQueryCondition($configKey, $sql, $fetchStyle, $columnIndex, $filter, $result)
	{
		if (self::$_allowStaleResponse)
		{
			self::setConditionalCacheExpiry(self::STALE_RESPONSE_EXPIRY);
			return;
		}

		foreach (self::$_activeInstances as $curInstance)
		{
			if ($curInstance->_cacheStatus == self::CACHE_STATUS_ANONYMOUS_ONLY)
				continue;
			
			if (!isset($curInstance->_sqlConditions[$configKey]))
			{
				if (count($curInstance->_sqlConditions) >= self::MAX_SQL_CONDITION_CONNS)
				{
					$curInstance->disableConditionalCacheInternal();
					continue;
				}

				$curInstance->_sqlConditions[$configKey] = array();
			}

			if (count($curInstance->_sqlConditions[$configKey]) >= self::MAX_SQL_CONDITION_QUERIES)
			{
				$curInstance->disableConditionalCacheInternal();
				continue;
			}
					
			$curInstance->_sqlConditions[$configKey][] = array(
					'sql' => $sql, 
					'fetchStyle' => $fetchStyle, 
					'columnIndex' => $columnIndex, 
					'filter' => $filter, 
					'expectedResult' => $result);
		}
	}

	protected static function addSqlQueryConditions($sqlConditions)
	{
		foreach ($sqlConditions as $configKey => $queries)
		{
			foreach ($queries as $query)
			{
				self::addSqlQueryCondition(
					$configKey,
					$query['sql'],
					$query['fetchStyle'],
					$query['columnIndex'],
					$query['filter'],
					$query['expectedResult']);
			}
		}
	}

	public static function enableStaleResponse()
	{
		self::$_allowStaleResponse = true;
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

		// the following code is required since there are no active cache instances in thumbnail action
		// and we need _hasExtraFields to be correct
		if ($extraField != self::ECF_REFERRER || self::$_usesHttpReferrer)
			self::$_hasExtraFields = true;
	}

	static public function getHttpReferrer()
	{
		self::$_usesHttpReferrer = true;
		return isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
	}

	protected function addExtraFieldInternal($extraField, $condition, $refValue)
	{
		$extraFieldParams = array($extraField, $condition, $refValue);
		if (in_array($extraFieldParams, $this->_extraFields))
			return;			// already added
		$this->_extraFields[] = $extraFieldParams;
		if ($extraField != self::ECF_REFERRER || self::$_usesHttpReferrer)
			self::$_hasExtraFields = true;

		$this->addExtraFieldsToCacheParams($extraField, $condition, $refValue);
	}
	
	// overridable
	protected function addExtraFieldsToCacheParams($extraField, $condition, $refValue)
	{
	}

	public static function filterQueryResult($input, $filter)
	{
		if (!$filter)
			return $input;
	
		$result = array();
		foreach ($input as $index => $curRow)
		{
			$matchesFilter = true;
			foreach ($filter as $key => $value)
			{
				if (!isset($curRow[$key]) || $curRow[$key] != $value)
				{
					$matchesFilter = false;
					break;
				}
			}
			
			if ($matchesFilter)
			{
				$result[] = $curRow;
			}
		}
		return $result;
	}
	
	/**
	 * @return int
	 */
	public static function getTime()
	{
		self::setConditionalCacheExpiry(600);
		return time();
	}

	protected function debugLog($msg)
	{
		error_log("kApiCache [$this->_instanceId] ".$msg);
	}
}
