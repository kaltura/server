<?php
/**
 * @service webCasting
 * @package plugins.webCasting
 * @subpackage api.services
 */
class WebCastingService extends KalturaBaseService
{
    /**
     * Returns versionInfo
     *
     * @action getVersionInfo
     * @param string $os
     * @param string $UIConfId (optional)
     * @return KalturaWebCastingVersionInfo
     * @throws UI_CONF_NOT_FOUND
     */
    function getVersionInfoAction($os, $UIConfId = null)
    {
        $versionManager = new kWebCastingVersionManager();
        $versionManager->getVersionInfo($os, $UIConfId);

        $response = new KalturaWebCastingVersionInfo();
        $response->url = "http://www.kaltura.com/" . $UIConfId;
        $response->minimalVersion = "1.2.3.4";
        $response->recommendedVersion = "2.3.4.5";

        return $response;
    }
}


