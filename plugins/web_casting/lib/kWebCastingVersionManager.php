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


    public function getVersionInfo($os, $UIConfId)
    {
        KalturaLog::info("in getVersionInfo");
        $osSpecificConfig = self::getConfig($os);
        if (!$osSpecificConfig)
            throw new KalturaAPIException(WebCastingErrors::UNKNOWN_OS, $os);

        KalturaLog::debug('got ' . print_r($osSpecificConfig, true) . ' from getConfig for os ' . $os);

        $serverDefinedMinimalVersion = $osSpecificConfig["minimalVersion"];
        $serverDefinedRecommended = $osSpecificConfig["recommendedVersion"];
        $serverDefinedURL = $osSpecificConfig["installationURL"];

        KalturaLog::debug('$serverDefinedMinimalVersion = ' . $serverDefinedMinimalVersion);
        KalturaLog::debug('$serverDefinedRecommended = ' . $serverDefinedRecommended);
        KalturaLog::debug('$serverDefinedURL = ' . $serverDefinedURL);


        $ui_conf = uiConfPeer::retrieveByPK($UIConfId);
        if (!$ui_conf)
            throw new KalturaAPIException(WebCastingErrors::UI_CONF_NOT_FOUND, $UIConfId);

        $config = json_decode($ui_conf->getConfig());
        KalturaLog::debug('gonfig after json_decode ' . print_r($config, true));

        $UIConfDefinedMinimalVersion = $config["minimalVersion"];
        $ignoreOptionalUpdates = $config["ignoreOptionalUpdates"];
        $UIConfDefinedURL = $ui_conf->getSwfUrl();
        KalturaLog::debug('$UIConfDefinedMinimalVersion = ' . $UIConfDefinedMinimalVersion);
        KalturaLog::debug('$ignoreOptionalUpdates = ' . $ignoreOptionalUpdates);
        KalturaLog::debug('$UIConfDefinedURL = ' . $UIConfDefinedURL);



    }
}