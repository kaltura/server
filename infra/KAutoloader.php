<?php
class KAutoloader 
{
	static private $_oldIncludePath = "";
	static private $_classPath = null;
	static private $_includePath = null;
	static private $_classMap = array();
	static private $_classMapFileLocation = "";
	static private $_noCache = false;
	
	static function register()
	{
		if (self::$_classPath === null)
			self::setDefaultClassPath();
			
		if (self::$_includePath === null)
			self::setDefaultIncludePath();
		
		// register the autoload
		spl_autoload_register(array("KAutoloader", "autoload"));
		
		// set include path
		self::$_oldIncludePath = get_include_path();
		set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, self::$_includePath));
	}
	
	static function unregister()
	{
		spl_autoload_unregister(array("KAutoloader", "autoload"));
		set_include_path(self::$_oldIncludePath);
	}
	
	static function autoload($class)
	{
		// if the class is part of Zend Framework, use Zend's loader
		if (strpos($class, "Zend_") === 0)
		{
			$zendLoaderClass = "Zend_Loader.php";
			require_once(self::buildPath(KALTURA_ROOT_PATH, "vendor", "ZendFramework", "library").DIRECTORY_SEPARATOR.str_replace("_", DIRECTORY_SEPARATOR, $zendLoaderClass));
			Zend_Loader::loadClass($class);
			return;
		}
		
		self::loadClassMap();
		
		if (array_key_exists($class, self::$_classMap))
		{
			require_once(self::$_classMap[$class]);
			return;
		}
	}
	
	static function scanDirectory($directory, $recursive)
	{
		if (!is_dir($directory))
		{
			return;
		}

		foreach(scandir($directory) as $file)
		{
			if ($file[0] != ".") // ignore linux hidden files
			{
				$path = realpath($directory."/".$file);
				if (is_dir($path) && $recursive)
				{
					$found = self::scanDirectory($path, $recursive);
					if ($found)
						return true;
				}
				else if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) == "php") 
				{
					$classes = array();
					if (preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+)~mi', file_get_contents($path), $classes))
					{
						foreach($classes[1] as $class)
						{
							self::$_classMap[$class] = $path;
						}
					}
				}
			}
		}
		return false;
	}
	
	static function setNoCache($noCache)
	{
		self::$_noCache = $noCache;
	}
	
	static function buildPath()
	{
		$args = func_get_args();
		return implode(DIRECTORY_SEPARATOR, $args);
	}
	
	/**
	 * Get the class map cache file path
	 */
	static function getClassMapFilePath()
	{
		return self::$_classMapFileLocation;
	}
	
	/**
	 * Set the class map cache file path
	 * 
	 * @param string $path
	 */
	static function setClassMapFilePath($path)
	{
		self::$_classMapFileLocation = $path;
	}
	
	/**
	 * Returns the array of include paths
	 * 
	 * @return array
	 */
	static function getIncludePath()
	{
		if (self::$_includePath === null)
			self::setDefaultIncludePath();
			
		return self::$_includePath;
	}
	
	/**
	 * Set the array of include paths
	 * 
	 * @param $array
	 */
	static function setIncludePath($array)
	{
		self::$_includePath = $array;
	}
	
	/**
	 * Adds another include path to the list of include paths
	 * @param $path
	 */
	static function addIncludePath($path)
	{
		if (self::$_includePath === null)
			self::setDefaultIncludePath();
			
		self::$_includePath[] = $path;
	}
	
	/**
	 * Returns the array of class paths
	 * 
	 * @return array
	 */
	static function getClassPath()
	{
		if (self::$_classPath === null)
			self::setDefaultClassPath();
			
		return self::$_classPath;
	}
	
	/**
	 * Set the array of class paths
	 * 
	 * @param $array
	 */
	static function setClassPath($array)
	{
		self::$_classPath = $array;
	}
	
	/**
	 * Adds another class path to the list of class paths
	 * @param $path
	 */
	static function addClassPath($path)
	{
		if (self::$_classPath === null)
			self::setDefaultClassPath();
			
		if(strpos($path, DIRECTORY_SEPARATOR . '*') > 0)
		{
			list($base, $rest) = explode(DIRECTORY_SEPARATOR . '*', $path, 2);
			if(strpos($rest, DIRECTORY_SEPARATOR . '*') > 0)
			{
				foreach(scandir($base) as $sub_folder)
				{
					if ($sub_folder[0] == "." || $sub_folder[0] == "..") // ignore linux hidden files
						continue;
						
					$path = realpath($base . DIRECTORY_SEPARATOR . $sub_folder);
					if (is_dir($path))
						self::addClassPath($path . $rest);
				}
				return;
			}
		}
		self::$_classPath[] = $path;
	}
	
	/**
	 * Get the class map array
	 * @return array
	 */
	static function getClassMap()
	{
		if(!count(self::$_classMap))
			self::loadClassMap();
			
		return self::$_classMap;
	}
	
	/**
	 * Sets the default class paths
	 */
	private static function setDefaultClassPath()
	{
		self::$_classPath = array(
			self::buildPath(KALTURA_ROOT_PATH, "infra", "*"),
	   		self::buildPath(KALTURA_ROOT_PATH, "vendor", "symfony", "*"),
	   		self::buildPath(SF_ROOT_DIR, "lib", "*"),
	   		self::buildPath(SF_ROOT_DIR, "config"),
	   		self::buildPath(SF_ROOT_DIR, "apps", "kaltura", "lib", "*"),
		);	
	}
	
	/**
	 * Sets the default include paths
	 */
	private static function setDefaultIncludePath()
	{
		self::$_includePath = array(
			self::buildPath(KALTURA_ROOT_PATH),
			self::buildPath(KALTURA_ROOT_PATH, "vendor", "symfony"),
			self::buildPath(KALTURA_ROOT_PATH, "vendor", "symfony", "vendor"),
			self::buildPath(KALTURA_ROOT_PATH, "vendor", "ZendFramework", "library"),
			self::buildPath(SF_ROOT_DIR),
			self::buildPath(SF_ROOT_DIR, "lib"),
			self::buildPath(SF_ROOT_DIR, "apps", "kaltura", "lib"),
		);
	}
	
	/**
	 * Load and cache the class map
	 */
	private static function loadClassMap()
	{
		if (!file_exists(self::$_classMapFileLocation) || self::$_noCache == true)
		{
			// cached map doesn't exists, rebuild the cache map
			foreach(self::$_classPath as $dir)
			{
				if (strpos($dir, DIRECTORY_SEPARATOR."*") == strlen($dir) - 2)
				{
					$dir = substr($dir, 0, strlen($dir) - 2);
					$recursive = true;
				}
				else 
				{
					$recursive = false;
				}
					
				self::scanDirectory($dir, $recursive);
			}
			
			if (self::$_noCache === false)
			{
				// save the cached map
				file_put_contents(self::$_classMapFileLocation, serialize(self::$_classMap));
			}
		}
		else if (count(self::$_classMap) == 0) 
		{
			// if cached map was not loaded but exists on the disk, load it
			self::$_classMap = unserialize(file_get_contents(self::$_classMapFileLocation));
		}
	}
}

?>