<?php

/**
 * @package server-infra
 * @subpackage cache
 */
class kApiCacheWatcher extends kApiCacheBase
{
	public function __construct()
	{
		parent::__construct();
	}

	public function stop()
	{
		$this->removeFromActiveList();
	}
	
	public function apply()
	{
		if ($this->_cacheStatus == self::CACHE_STATUS_DISABLED)
		{
			kApiCache::disableCache();
			return;
		}

		// common cache fields
		foreach ($this->_extraFields as $extraField)
		{
			call_user_func_array(array('kApiCache', 'addExtraField'), $extraField);
		}
		
		// anonymous cache fields
		if ($this->_expiry)
			kApiCache::setExpiry($this->_expiry);
		
		if ($this->_cacheStatus == self::CACHE_STATUS_ANONYMOUS_ONLY)
		{
			kApiCache::disableConditionalCache();
			return;
		}
		
		// conditional cache fields
		if ($this->_conditionalCacheExpiry)
			kApiCache::setConditionalCacheExpiry($this->_conditionalCacheExpiry);
		
		kApiCache::addInvalidationKeys(array_keys($this->_invalidationKeys), $this->_invalidationTime);

		kApiCache::addSqlQueryConditions($this->_sqlConditions);
	}
}
