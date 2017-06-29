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
		if (!function_exists('apc_fetch') && !function_exists('apcu_fetch'))
			return false;
		return true;
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::get()
	 */
	protected function doGet($key)
	{
		if(function_exists(apc_fetch))
			return apc_fetch($key);
		
		if(function_exists(apcu_fetch))
			return apcu_fetch($key);
	}
		
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	protected function doSet($key, $var, $expiry = 0)
	{
		if(function_exists(apc_store))
			return apc_store($key);
		
		if(function_exists(apcu_store))
			return apcu_store($key);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::add()
	 */
	protected function doAdd($key, $var, $expiry = 0)
	{
		if(function_exists(apc_add))
			return apc_add($key);
		
		if(function_exists(apcu_add))
			return apcu_add($key);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::multiGet()
	 */
	protected function doMultiGet($keys)
	{
		if(function_exists(apc_fetch))
			return apc_fetch($keys);
		
		if(function_exists(apcu_fetch))
			return apcu_fetch($keys);
	}


	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::delete()
	 */
	protected function doDelete($key)
	{
		if(function_exists(apc_delete))
			return apc_delete($key);
		
		if(function_exists(apcu_delete))
			return apcu_delete($key);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::increment()
	 */
	public function doIncrement($key, $delta = 1)
	{
		if(function_exists(apc_inc))
			return apc_inc($key, $delta);
		
		if(function_exists(apcu_inc))
			return apcu_inc($key, $delta);
		
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::decrement()
	 */
	public function doDecrement($key, $delta = 1)
	{
		if(function_exists(apc_dec))
			return apc_dec($key, $delta);
		
		if(function_exists(apcu_dec))
			return apcu_dec($key, $delta);
	}
}
