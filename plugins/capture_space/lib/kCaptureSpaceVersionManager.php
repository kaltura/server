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
    
	private static function initConfig(){
		if(!self::$config){
			$filename = __DIR__ . '/../config/' . self::INI_FILE_NAME;
			self::$config = parse_ini_file($filename, true);
		}
		return self::$config;
	}
	
	private static function getConfig($os, $osTypes, $version = null){
		$config = self::initConfig();
		if($version){
			$version = str_replace('.', '_', $version);
			if(!isset($config[$version])){
				return null;
			}
			$sections = array($version => $config[$version]);
		}
		else {
			uksort($config, 'version_compare');
			$sections = array_reverse($config, true);
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
				return explode(',', $section[$osFileType]);
			}
		}
		
		return null;
	}
	
	public static function isLatest($os, $version){
		$config = self::initConfig();
		$versions = array_keys($config);
		uasort($versions, 'version_compare');
		
		$latestVersion = array_pop($versions);
		$section = $config[$latestVersion];
		
		if($latestVersion == $version){
			foreach(self::$osUpdateTypes as $osType => $fileType){
				if(strpos($os, $osType) === 0){
					return isset($section[$fileType]);
				}
			}
		}
		return false;
	}
	
	public static function getUpdateHash($os, $version){
		$config = self::getConfig($os, self::$osUpdateTypes, $version);
		if(!$config){
			return null;
		}
		
		list($filename, $hash) = $config;
		return $hash;
	}
	
	public static function getUpdateFile($os, $version){
		$config = self::getConfig($os, self::$osUpdateTypes, $version);
		if(!$config){
			return null;
		}
		
		list($filename, $hash) = $config;
		return $filename;
	}
	
	public static function getInstallFile($os){
		$config = self::getConfig($os, self::$osInstallTypes);
		if(!$config){
			return null;
		}
		
		list($filename, $hash) = $config;
		return $filename;
	}
}