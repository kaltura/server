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
    private static function getServerConfig($os){
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

    // Implement actual logic
    //
    private static function internalGetVersionInfo($serverDefinedMinimalVersion,
                                                   $serverDefinedRecommendedVersion,
                                                   $serverDefinedURL,
                                                   $UIConfDefinedMinimalVersion,
                                                   $UIConfDefinedIgnoreOptionalUpdates,
                                                   $UIConfDefinedURL)
    {
        KalturaLog::debug('$serverDefinedMinimalVersion = ' . $serverDefinedMinimalVersion .
            '$serverDefinedRecommendedVersion = ' . $serverDefinedRecommendedVersion .
            '$serverDefinedURL = ' . $serverDefinedURL .
            '$UIConfDefinedMinimalVersion = ' . $UIConfDefinedMinimalVersion .
            '$UIConfDefinedIgnoreOptionalUpdates = ' . $UIConfDefinedIgnoreOptionalUpdates .
            '$UIConfDefinedURL = ' . $UIConfDefinedURL);


        $response = new KalturaWebCastingVersionInfo();
        $response->url = "http://www.kaltura.com/";
        $response->minimalVersion = "1.2.3.4";
        $response->recommendedVersion = "2.3.4.5";

        return $response;
    }

    public function getVersionInfo($os, $UIConfId)
    {
        KalturaLog::info("in getVersionInfo");
        $osSpecificConfig = self::getServerConfig($os);
        if (!$osSpecificConfig)
            throw new KalturaAPIException(WebCastingErrors::UNKNOWN_OS, $os);

        $serverDefinedMinimalVersion = $osSpecificConfig["minimalVersion"];
        $serverDefinedRecommendedVersion = $osSpecificConfig["recommendedVersion"];
        $serverDefinedURL = $osSpecificConfig["installationURL"];

        $ui_conf = uiConfPeer::retrieveByPK($UIConfId);
        if (!$ui_conf)
            throw new KalturaAPIException(WebCastingErrors::UI_CONF_NOT_FOUND, $UIConfId);

        $config = json_decode($ui_conf->getConfig(), true);
        $UIConfDefinedMinimalVersion = array_key_exists("minimalVersion", $config) ? $config["minimalVersion"] : null;
        $UIConfDefinedIgnoreOptionalUpdates = array_key_exists("ignoreOptionalUpdates", $config) ? $config["ignoreOptionalUpdates"] : null;
        $UIConfDefinedURL = $ui_conf->getSwfUrl();

        return self::internalGetVersionInfo($serverDefinedMinimalVersion,
            $serverDefinedRecommendedVersion,
            $serverDefinedURL,
            $UIConfDefinedMinimalVersion,
            $UIConfDefinedIgnoreOptionalUpdates,
            $UIConfDefinedURL);
    }
}