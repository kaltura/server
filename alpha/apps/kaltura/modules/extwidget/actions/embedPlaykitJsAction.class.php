<?php

/**
 * @package Core
 * @subpackage externalWidgets
 */
class embedPlaykitJsAction extends sfAction
{
	const UI_CONF_ID_PARAM_NAME = "uiconf_id";
	const PARTNER_ID_PARAM_NAME = "partner_id";
	const VERSIONS_PARAM_NAME = "versions";
	const ENTRY_ID_PARAM_NAME = "entry_id";
	const CONFIG_PARAM_NAME = "config";	
	const REGENERATE_PARAM_NAME = "regenerate";
	const IFRAME_EMBED_PARAM_NAME = "iframeembed";
	const AUTO_EMBED_PARAM_NAME = "autoembed";
	const LATEST = "{latest}";
	const BETA = "{beta}";

	private $bundleCache = null;
	private $sourceMapsCache = null;
	private $eTagHash = null;
	private $uiconfId = null;
	private $partnerId = null;
	private $bundle_name = null;
	private $bundlerUrl = null;
	private $sourcesPath = null;
	private $bundleConfig = null;
	private $sourceMapLoader = null;
	private $cacheVersion = null;
	private $playKitVersion = null;
	private $playerConfig = null;
	private $uiConfUpdatedAt = null;
	private $regenerate = false;
	
	public function execute()
	{
		$this->initMembers();
		
		$bundleContent = $this->bundleCache->get($this->bundle_name);
		if (!$bundleContent || $this->regenerate) 
		{
			$bundleContent = kLock::runLocked($this->bundle_name, array("embedPlaykitJsAction", "buildBundleLocked"), array($this));
		}

		$lastModified = $this->getLastModified($bundleContent);

		//Format bundle contnet
		$bundleContent = $this->formatBundleContent($bundleContent);

		// send cache headers
		$this->sendHeaders($bundleContent, $lastModified);
		
		echo($bundleContent);
		
		KExternalErrors::dieGracefully();
	}
	
	public static function buildBundleLocked($context)
	{
		//if bundle not exists or explicitly should be regenerated build it
		if(!$context->regenerate)
		{
			$bundleContent = $context->bundleCache->get($context->bundle_name);
			if ($bundleContent) 
			{
				return $bundleContent;
			}
		}

		//build bundle and save in memcache
		$config = str_replace("\"", "'", json_encode($context->bundleConfig));
		if(!$config)
		{
			KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config . " wrong config object");
		}

		$url = $context->bundlerUrl . "/build?config=" . base64_encode($config) . "&name=" . $context->bundle_name . "&source=" . base64_encode($context->sourcesPath);
		$content = KCurlWrapper::getContent($url, array('Content-Type: application/json'));

		if (!$content) 
		{
			KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config . " failed to get content from bundle builder");
		}

		$content = json_decode($content, true);
		if(!$content || !$content['bundle'])
		{
			KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config . " bundle created with wrong content");
		}

		$sourceMapContent = base64_decode($content['sourceMap']);
		$bundleContent = time() . "," . base64_decode($content['bundle']);
		$bundleSaved =  $context->bundleCache->set($context->bundle_name, $bundleContent);
		$context->sourceMapsCache->set($context->bundle_name, $sourceMapContent);
		if(!$bundleSaved)
		{
			KalturaLog::log("Error - failed to save bundle content in cache for config [".$config."]");
		}

		return $bundleContent;
		
	}
	
	private function formatBundleContent($bundleContent)
	{
		$bundleContentParts = explode(",", $bundleContent, 2);
		$bundleContent = $this->appendConfig($bundleContentParts[1]);
		$autoEmbed = $this->getRequestParameter(self::AUTO_EMBED_PARAM_NAME);
		$iframeEmbed = $this->getRequestParameter(self::IFRAME_EMBED_PARAM_NAME);
		
		//if auto embed selected add embed script to bundle content
		if ($autoEmbed) 
		{
			$bundleContent .= $this->getAutoEmbedCode();
		} 
		elseif ($iframeEmbed) 
		{
			$bundleContent = $this->getIfarmEmbedCode($bundleContent);
		}
		
		$protocol = infraRequestUtils::getProtocol();
		$host = myPartnerUtils::getCdnHost($this->partnerId, $protocol, 'api');
		$sourceMapLoaderURL = "$host/$this->sourceMapLoader/path/$this->bundle_name";
		$bundleContent = str_replace("//# sourceMappingURL=$this->bundle_name.min.js.map", "//# sourceMappingURL=$sourceMapLoaderURL", $bundleContent);
		
		return $bundleContent;
	}

	private function appendConfig($content)
	{
	    $uiConf = $this->playerConfig;
	    $uiConf["env"] = $this->getEnvConfig();
	    $uiConfJson = json_encode($uiConf);
	    if ($uiConfJson === false)
	    {
	        KExternalErrors::dieError(KExternalErrors::INVALID_PARAMETER, "Invalid config object");
	    }
	    $confNS = "window.__kalturaplayerdata";
	    $content .= "
	    $confNS = ($confNS || {});
	    $confNS.UIConf = ($confNS.UIConf||{});$confNS.UIConf[\"" . $this->uiconfId . "\"]=$uiConfJson;
	    ";
	    return $content;
	}

	private function getEnvConfig()
	{
		$protocol = infraRequestUtils::getProtocol();

		// The default Kaltura service url:
		$serviceUrl = requestUtils::getApiCdnHost().'/api_v3';
		// Default Kaltura CDN url:
		$cdnUrl = requestUtils::getCdnHost($protocol);
		// Default Stats URL
		$statsServiceUrl = $this->buildUrl($protocol,"stats_host");
		// Default Live Stats URL
		$liveStatsServiceUrl = $this->buildUrl($protocol,"live_stats_host");
		// Default Kaltura Analytics URL
		$analyticsServiceUrl = $this->buildUrl($protocol,"analytics_host");
		// Get Kaltura Supported API Features
		$apiFeatures = $this->getFromConfig('features');

		$envConfig = array(
			"serviceUrl" => $serviceUrl,
			"cdnUrl" => $cdnUrl,
			"statsServiceUrl" => $statsServiceUrl,
			"liveStatsServiceUrl" => $liveStatsServiceUrl,
			"analyticsServiceUrl" => $analyticsServiceUrl,
			"apiFeatures" => $apiFeatures
		);
		return $envConfig;
	}

	private function getFromConfig($key)
	{
		if( kConf::hasParam($key) ) {
			return kConf::get($key);
		}
		return '';
	}

	private function buildUrl($protocol, $key)
	{
	    if ($protocol == "https")
	    {
	        $key .= "_https";
	    }
		$configValue = $this->getFromConfig($key);
		$port = (($_SERVER['SERVER_PORT']) != '80' && $_SERVER['SERVER_PORT'] != '443')?':'.$_SERVER['SERVER_PORT']:'';
		if( $key && $configValue)
		{
			return $protocol . '://' . $configValue . $port;
		}

		return '';
	}
	
	private function sendHeaders($content, $lastModified)
	{
		$max_age = 60 * 10;
		// Support Etag and 304
		if (
			(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
				$_SERVER['HTTP_IF_MODIFIED_SINCE'] == infraRequestUtils::formatHttpTime($lastModified)) &&
			(isset($_SERVER['HTTP_IF_NONE_MATCH']) && @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $this->getOutputHash($content))
		) {
			infraRequestUtils::sendCachingHeaders($max_age, false, $lastModified);
			header("HTTP/1.1 304 Not Modified");
			return;
		}
		
		$iframeEmbed = $this->getRequestParameter('iframeembed');
		if ($iframeEmbed) 
		{
			header("Content-Type: text/html");
		} 
		else 
		{
			header("Content-Type: text/javascript");
		}
		
		header("Etag: " . $this->getOutputHash($content));
		// always set cross orgin headers:
		header("Access-Control-Allow-Origin: *");
		infraRequestUtils::sendCachingHeaders($max_age, false, $lastModified);
	}
	
	private function getLastModified($content)
	{
		$contentParts = explode(",", $content, 2);
		$lastModified = $contentParts[0];
		
		if($this->uiConfUpdatedAt > $lastModified)
			$lastModified = $this->uiConfUpdatedAt;

		return $lastModified;
	}
	
	private function getOutputHash($o)
	{
		if (!$this->eTagHash) 
		{
			$this->eTagHash = md5($o);
		}
		return $this->eTagHash;
	}
	
	private function getAutoEmbedCode($targetId = null)
	{
		$targetId = $targetId ? $targetId : $this->getRequestParameter('targetId');
		if (is_null($targetId) && $targetId == "")
		{
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, "Player target ID not defined");
		}
		$entry_id = $this->getRequestParameter(self::ENTRY_ID_PARAM_NAME);		
		if (!$entry_id)
		{
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, "Entry ID not defined");
		}
		$config = $this->getRequestParameter(self::CONFIG_PARAM_NAME, array());		
		//enable passing nested config options
		foreach ($config as $key=>$val)
		{
			$config[$key] = json_decode($val);
		}

		$config["partnerId"] = $this->partnerId;		
		$config["uiConfId"] = $this->uiconfId;
		
		$config = json_encode($config);		
		if ($config === false)
		{
			KExternalErrors::dieError(KExternalErrors::INVALID_PARAMETER, "Invalid config object");
		}

		$autoEmbedCode = "
		try {
			var kalturaPlayer = KalturaPlayer.setup(\"$targetId\", $config);
		    kalturaPlayer.loadMedia(\"" . $entry_id . "\");
		  } catch (e) {
		    console.error(e.message)
		  }
		";
		
		return $autoEmbedCode;
	}
	
	private function getIfarmEmbedCode($bundleContent)
	{
		$bundleContent .= $this->getAutoEmbedCode("player_container");
		$htmlDoc = '<!DOCTYPE html PUBLIC " -//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns = "http://www.w3.org/1999/xhtml" >
                        <head >
                            <meta http - equiv = "Content-Type" content = "text/html; charset=iso-8859-1" />
                            <style>
                            	#player_container{
	                            	position: absolute;
								    top: 0;
								    left: 0;
								    height: 100%;
								    width: 100%;
                        		}
                            </style>
                        </head >
                        <body >
                        	<div id="player_container"></div>
                            <script type = "text/javascript" > ' . $bundleContent . '</script >
                        </body >
                    </html >';
		return $htmlDoc;
	}

	private function toAssociativeArray($input)
	{
		$configs = explode(",", $input);
		$arr = array();
		foreach($configs as $conf)
		{
			$obj = explode("=", $conf);
			$key = $obj[0];
			$value = $obj[1];
			$arr[$key] = $value;
		}
		return $arr;
	}

	private function mergeVersionsParamIntoConfig()
	{
		//Get version from QS
		$versions = $this->getRequestParameter(self::VERSIONS_PARAM_NAME);
		if ($versions) {
			$pattern = '/[^?&,]+=[^?&,]+(?>,[^,?&]+=[^,?&]+)*/'; // key value object
			$success = preg_match($pattern, $versions, $matches);
			if ($success && strlen($matches[0]) === strlen($versions)) { // the whole versions string matches the pattern
				$versionsArr = $this->toAssociativeArray($versions);
				if (!$this->bundleConfig) {
					$this->bundleConfig = array();
				}
				$this->bundleConfig = array_merge($this->bundleConfig, $versionsArr);
			}
		}
	}
	
	private function setLatestOrBetaVersionNumber()
	{
		//if latest/beta version required set version number in config obj
		$isLatestVersionRequired = array_search(self::LATEST, $this->bundleConfig) !== false;
		$isBetaVersionRequired = array_search(self::BETA, $this->bundleConfig) !== false;

		if ($isLatestVersionRequired || $isBetaVersionRequired) {
			$latestVersionsMapPath = $this->sourcesPath . "/latest.json";
			$latestVersionMap = file_exists($latestVersionsMapPath) ? json_decode(file_get_contents($latestVersionsMapPath), true) : null;

			$betaVersionsMapPath = $this->sourcesPath . "/beta.json";
			$betaVersionMap = file_exists($betaVersionsMapPath) ? json_decode(file_get_contents($betaVersionsMapPath), true) : null;

			foreach ($this->bundleConfig as $key => $val)
			{
				if ($val == self::LATEST && $latestVersionMap != null && isset($latestVersionMap[$key]))
				{
					$this->bundleConfig[$key] = $latestVersionMap[$key];
				}

				if ($val == self::BETA && $betaVersionMap != null && isset($betaVersionMap[$key]))
				{
					$this->bundleConfig[$key] = $betaVersionMap[$key];
				}
			}
		}
	}
	
	private function initMembers()
	{
		$this->eTagHash = null;
		
		$this->bundleCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PLAYKIT_JS);
		if (!$this->bundleCache)
			KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, "Bundle cache not defined");
		
		$this->sourceMapsCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PLAYKIT_JS_SOURCE_MAP);
		if (!$this->sourceMapsCache)
			KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, "PlayKit source maps cache not defined");
		
		//Get uiConf ID from QS
		$this->uiconfId = $this->getRequestParameter(self::UI_CONF_ID_PARAM_NAME);
		if (!$this->uiconfId)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, self::UI_CONF_ID_PARAM_NAME);
		
		// retrieve uiCong Obj
		$uiConf = uiConfPeer::retrieveByPK($this->uiconfId);
		if (!$uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);
		$this->playerConfig = json_decode($uiConf->getConfig(), true);
		$this->uiConfUpdatedAt = $uiConf->getUpdatedAt(null);
		
		//Get bundle configuration stored in conf_vars
		$confVars = $uiConf->getConfVars();
		if (!$confVars) {
			KExternalErrors::dieGracefully("Missing bundle configuration in uiConf, uiConfID: $this->uiconfId");
		}
		
		//Get partner ID from QS or from UI conf
		$this->partnerId = $this->getRequestParameter(self::PARTNER_ID_PARAM_NAME, $uiConf->getPartnerId());
		
		//Get should force regenration
		$this->regenerate = $this->getRequestParameter(self::REGENERATE_PARAM_NAME);
		
		//Get config params
		try 
		{
			$playkitConfig = kConf::get('playkit-js');
			if (array_key_exists('internal_bundler_url', $playkitConfig))
				$this->bundlerUrl = rtrim($playkitConfig['internal_bundler_url']);
			
			if (array_key_exists('playkit_js_sources_path', $playkitConfig))
				$this->sourcesPath = rtrim($playkitConfig['playkit_js_sources_path']);
			
			if (array_key_exists('play_kit_js_cache_version', $playkitConfig))
				$this->cacheVersion = rtrim($playkitConfig['play_kit_js_cache_version']);
			
			if (array_key_exists('play_kit_js_cache_version', $playkitConfig))
				$this->sourceMapLoader = rtrim($playkitConfig['playkit_js_source_map_loader']);
			
			
		} 
		catch (Exception $ex) 
		{
			KExternalErrors::dieError(KExternalErrors::INTERNAL_SERVER_ERROR);
		}
		
		$this->bundleConfig = json_decode($confVars, true);
		$this->mergeVersionsParamIntoConfig();
		if (!$this->bundleConfig) {
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, "unable to resolve bundle config");
		}
		$this->setLatestOrBetaVersionNumber();
		
		$this->setBundleName();
	}
	
	private function setBundleName()
	{
		//sort bundle config by key
		ksort($this->bundleConfig);
		
		//create base64 bundle name from json config
		$config_str = json_encode($this->bundleConfig);
		$this->bundle_name = base64_encode($config_str);
		if($this->cacheVersion)
			$this->bundle_name = $this->cacheVersion . "_" . $this->bundle_name;
	}
	
	
}