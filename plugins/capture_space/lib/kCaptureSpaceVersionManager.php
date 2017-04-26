<?php
/**
 * @package plugins.captureSpace
 * @subpackage lib
 */
class kCaptureSpaceVersionManager{
    const INI_FILE_NAME = 'collaajClientUpdate.ini';
    
    private static $osUpdateTypes = array(
    	'Mac OS X' => 'zip', 
    	'Windows' => 'msi'
   	);
	
    private static $osInstallTypes = array(
    	'Mac OS X' => 'dmg', 
    	'Windows' => 'exe'
   	);
	
    private static $config = null;
    
	private static function getConfig($os, $osTypes, $version = null){
		if(!self::$config){
			$filename = __DIR__ . '/../config/' . self::INI_FILE_NAME;
			self::$config = parse_ini_file($filename, true);
		}
		
		if($version){
			$version = str_replace('.', '_', $version);
			if(!isset(self::$config[$version])){
				return null;
			}
			$sections = array($version => self::$config[$version]);
		}
		else {
			uksort(self::$config, 'version_compare');
			$sections = array_reverse(self::$config, true);
		}
		
		$osFileType = null;
		foreach($osTypes as $osType => $fileType){
			if(strpos($os, $osType) === 0){
				$osFileType = $fileType;
				break;
			}
		}
		
		foreach($sections as $section){
			if(isset($section[$osFileType])){		
				return $section[$osFileType];
			}
		}
		
		return null;
	}
	
	public static function getUpdateHash($os, $version, $hashAlgorithm){
		$filename = self::getConfig($os, self::$osUpdateTypes, $version);
		if(!$filename){
			return null;
		}
		$actualPath = self::getActualFilePath($filename);
		if (!$actualPath)
			return null;
		$cacheKey = "capture-space-file-hash-key-os".$os."-version-".$version."-hash-algo-".$hashAlgorithm;
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_API_V3);
		$hash = null;
		if (!$cache)
			$hash = hash_file($hashAlgorithm, $actualPath);
		else
			$hash = $cache->get($cacheKey);
		if (!$hash)
		{
			$hash = hash_file($hashAlgorithm, $actualPath);
			$cache->set($cacheKey, $hash);
		}

		return $hash;
	}
	
	public static function getUpdateFile($os, $version){
		$filename = self::getConfig($os, self::$osUpdateTypes, $version);
		if(!$filename){
			return null;
		}
		return $filename;
	}
	
	public static function getInstallFile($os){
		$filename = self::getConfig($os, self::$osInstallTypes);
		if(!$filename){
			return null;
		}
		return $filename;
	}

	public static function getActualFilePath($filename)
	{
		$actualFilePath = myContentStorage::getFSContentRootPath() . "/content/third_party/capturespace/$filename";
		if (!file_exists($actualFilePath)) {
			return false;
		}
		return $actualFilePath;
	}
}