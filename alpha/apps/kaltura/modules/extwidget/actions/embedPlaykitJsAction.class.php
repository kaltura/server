<?php

/**
 * @package Core
 * @subpackage externalWidgets
 */
class embedPlaykitJsAction extends sfAction
{
    var $eTagHash = null;
    var $partner_id = null;
    var $ui_conf_id = null;
    var $sourcesPath = null;
    var $bundle_name = null;

    public function execute()
    {
        //Get uiConf ID from QS
        $this->uiconf_id = $this->getRequestParameter('uiconf_id');
        if (!$this->uiconf_id)
            KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'uiconf_id');

        // retrieve uiCong Obj
        $uiConf = uiConfPeer::retrieveByPK($this->uiconf_id);
        if (!$uiConf)
            KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);

        //Get partner ID from QS
        $this->partner_id = $this->getRequestParameter('partner_id', $uiConf->getPartnerId());
        if (!$this->partner_id)
            KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'partner_id');

        $bundleWebDirPath = kConf::get('playkit_js_bundles_path');
        $bundleBuilderPath = kConf::get('bundle_builder_cli_path');
        $this->sourcesPath = kConf::get('playkit_js_sources_path');

        //Get bundle configuration stored in conf_vars
        $bundleConfig = $uiConf->getConfVars();

        //convert string to json
        $bundleConfig_json = json_decode($bundleConfig, true);

        //if latest/beta version required set version number in config obj
        $isLatestVersionRequired = strpos($bundleConfig, "{latest}");
        $isBetaVersionRequired = strpos($bundleConfig, "{beta}");
        if ($isLatestVersionRequired || $isBetaVersionRequired) {
            $bundleConfig_json = $this->setLatestOrBetaVersionNumber($bundleConfig_json);
        }

        //sort bundle config by key
        ksort($bundleConfig_json);

        //create base64 bundle name from json config
        $config_str = json_encode($bundleConfig_json);
        $this->bundle_name = base64_encode($config_str);

        //check if bundle already exists
        $bundle_path = $bundleWebDirPath . $this->bundle_name . ".min.js";
        if (file_exists($bundle_path)) {
            $bundleContent = $this->getbundleContent($bundle_path);
        } else {
            //build bundle and save in web dir
            $config = str_replace("\"", "'", $config_str);
            $command = $bundleBuilderPath . ' --name ' . $this->bundle_name . ' --config "' . $config . '" --dest ' . $bundleWebDirPath . " --source " . $this->sourcesPath . " 2>&1";
            exec($command, $output, $return_var);

            //bundle build failed
            if ($return_var != 0) {
                KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config);

            } else {
                if ($output[4] == "Bundle created: $this->bundle_name.min.js") {
                    $bundleContent = $this->getbundleContent($bundle_path);
                } else {
                    KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config);
                }
            }
        }

        // send cache headers
        $this->sendHeaders($bundleContent);

        // start gzip handler if possible:
        if (!ob_start("ob_gzhandler")) ob_start();

        echo($bundleContent);

        KExternalErrors::dieGracefully();
    }

    private function getBundleContent($path)
    {
        $bundleContent = file_get_contents($path);
        $autoEmbed = $this->getRequestParameter('autoembed');
        $iframeEmbed = $this->getRequestParameter('iframeembed');

        //if auto embed selected add embed script to bundle content
        if ($autoEmbed) {
            $bundleContent .= $this->getAutoEmbedCode();
        }

        if ($iframeEmbed) {
            $bundleContent = $this->getIfarmEmbedCode($bundleContent);
        }

        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";
        $host = myPartnerUtils::getCdnHost($this->partner_id, $protocol, 'api');
        $loader = kConf::get('playkit_js_source_map_loader');
        $sourceMapLoaderURL = "$host/$loader/path/$this->bundle_name.min.js.map";
        $bundleContent = str_replace("//# sourceMappingURL=$this->bundle_name.min.js.map", "//# sourceMappingURL=$sourceMapLoaderURL", $bundleContent);

        return $bundleContent;
    }

    private function sendHeaders($content)
    {
        // Support Etag and 304
        if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= gmdate('D, d M Y H:i:s', time()) ||
            @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $this->getOutputHash($content)
        ) {
            header('HTTP/1.1 304 Not Modified');
            exit();
        }

        $iframeEmbed = $this->getRequestParameter('iframeembed');
        if ($iframeEmbed) {
            header("Content-Type: text/html");
        } else {
            header("Content-Type: text/javascript");
        }

        header("Etag: " . $this->getOutputHash($content));

        // alwayse set cross orgin headers:
        header("Access-Control-Allow-Origin: *");
        // Default expire time for the loader to 10 min ( we support 304 not modified so no need for long expire )
        $max_age = 60 * 10;
        header("Cache-Control: public, max-age=$max_age max-stale=0");
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . ' GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
    }

    private function getOutputHash($o)
    {
        if (!$this->eTagHash) {
            $this->eTagHash = md5($o);
        }
        return $this->eTagHash;
    }

    private function getAutoEmbedCode()
    {
        $config = json_encode($this->getRequestParameter("config"));
        $entry_id = $this->getRequestParameter('entry_id');
        $autoEmbedCode = "\n var player; var ovpProvider = new Providers.OvpProvider($this->partner_id,\"\",$config);\n" .
            "\t    ovpProvider.getConfig(\"" . $entry_id . "\",$this->uiconf_id).then(config => {\n" .
            "\t    player = Playkit.playkit(config);\n" .
            "\t }, \n" .
            "\t err => {\n" .
            "\t    console.log(err)\n" .
            "\t})\n";

        return $autoEmbedCode;
    }

    private function getIfarmEmbedCode($bundleContent)
    {
        $bundleContent .= $this->getAutoEmbedCode();
        $htmlDoc = '<!DOCTYPE html PUBLIC " -//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns = "http://www.w3.org/1999/xhtml" >
                <head >
                <meta http - equiv = "Content-Type" content = "text/html; charset=iso-8859-1" />
               
                </head >
                <body >
                 <script type = "text/javascript" > ' . $bundleContent . '</script >
                </body >
            </html >
';
        return $htmlDoc;
    }

    private function setLatestOrBetaVersionNumber($bundleConfig_json)
    {
        $latestVersionsMapPath = $this->sourcesPath . "/latest.json";
        $latestVersionMap = file_exists($latestVersionsMapPath) ? json_decode(file_get_contents($latestVersionsMapPath), true) : null;

        $betaVersionsMapPath = $this->sourcesPath . "/beta.json";
        $betatVersionMap = file_exists($betaVersionsMapPath) ? json_decode(file_get_contents($betaVersionsMapPath), true) : null;

        foreach ($bundleConfig_json as $key => $val) {
            if ($val == "{latest}" && $latestVersionMap != null) {
                $bundleConfig_json[$key] = $latestVersionMap[$key];
            }

            if ($val == "{beta}" && $betatVersionMap != null) {
                $bundleConfig_json[$key] = $betatVersionMap[$key];
            }
        }
        return $bundleConfig_json;
    }

}
