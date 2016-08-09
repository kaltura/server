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
        return $versionManager->getVersionInfo($os, $UIConfId);
    }
}


