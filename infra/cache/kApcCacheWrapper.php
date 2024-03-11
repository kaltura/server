<?php

require_once(dirname(__FILE__) . '/kApcWrapper.php');
require_once(dirname(__FILE__) . '/kInfraBaseCacheWrapper.php');

/**
 * @package infra
 * @subpackage cache
 */
class kApcCacheWrapper extends kInfraBaseCacheWrapper
{
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::init()
	 */
	protected function doInit($config)
	{
		return kApcWrapper::apcEnabled();
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::get()
	 */
	protected function doGet($key)
	{
		return kApcWrapper::apcFetch($key);
	}
		
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	protected function doSet($key, $var, $expiry = 0)
	{
		return kApcWrapper::apcStore($key, $var, $expiry);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::add()
	 */
	protected function doAdd($key, $var, $expiry = 0)
	{
		return kApcWrapper::apcAdd($key, $var, $expiry);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::multiGet()
	 */
	protected function doMultiGet($keys)
	{
		return kApcWrapper::apcMultiGet($keys);
	}


	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::delete()
	 */
	protected function doDelete($key)
	{
		return kApcWrapper::apcDelete($key);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::increment()
	 */
	public function doIncrement($key, $delta = 1)
	{
		return kApcWrapper::apcInc($key, $delta);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::decrement()
	 */
	public function doDecrement($key, $delta = 1)
	{
		return kApcWrapper::apcDec($key, $delta);
	}
}
