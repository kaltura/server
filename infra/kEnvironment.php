<?php

/**
 * Manages Kaltura environment relative configurations
 * @package infra
 * @subpackage Configuration
 */
class kEnvironment
{
	const APC_CACHE_MAP = 'kConf';
	
	protected static $envMap = null;
	
	protected static function init()
	{
		if (self::$envMap) 
			return;
			
		$appDir = realpath(__DIR__ . '/..');	
		$cacheDir = "$appDir/cache";
		
		self::$envMap = array(
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
		if(isset(self::$envMap[$paramName]))
			return self::$envMap[$paramName];
		
		throw new Exception("Cannot find [$paramName] in config"); 
	}
	
	/**
	 * @param array $srcConfig
	 * @param array $newConfig
	 * @param bool $valuesOnly
	 * @param bool $overwrite
	 * @return array
	 */
	public static function mergeConfigItem(array $srcConfig, array $newConfig, $valuesOnly = false, $overwrite = true)
	{
		$returnedConfig = $srcConfig;
		
		if($valuesOnly)
		{
			foreach($srcConfig as $key => $value)
			{
				if(!isset($newConfig[$key])) // nothing to append
					continue;
				elseif(is_array($value) && isset($newConfig[$key]['disable']) && $newConfig[$key]['disable'] == true)
				{
					unset($returnedConfig[$key]);
					continue;
				}
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

