<?php
class KalturaTestsAutoload 
{
	static private $_classDirs = array();
	static private $_classMap = array();
	
	static function register()
	{
		self::$_classDirs = array(
			self::buildPath(KALTURA_ROOT_PATH, "tests", "*")
		);	
		
		// register the autoload
		spl_autoload_register(array("KalturaTestsAutoload", "autoload"));
	}
	
	static function unregister()
	{
		spl_autoload_unregister(array("KAutoloader", "autoload"));
	}
	
	static function autoload($class)
	{
		foreach(self::$_classDirs as $dir)
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
				
			self::scanDirectory($class, $dir, $recursive);
		}
	}
	
	static function scanDirectory($class, $directory, $recursive)
	{
		if (!is_dir($directory))
		{
			return;
		}

		foreach(scandir($directory) as $file)
		{
			if(pathinfo($file, PATHINFO_EXTENSION) == 'avi' ||
			   pathinfo($file, PATHINFO_EXTENSION) == 'flv' )
				continue;
			
			if ($file[0] != "." ) // ignore linux hidden files & non-php files
			{
				$path = realpath($directory."/".$file);
				if (is_dir($path) && $recursive)
				{
					$found = self::scanDirectory($class, $path, $recursive);
					if ($found)
						return true;
				}
				else if (is_file($path)) 
				{
					$classes = array();
					if (preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+)~mi', file_get_contents($path), $classes))
					{
						foreach($classes[1] as $classInFile)
						{
							if ($class === $classInFile)
							{
								require_once($path);
								return;
							} 
						}
					}
				}
			}
		}
		return false;
	}
	
	static function buildPath()
	{
		$args = func_get_args();
		if ($args[count($args) - 1] === "*")
			return implode(DIRECTORY_SEPARATOR, $args);
		else 
			return implode(DIRECTORY_SEPARATOR, $args) . DIRECTORY_SEPARATOR;
	}
}

?>