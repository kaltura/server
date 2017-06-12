<?php

/**
 * @package Core
 * @subpackage externalWidgets
 */
class embedPlaykitJsSourceMapsAction extends sfAction
{
    public function execute()
    {
        $sourceMapsCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PLAYKIT_JS_SOURCE_MAP);
        if (!sourceMapsCache)
            KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, "PlayKit source maps cache not defined");

        $playkitConfig = kConf::get('playkit-js');

        if (array_key_exists('play_kit_js_cache_version', $playkitConfig)) {
            $this->cacheVersion = rtrim($playkitConfig['play_kit_js_cache_version']);
        } else {
            KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, "play_kit_js_cache_version not defined");
        }

        //Get cacheKey
        $cacheKey = $this->getRequestParameter('path');

        if (!$cacheKey)
            KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'path');
        //cacheKey should be base64 encoded string which ends with min.js.map
        if (!preg_match('`^[a-zA-Z0-9+/]+={0,2}`', $cacheKey)) {
            KExternalErrors::dieGracefully("Wrong source map name pattern");
        }
        $sourceMap = $sourceMapsCache->get($this->cacheVersion . "_" . $cacheKey);

        header("Content-Type:application/octet-stream");

        echo($sourceMap);

        KExternalErrors::dieGracefully();
    }
}
