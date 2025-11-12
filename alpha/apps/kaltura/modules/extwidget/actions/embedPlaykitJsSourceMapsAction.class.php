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
        if (!$sourceMapsCache)
            KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, "PlayKit source maps cache not defined");

        //Get cacheKey
        $cacheKey = $this->getRequestParameter('path');
        if (!$cacheKey)
            KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'path');
        
        //cacheKey should be base64 encoded string which ends with min.js.map
        if (!preg_match('`^[a-zA-Z0-9+/]+={0,2}`', $cacheKey)) 
        {
            KExternalErrors::dieGracefully("Wrong source map name pattern");
        }
        
        $sourceMap = $sourceMapsCache->get($cacheKey);
	    //Source map can be compressed see alpha/apps/kaltura/modules/extwidget/actions/embedPlaykitJsAction.class.php:142
	    if($sourceMap && strpos($sourceMap, "COMPRESSED,") === 0)
	    {
		    $sourceMap = substr($data, strlen("COMPRESSED,"));
		    $sourceMap = gzuncompress($data);
	    }
        header("Content-Type:application/octet-stream");

        echo($sourceMap);
        KExternalErrors::dieGracefully();
    }
}
