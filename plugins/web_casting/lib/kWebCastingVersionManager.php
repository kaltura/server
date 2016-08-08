<?php
/**
 * @package plugins.webCasting
 * @subpackage lib
 */
class kCaptureSpaceVersionManager{
    const INI_FILE_NAME = 'webCastingInfo.ini';

//    private static $supportedOS = array(
//        'windows',
//        'osx'
//    );

    private static function getConfig($os){
        KalturaLog::info("in getConfig");

        $filename = __DIR__ . '/../config/' . self::INI_FILE_NAME;

        KalturaLog::info("filename is " . $filename);

        $config = parse_ini_file($filename, true);
        print_r($config, true);

        $osSpecificConfig = $config[strtolower($os)];

        print_r($osSpecificConfig, true);

        return null;
    }
//
//    public static function getUpdateHash($os, $version){
//        $config = self::getConfig($os, self::$osUpdateTypes, $version);
//        if(!$config){
//            return null;
//        }
//
//        list($filename, $hash) = $config;
//        return $hash;
//    }
//
//    public static function getUpdateFile($os, $version){
//        $config = self::getConfig($os, self::$osUpdateTypes, $version);
//        if(!$config){
//            return null;
//        }
//
//        list($filename, $hash) = $config;
//        return $filename;
//    }
//
//    public static function getInstallFile($os){
//        $config = self::getConfig($os, self::$osInstallTypes);
//        if(!$config){
//            return null;
//        }
//
//        list($filename, $hash) = $config;
//        return $filename;
//    }
    public function getVersionInfo($os, $UIConfId)
    {
        KalturaLog::info("in getVersionInfo");
        self::getConfig($os);
    }
}