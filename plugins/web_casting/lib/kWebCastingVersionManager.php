<?php
/**
 * @package plugins.webCasting
 * @subpackage lib
 */
class kWebCastingVersionManager{
    const INI_FILE_NAME = 'webCastingInfo.ini';

    // returns an associative array like below, or null if the $os configuration:
    //    [minimalVersion] => 2.0.142
    //    [recommendedVersion] => 2.0.155
    //    [installationURL] => http://www.kaltura.com/flash/webcastproducer/v2.0.155/KalturaWebCast.exe
    private static function getConfig($os){
        $filename = __DIR__ . '/../config/' . self::INI_FILE_NAME;

        $config = parse_ini_file($filename, true);
        KalturaLog::info(print_r($config, true));

        $os_lower = strtolower($os);
        if (array_key_exists($os_lower, $config))
        {
            return $config[strtolower($os)];
        }
        else
        {
            KalturaLog::warning("tried to get non existing configuration for os [" . $os_lower . "]");
            return null;
        }
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
        $osSpecificConfig = self::getConfig($os);
        if (!$osSpecificConfig)
            throw new KalturaAPIException(WebCastingErrors::UNKNOWN_OS, $os);

        KalturaLog::debug('got ' . $osSpecificConfig . ' from getConfig for os ' . $os);
    }
}