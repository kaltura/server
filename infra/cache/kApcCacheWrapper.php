<?php

require_once(dirname(__FILE__) . '/kBaseCacheWrapper.php');

/**
 * @package infra
 * @subpackage cache
 */
class kApcCacheWrapper extends kBaseCacheWrapper
{
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::init()
	 */
	protected function doInit($config)
	{
		if (!function_exists('apc_fetch'))
			return false;
		return true;
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::get()
	 */
	protected function doGet($key)
	{
		return apc_fetch($key);
	}
		
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	protected function doSet($key, $var, $expiry = 0)
	{
		return apc_store($key, $var, $expiry);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::add()
	 */
	protected function doAdd($key, $var, $expiry = 0)
	{
		return apc_add($key, $var, $expiry);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::multiGet()
	 */
	protected function doMultiGet($keys)
	{
		return apc_fetch($keys);
	}


	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::delete()
	 */
	protected function doDelete($key)
	{
		return apc_delete($key);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::increment()
	 */
	public function doIncrement($key, $delta = 1)
	{
		return apc_inc($key, $delta);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::decrement()
	 */
	public function doDecrement($key, $delta = 1)
	{
		return apc_dec($key, $delta);
	}
}
