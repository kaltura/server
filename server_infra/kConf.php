<?php
/**
 * @package server-infra
 * @subpackage config
 */
setlocale(LC_ALL, 'en_US.UTF-8');
libxml_disable_entity_loader(true);

require_once __DIR__ . '/../infra/kEnvironment.php';

/**
 * Manages all Kaltura configurations
 * @package server-infra
 * @subpackage Configuration
 */
class kConf extends kEnvironment
{
	const APC_CACHE_MAP = 'kConf';
	const CACHE_VERSION_KEY = '__config_cache_version';
	
	protected static $initialized = false;
	
	protected static function init()
	{
		if (self::$initialized) 
			return;
		
		self::$initialized = true;
		parent::init();
		
		$cacheDir = self::$map['cache_root_path'];
		$reloadFileExists = file_exists("$cacheDir/base.reload");
		
		if (!$reloadFileExists && function_exists('apc_fetch'))
		{
			$apcMap = apc_fetch(self::APC_CACHE_MAP);
			if($apcMap)
			{
				self::$map = $apcMap;
				return;
			}
		}
		
		$configDir = self::getConfigDir();
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
		$config = self::mergeConfigItem($config, $localConfig);
		
		$hostname = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname());
		if($hostname)
		{
			$localConfigFile = "$hostname.ini";
			
			$configPath = "$configDir/hosts";
			$configDir = dir($configPath);
			while (false !== ($iniFile = $configDir->read())) 
			{
				$iniFileMatch = str_replace('#', '*', $iniFile);
				if(!fnmatch($iniFileMatch, $localConfigFile))
					continue;
					
				$localConfig = parse_ini_file("$configPath/$iniFile", true);
				$config = self::mergeConfigItem($config, $localConfig);
			}
			$configDir->close();
		}
			
		self::$map = array_merge(self::$map, $config);
		
		if(function_exists('apc_store'))
		{
			self::$map[self::CACHE_VERSION_KEY] = uniqid();
			$res = apc_store(self::APC_CACHE_MAP, self::$map);
			if($reloadFileExists && $res)
			{
				$deleted = @unlink("$cacheDir/base.reload");
				error_log("Base configuration reloaded");
				if(!$deleted)
					error_log("Failed to delete base.reload file");
			}
		}			
	}
	
	public static function getCachedVersionId()
	{
		if (!isset(self::$map[self::CACHE_VERSION_KEY]))
			return null;
		
		return self::$map[self::CACHE_VERSION_KEY];
	}

	public static function get($paramName)
	{
		self::init();
		return parent::get($paramName); 
	}
	
	public static function getAll()
	{
		self::init();
		return self::$map;
	}
		
	public static function hasMap($mapName)
	{
		self::init();
		
		if($mapName == 'local')
			return true;
		
		if(isset(self::$map[$mapName]))
			return true;
		
		$configDir = realpath(dirname(__file__) . '/../configurations');
		return file_exists("$configDir/$mapName.ini");
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
	
		$hostname = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname());
		if($hostname)
		{
			$localConfigFile = "$hostname.ini";
			
			$configPath = "$configDir/hosts/$mapName";
			if(file_exists($configPath) && is_dir($configPath)){			
				$configDir = dir($configPath);
				while (false !== ($iniFile = $configDir->read())) 
				{
					$iniFileMatch = str_replace('#', '*', $iniFile);
					if(!fnmatch($iniFileMatch, $localConfigFile))
						continue;
						
					$config = new Zend_Config_Ini("$configPath/$iniFile");
					self::$map[$mapName] = self::mergeConfigItem(self::$map[$mapName], $config->toArray());
				}
				$configDir->close();
			}
		}
		
		if(function_exists('apc_store'))
			apc_store(self::APC_CACHE_MAP, self::$map);
		
		return self::$map[$mapName];
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

	/**
	 * @param array $srcConfig
	 * @param array $newConfig
	 * @param bool $valuesOnly
	 * @param bool $overwrite
	 * @return array
	 */
	protected static function mergeConfigItem(array $srcConfig, array $newConfig, $valuesOnly = false, $overwrite = true)
	{
		$returnedConfig = $srcConfig;
		
		if($valuesOnly)
		{
			foreach($srcConfig as $key => $value)
			{
				if(!isset($newConfig[$key])) // nothing to append
					continue;
				elseif(is_array($value))
					$returnedConfig[$key] = self::mergeConfigItem($srcConfig[$key], $newConfig[$key], $valuesOnly, $overwrite);
				elseif($overwrite)
					$returnedConfig[$key] = $newConfig[$key];
				else
					$returnedConfig[$key] = $srcConfig[$key] . ',' . $newConfig[$key];
			}
		}
		else
		{
			foreach($newConfig as $key => $value)
			{
				if(is_numeric($key))
				{
					$returnedConfig[] = $newConfig[$key];
				}
				elseif(!isset($srcConfig[$key]))
				{
					$returnedConfig[$key] = $newConfig[$key];
				}
				elseif(is_array($value))
				{
					if(!isset($srcConfig[$key]))
						$srcConfig[$key] = array();
						
					$returnedConfig[$key] = self::mergeConfigItem($srcConfig[$key], $newConfig[$key], $valuesOnly, $overwrite);
				}
				elseif($overwrite)
				{
					$returnedConfig[$key] = $newConfig[$key];
				}
				else
				{
					$returnedConfig[$key] = $srcConfig[$key] . ',' . $newConfig[$key];
				}
			}
		}
		
		return $returnedConfig;
	}
}

