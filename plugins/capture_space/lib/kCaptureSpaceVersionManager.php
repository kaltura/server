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
				return explode(',', $section[$osFileType]);
			}
		}
		
		return null;
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