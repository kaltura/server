<?php

require_once(dirname(__FILE__) . '/kBaseCacheWrapper.php');

/**
 * @package infra
 * @subpackage cache
 */
class kApcCacheWrapper extends kBaseCacheWrapper
{
	/**
	 * @return bool false on error
	 */
	public function init()
	{
		if (!function_exists('apc_fetch'))
			return false;
		return true;
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::get()
	 */
	public function get($key, $defaultExpiry = 0)
	{
		return apc_fetch($key);
	}
		
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	public function set($key, $var, $expiry = 0, $defaultExpiry = 0)
	{
		apc_store($key, $var, $expiry);
	}
}
