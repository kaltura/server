<?php
/**
 * @package server-infra
 * @subpackage config
 */
setlocale(LC_ALL, 'en_US.UTF-8');
libxml_disable_entity_loader(true);

$include_path = realpath(__DIR__ . '/../../vendor/ZendFramework/library') . PATH_SEPARATOR . get_include_path();
set_include_path($include_path);

require_once __DIR__ . '/../../infra/kEnvironment.php';

/**
 * Manages all Kaltura configurations
 * @package server-infra
 * @subpackage Configuration
 */
class kConf extends kEnvironment
{
	const APC_CACHE_MAP = 'kConf-';
	
	const FULL_MAP_KEY = '__full';
	
	protected static $cacheKey = null;
	
	protected static $cacheVersion = null;
	
	protected static $map = array();
	
	protected static function init()
	{
		if (self::$cacheVersion) 
			return;
						
		parent::init();
		
		// check for the reload file
		$cacheDir = self::$envMap['cache_root_path'];
		$reloadFileExists = file_exists("$cacheDir/base.reload");

		// fetch the cache version from APC
		$fileHash = md5(realpath(__file__));
		$cacheVersionKey = self::APC_CACHE_MAP . $fileHash;
		
		if (!$reloadFileExists && function_exists('apc_fetch'))
		{
			self::$cacheVersion = apc_fetch($cacheVersionKey);
			if(self::$cacheVersion)
			{
				self::$cacheKey = 'kConf-'.self::$cacheVersion;
				return;
			}
		}
		
		// no cache version in APC - create a new one
		self::$cacheVersion = substr(time(), -6) . substr($fileHash, 0, 4);
		self::$cacheKey = 'kConf-'.self::$cacheVersion;
		
		// save the cache version
		if(function_exists('apc_store') && PHP_SAPI != 'cli')
		{
			$res = apc_store($cacheVersionKey, self::$cacheVersion);
			if($reloadFileExists && $res)
			{
				$deleted = @unlink("$cacheDir/base.reload");
				error_log("Base configuration reloaded");
				if(!$deleted)
					error_log("Failed to delete base.reload file");
			}
		}
	}
	
	public static function hasMap($mapName)
	{
		self::init();
		
		if(isset(self::$map[$mapName]))
			return true;
		
		$configDir = self::getConfigDir();
		return file_exists("$configDir/$mapName.ini");
	}
		
	public static function getMap($mapName)
	{
		self::init();

		if($mapName===NULL)
		{
			return;
		}

		// check for a previously loaded map
		if(isset(self::$map[$mapName . self::FULL_MAP_KEY]))
			return self::$map[$mapName];
		
		// try to fetch from APC
		$cacheKey = self::$cacheKey . '-' . $mapName;
		if(function_exists('apc_fetch'))
		{
			$result = apc_fetch($cacheKey);
			if ($result !== false)
			{
				self::$map[$mapName] = $result;
				self::$map[$mapName . self::FULL_MAP_KEY] = true;
				return $result;
			}
		}
		
		// get the list of ini files
		$configDir = self::getConfigDir();
		$iniFiles = array();
		if ($mapName == 'local')
			$iniFiles[] = "$configDir/base.ini";
		$iniFiles[] = "$configDir/$mapName.ini";
		
		$hostname = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname());
		if($hostname)
		{			
			$configPath = "$configDir/hosts";
			if ($mapName != 'local')
				$configPath .= "/$mapName";
			
			if(is_dir($configPath))
			{
				$localConfigFile = "$hostname.ini";
				
				$configDir = dir($configPath);
				while (false !== ($iniFile = $configDir->read()))
				{
					$iniFileMatch = str_replace('#', '*', $iniFile);
					if(!fnmatch($iniFileMatch, $localConfigFile))
						continue;
						
					$iniFiles[] = "$configPath/$iniFile";
				}
				$configDir->close();
			}
		}
			
		// load zend config classes
		if(!class_exists('Zend_Config_Ini'))
		{
			require_once 'Zend/Config/Exception.php';
			require_once 'Zend/Config/Ini.php';
		}
		
		// load and merge the configurations
		$result = array();
		if ($mapName == 'local')
			$result = self::$envMap;
		foreach ($iniFiles as $iniFile)
		{
			if(!file_exists($iniFile))
				throw new Exception("Cannot find configuration file [$iniFile]");
					
			$config = new Zend_Config_Ini($iniFile);
			if (!$result)
				$result = $config->toArray();
			else
				$result = self::mergeConfigItem($result, $config->toArray());
		}
			
		// cache the result
		self::$map[$mapName] = $result;
		self::$map[$mapName . self::FULL_MAP_KEY] = true;
		
		if(function_exists('apc_store'))
		{
			apc_store($cacheKey, $result);
		}
		
		return $result;
	}
	
	protected static function getInternal($paramName, $mapName)
	{
		self::init();
		
		// check for a previously loaded var
		if(array_key_exists($mapName, self::$map) && 
			array_key_exists($paramName, self::$map[$mapName]))		// not using isset, since we want to return if it's null
			return self::$map[$mapName][$paramName];
		
		// try to fetch from APC
		$cacheKey = self::$cacheKey . '-' . $mapName . '-' . $paramName; 
		if (function_exists('apc_fetch'))
		{
			$result = apc_fetch($cacheKey);
			if ($result !== false)
			{
				self::$map[$mapName][$paramName] = $result;
				return $result;
			}
		}
		
		// load all map fields since we don't know whether:
		// 1. the key does not exist in the config
		// 2. the key was evicted from APC
		// 3. the map configuration was not loaded since the last reload 
		self::getMap($mapName);

		// the parameter is still not there, mark it with null so that we don't have to load entire map next time
		if (!array_key_exists($paramName, self::$map[$mapName]))
			self::$map[$mapName][$paramName] = null;
		
		if(function_exists('apc_store'))
		{
			apc_store($cacheKey, self::$map[$mapName][$paramName]);
		}
		return self::$map[$mapName][$paramName];
	}
	
	public static function hasParam($paramName, $mapName = 'local')
	{
		$result = self::getInternal($paramName, $mapName);
		return !is_null($result);
	}

	public static function get($paramName, $mapName = 'local', $defaultValue = false)
	{
		$result = self::getInternal($paramName, $mapName);
		if (is_null($result))
		{
			if ($defaultValue === false)
				throw new Exception("Cannot find [$paramName] in config");
			return $defaultValue;
		}
		return $result;
	}

	/**
	 * Adds the ability to get an element from array(Section) of configuration directly instead of the Entire array
	 * @param string $sectionName
	 * @param string $paramName
	 * @param string $mapName
	 * @param mixed $defaultValue
	 * @return bool|mixed
	 * @throws Exception
	 */
	public static function getItemFromSection($paramName, $sectionName, $mapName = 'local', $defaultValue = false)
	{
		$result = kConf::get($sectionName,$mapName,$defaultValue);
		if (is_array($result) && isset($result[$paramName]))
			return $result[$paramName];
		return $defaultValue;
	}

	public static function getCachedVersionId()
	{
		self::init();
		return self::$cacheVersion;
	}
	
	public static function getAll()
	{
		return self::getMap('local');
	}
	
	public static function getDB()
	{
		return self::getMap('db');
	}
}

