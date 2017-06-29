<?php

class phpCompatibility
{
	public static function apc_exists()
	{
		if(function_exists('apc_fetch') || function_exists('apcu_fetch'))
			return true;
		
		return false;
	}
	
	public static function apcFetch($key)
	{
		if(function_exists('apc_fetch'))
			return apc_fetch($key);
		
		if(function_exists('apcu_fetch'))
			return apcu_fetch($key);
	}
	
	public static function apcStore($key)
	{
		if(function_exists('apc_store'))
			return apc_fetch($key);
		
		if(function_exists('apcu_store'))
			return apcu_store($key);
	}
	
	public static function clearCache($key)
	{
		if(function_exists('apc_clear_cache'))
			return apc_clear_cache($key);
		
		if(function_exists('apcu_store'))
			return apcu_store($key);
	}
}