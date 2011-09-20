<?php
setlocale(LC_ALL, 'en_US.UTF-8');

class kConf
{
	const APC_CACHE_MAP = 'kConf';
	
	protected static $map = null;
	
	private static function init()
	{
		if (self::$map) 
			return;
		
		self::$map = array();
		if(function_exists('apc_fetch'))
		{
			self::$map = apc_fetch(self::APC_CACHE_MAP);
			if(self::$map)
				return;
		}
		
		$configDir = realpath(dirname(__file__) . '/../configurations');
		$baseConfig = parse_ini_file("$configDir/base.ini", true);
		$localConfig = parse_ini_file("$configDir/local.ini", true);
		$config = array_merge_recursive($baseConfig, $localConfig);
		
		self::$map = $config;
		
		if(function_exists('apc_store'))
			apc_store(self::APC_CACHE_MAP, self::$map);
	}
	
	/**
	* @param Iterator $srcConfig
	* @param Iterator $newConfig
	* @param bool $valuesOnly
	* @return Iterator
	*/
	public static function mergeConfigItem(Iterator $srcConfig, Iterator $newConfig, $valuesOnly = false)
	{
		$returnedConfig = $srcConfig;
		
		if($valuesOnly)
		{
			foreach($srcConfig as $key => $value)
			{
				if(!$newConfig->$key) // nothing to append
					continue;
				elseif($value instanceof Iterator)
					$returnedConfig->$key = self::mergeConfigItem($srcConfig->$key, $newConfig->$key, $valuesOnly);
				else
					$returnedConfig->$key = $srcConfig->$key . ',' . $newConfig->$key;
			}
		}
		else
		{
			foreach($newConfig as $key => $value)
			{
				if(!$srcConfig->$key)
					$returnedConfig->$key = $newConfig->$key;
				elseif($value instanceof Iterator)
					$returnedConfig->$key = self::mergeConfigItem($srcConfig->$key, $newConfig->$key, $valuesOnly);
				else
					$returnedConfig->$key = $srcConfig->$key . ',' . $newConfig->$key;
			}
		}
		
		return $returnedConfig;
	}
		
	public static function getMap($mapName)
	{
		if(isset(self::$map[$mapName]))
			return self::$map[$mapName];
		
		$configDir = realpath(dirname(__file__) . '/../configurations');
		$config = new Zend_Config_Ini("$configDir/$mapName.ini");
		self::$map[$mapName] = $config->toArray();
		
		if(function_exists('apc_store'))
			apc_store(self::APC_CACHE_MAP, self::$map);
		
		return self::$map[$mapName];
	}
		
	public static function get($paramName)
	{
		self::init();
		if(isset(self::$map[$paramName]))
			return self::$map[$paramName];
		
		throw new Exception("Cannot find [$paramName] in config"); 
	}
	
	public static function hasParam($paramName)
	{
		self::init();
		return isset(self::$map[$paramName]);
	}

	public static function getDB()
	{
		return self::getMap('db');
	}
}

