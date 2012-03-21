<?php
setlocale(LC_ALL, 'en_US.UTF-8');

/**
 * Manages all Kaltura configurations
 * @package infra
 * @subpackage Configuration
 */
class kConf
{
	const APC_CACHE_MAP = 'kConf';
	
	protected static $map = null;
	
	private static function init()
	{
		if (self::$map) 
			return;
		
		$configDir = realpath(dirname(__file__) . '/../configurations');
		
		self::$map = array();
		if(function_exists('apc_exists') && apc_exists(self::APC_CACHE_MAP))
		{
			// existence of base.reload file means that the kConf should be reloaded from the file
			if(file_exists("$configDir/base.reload"))
			{
				if(apc_delete(self::APC_CACHE_MAP))
				{
					@unlink("$configDir/base.reload");
					error_log("Base configuration reloaded");
				}
				else 
				{
					error_log("Failed to reload configuration, APC cache not deleted");
				}
			}
			else
			{
				self::$map = apc_fetch(self::APC_CACHE_MAP);
				if(self::$map)
					return;
			}
		}
		
		if(!file_exists("$configDir/base.ini"))
		{
			error_log("Base configuration not found [$configDir/base.ini]");
			die("Base configuration not found [$configDir/base.ini]");
		}
		$config = parse_ini_file("$configDir/base.ini", true);
	
		if(!file_exists("$configDir/local.ini"))
		{
			error_log("Local configuration not found [$configDir/local.ini]");
			die("Local configuration not found [$configDir/local.ini]");
		}		
		$localConfig = parse_ini_file("$configDir/local.ini", true);
		$config = array_merge_recursive($config, $localConfig);
		
		if(isset($_SERVER["HOSTNAME"]))
		{
			$hostName = $_SERVER["HOSTNAME"];
			$localConfigFile = "$hostName.ini";
			
			$configPath = "$configDir/hosts";
			$configDir = dir($configPath);
			while (false !== ($iniFile = $configDir->read())) 
			{
				$iniFileMatch = str_replace('#', '*', $iniFile);
				if(!fnmatch($iniFileMatch, $localConfigFile))
					continue;
					
				$localConfig = parse_ini_file("$configPath/$iniFile", true);
				$config = array_merge_recursive($config, $localConfig);
			}
			$configDir->close();
		}
			
		self::$map = $config;
		
		if(function_exists('apc_store'))
			apc_store(self::APC_CACHE_MAP, self::$map);
	}
	
	public static function getAll()
	{
		self::init();
		return self::$map;
	}
		
	public static function getMap($mapName)
	{
		self::init();
		
		if($mapName == 'local')
			return self::$map;
		
		if(isset(self::$map[$mapName]))
			return self::$map[$mapName];
		
		$configDir = realpath(dirname(__file__) . '/../configurations');
		if(!file_exists("$configDir/$mapName.ini"))
			throw new Exception("Cannot find map [$mapName] in config folder");
		
		$config = new Zend_Config_Ini("$configDir/$mapName.ini");
		self::$map[$mapName] = $config->toArray();
	
		if(isset($_SERVER["HOSTNAME"]))
		{
			$hostName = $_SERVER["HOSTNAME"];
			$localConfigFile = "$hostName.ini";
			
			$configPath = "$configDir/hosts/$mapName";
			if(file_exists($configPath) && is_dir($configPath)){			
				$configDir = dir($configPath);
				while (false !== ($iniFile = $configDir->read())) 
				{
					$iniFileMatch = str_replace('#', '*', $iniFile);
					if(!fnmatch($iniFileMatch, $localConfigFile))
						continue;
						
					$config = new Zend_Config_Ini("$configPath/$iniFile");
					self::$map[$mapName] = array_merge_recursive(self::$map[$mapName], $config->toArray());
				}
				$configDir->close();
			}
		}
		
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

