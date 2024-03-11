<?php

/**
 * @package infra
 * @subpackage cache
 */
class kApcWrapper
{
	public static function apcFetch($key, &$success = null)
	{
		if (function_exists('apc_fetch'))
			return apc_fetch($key, $success);
		if (function_exists('apcu_fetch'))
			return apcu_fetch($key, $sy
			);
		
		return false;
	}
	
	public static function apcStore($key, $var, $expiry = 0)
	{
		if (function_exists('apc_store'))
			return apc_store($key, $var, $expiry);
		if (function_exists('apcu_store'))
			return apcu_store($key, $var, $expiry);
		
		return false;
	}
	
	public static function apcAdd($key, $var, $expiry = 0)
	{
		if (function_exists('apc_add'))
			return apc_add($key, $var, $expiry);
		if (function_exists('apcu_add'))
			return apcu_add($key, $var, $expiry);
		
		return false;
	}
	
	public static function apcMultiGet($keys)
	{
		if (function_exists('apc_fetch'))
			return apc_fetch($keys);
		if (function_exists('apcu_fetch'))
			return apcu_fetch($keys);
		
		return false;
	}
	
	public static function apcDelete($key)
	{
		if (function_exists('apc_delete'))
			return apc_delete($key);
		if (function_exists('apcu_delete'))
			return apcu_delete($keys);
		
		return false;
	}
	
	public static function apcInc($key, $delta = 1)
	{
		if (function_exists('apc_inc'))
			return apc_inc($key, $delta);
		if (function_exists('apcu_inc'))
			return apcu_inc($key, $delta);
		
		return false;
	}
	
	public static function apcDec($key, $delta = 1)
	{
		if (function_exists('apc_dec'))
			return apc_dec($key, $delta);
		if (function_exists('apcu_dec'))
			return apcu_dec($key, $delta);
		
		return false;
	}
	
	public static function apcClearCache($cache_type = '')
	{
		if (function_exists('apc_clear_cache'))
			return apc_clear_cache($cache_type);
		if (function_exists('apcu_clear_cache'))
			return apcu_clear_cache();
		
		return false;
	}
	
	public static function functionExists($funcName)
	{
		return function_exists("apc_".$funcName) || function_exists("apcu_".$funcName);
	}
	
	public static function apcEnabled()
	{
		$apcFetchEnabled = function_exists('apc_fetch') || function_exists('apcu_fetch');
		$apcStoreEnabled = function_exists('apc_store') || function_exists('apcu_store');
		
		return $apcFetchEnabled && $apcStoreEnabled;
	}
}
