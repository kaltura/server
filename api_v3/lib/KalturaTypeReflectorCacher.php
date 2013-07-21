<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaTypeReflectorCacher
{
	protected static $_loadedTypeReflectors = array();
	protected static $_enabled = true;
	
	static function disable()
	{
		self::$_enabled = false;
	}
	
	static function enable()
	{
		self::$_enabled = true;
	}
	
	/**
	 * @param string $type
	 * @return KalturaTypeReflector
	 */
	static function get($type)
	{
		if (!self::$_enabled)
			return new KalturaTypeReflector($type);
			
		if (!array_key_exists($type, self::$_loadedTypeReflectors))
		{
			$cachedDir = KAutoloader::buildPath(kConf::get("cache_root_path"), "api_v3", "typeReflector");
			if (!is_dir($cachedDir))
			{
				mkdir($cachedDir);
				chmod($cachedDir, 0755);
			}
			
			$cachedFilePath = $cachedDir.DIRECTORY_SEPARATOR.$type.".cache";
			
			$typeReflector = null;
			if (file_exists($cachedFilePath))
			{
				$cachedData = file_get_contents($cachedFilePath);
				$typeReflector = unserialize($cachedData);
			}
			
			if (!$typeReflector)
			{
				$typeReflector = new KalturaTypeReflector($type);
				$cachedData = serialize($typeReflector);
				$bytesWritten = kFile::safeFilePutContents($cachedFilePath, $cachedData);
				if(!$bytesWritten)
				{
					$folderPermission = substr(decoct(fileperms(dirname($cachedFilePath))), 2);
					error_log("Kaltura type reflector could not be saved to path [$cachedFilePath] type [$type] folder permisisons [$folderPermission]");
				}
			}
			
			self::$_loadedTypeReflectors[$type] = $typeReflector;
		}
		
		return self::$_loadedTypeReflectors[$type];
	}
}
