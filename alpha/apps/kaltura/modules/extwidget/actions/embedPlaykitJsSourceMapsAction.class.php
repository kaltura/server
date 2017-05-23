<?php

/**
 * @package Core
 * @subpackage externalWidgets
 */
class embedPlaykitJsSourceMapsAction extends sfAction
{
    var $uiconf_id = null;

    public function execute()
    {
        //Get file name
        $fileName = $this->getRequestParameter('path');
        if (!$fileName)
            KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'path');
        $bundleWebDirPath = kConf::get('playkit_js_bundles_path');
        $sourceMapFilePath = $bundleWebDirPath .$fileName ;
        $sourceMap = file_get_contents($sourceMapFilePath);
        header("Content-Type: application/octet-stream");

        echo($sourceMap);

        KExternalErrors::dieGracefully();
    }
}
