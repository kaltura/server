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
    private static function internalGetVersionInfo($serverDefinedMinimalVersion,
                                                   $serverDefinedRecommendedVersion,
                                                   $serverDefinedURL,
                                                   $UIConfDefinedMinimalVersion,
                                                   $UIConfDefinedIgnoreOptionalUpdates,
                                                   $UIConfDefinedURL)
    {
        KalturaLog::debug('$serverDefinedMinimalVersion = ' . $serverDefinedMinimalVersion .
            ' $serverDefinedRecommendedVersion = ' . $serverDefinedRecommendedVersion .
            ' $serverDefinedURL = ' . $serverDefinedURL .
            ' $UIConfDefinedMinimalVersion = ' . $UIConfDefinedMinimalVersion .
            ' $UIConfDefinedIgnoreOptionalUpdates = ' . $UIConfDefinedIgnoreOptionalUpdates .
            ' $UIConfDefinedURL = ' . $UIConfDefinedURL);

        $response = new KalturaWebCastingVersionInfo();

        // we have a $UIConfDefinedMinimalVersion and its higher
        if ($UIConfDefinedMinimalVersion && version_compare($UIConfDefinedMinimalVersion, $serverDefinedMinimalVersion) > 0)
        {
            $response->minimalVersion = $UIConfDefinedMinimalVersion;
            $response->url = $UIConfDefinedURL;
        }
        else
        {
            $response->minimalVersion = $serverDefinedMinimalVersion;
            $response->url = $serverDefinedURL;
        }

        if ($UIConfDefinedIgnoreOptionalUpdates)
        {
            $response->recommendedVersion = $response->minimalVersion;
        }
        else
        {
            $response->recommendedVersion = $serverDefinedRecommendedVersion;

            // if $serverDefinedRecommendedVersion >= $response->minimalVersion
            if (version_compare($response->minimalVersion, $serverDefinedRecommendedVersion) >= 0)
            {
                $response->url = $serverDefinedURL;
            }
        }

        return $response;
    }

    public function getVersionInfo($os, $UIConfId)
    {
        $osSpecificConfig = self::getServerConfig($os);
        if (!$osSpecificConfig)
            throw new KalturaAPIException(WebCastingErrors::UNKNOWN_OS, $os);

        $serverDefinedMinimalVersion = $osSpecificConfig["minimalVersion"];
        $serverDefinedRecommendedVersion = $osSpecificConfig["recommendedVersion"];
        $serverDefinedURL = $osSpecificConfig["installationURL"];

        if ($UIConfId)
        {
            $ui_conf = uiConfPeer::retrieveByPK($UIConfId);
            if (!$ui_conf)
                throw new KalturaAPIException(WebCastingErrors::UI_CONF_NOT_FOUND, $UIConfId);
        }

        $config = json_decode($ui_conf->getConfig(), true);
        $UIConfDefinedMinimalVersion = array_key_exists("minimalVersion", $config) ? $config["minimalVersion"] : null;
        $UIConfDefinedIgnoreOptionalUpdates = array_key_exists("ignoreOptionalUpdates", $config) ? $config["ignoreOptionalUpdates"] : false;
        $UIConfDefinedURL = $ui_conf->getSwfUrl();

        return self::internalGetVersionInfo($serverDefinedMinimalVersion,
            $serverDefinedRecommendedVersion,
            $serverDefinedURL,
            $UIConfDefinedMinimalVersion,
            $UIConfDefinedIgnoreOptionalUpdates,
            $UIConfDefinedURL);
    }
}