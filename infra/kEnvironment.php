<?php

/**
 * Manages Kaltura environment relative configurations
 * @package infra
 * @subpackage Configuration
 */
class kEnvironment
{
	const APC_CACHE_MAP = 'kConf';
	
	protected static $map = null;
	
	protected static function init()
	{
		if (self::$map) 
			return;
			
		$appDir = realpath(__DIR__ . '/..');	
		$cacheDir = "$appDir/cache";
		
		self::$map = array(
			'cache_root_path' =>  "$cacheDir/",
			'general_cache_dir' => "$cacheDir/general/",
			'response_cache_dir' => "$cacheDir/response/",
			'syndication_core_xsd_path' => "$appDir/alpha/config/syndication.core.xsd",
		);
	}
		
	public static function getConfigDir()
	{
		return realpath(__DIR__ . '/../configurations');
	}
		
	public static function get($paramName)
	{
		self::init();
		if(isset(self::$map[$paramName]))
			return self::$map[$paramName];
		
		throw new Exception("Cannot find [$paramName] in config"); 
	}
}

