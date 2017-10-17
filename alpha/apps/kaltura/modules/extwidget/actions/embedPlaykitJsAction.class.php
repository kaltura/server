<?php

/**
 * @package Core
 * @subpackage externalWidgets
 */
class embedPlaykitJsAction extends sfAction
{
	const UI_CONF_ID_PARAM_NAME = "uiconf_id";
	const PARTNER_ID_PARAM_NAME = "partner_id";
	const ENTRY_ID_PARAM_NAME = "entry_id";
	const CONFIG_PARAM_NAME = "config";	
	const REGENERATE_PARAM_NAME = "regenerate";
	const IFRAME_EMBED_PARAM_NAME = "iframeembed";
	const AUTO_EMBED_PARAM_NAME = "autoembed";
	
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
	private $regenerate = false;
	
	public function execute()
	{
		$this->initMembers();
		
		$bundleContent = $this->bundleCache->get($this->bundle_name);
		if (!$bundleContent || $this->regenerate) 
		{
			$bundleContent = kLock::runLocked($this->bundle_name, array("embedPlaykitJsAction", "buildBundleLocked"), array($this));
		}
		
		// send cache headers
		$this->sendHeaders($bundleContent);
		
		//Format bundle contnet
		$bundleContent = $this->formatBundleContent($bundleContent);
		
		echo($bundleContent);
		
		KExternalErrors::dieGracefully();
	}
	
	public static function buildBundleLocked($context)
	{
		//if bundle not exists or explicitly should be regenerated build it
		if (!$context->bundleCache->get($context->bundle_name) || $context->regenerate) 
		{
			//build bundle and save in memcache
			$config = str_replace("\"", "'", json_encode($context->bundleConfig));
			if ($config) 
			{
				$url = $context->bundlerUrl . "/build?config=" . base64_encode($config) . "&name=" . $context->bundle_name . "&source=" . base64_encode($context->sourcesPath);
				$content = KCurlWrapper::getContent($url, array('Content-Type: application/json'));
				
				if ($content) 
				{
					try 
					{
						$content = json_decode($content, true);
						$sourceMapContent = base64_decode($content['sourceMap']);
						$bundleContent = time() . "," . base64_decode($content['bundle']);
						$context->bundleCache->set($context->bundle_name, $bundleContent);
						$context->sourceMapsCache->set($context->bundle_name, $sourceMapContent);
					} 
					catch (Exception $ex) 
					{
						KExternalErrors::dieError(KExternalErrors::INTERNAL_SERVER_ERROR);
					}
					
				} 
				else 
				{
					KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config);
				}
				
				return $bundleContent;
			} 
			else 
			{
				KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config);
			}
		}
	}
	
	private function formatBundleContent($bundleContent)
	{
		$bundleContentParts = explode(",", $bundleContent, 2);
		$bundleContent = $bundleContentParts[1];
		
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
		else
		{
			$config["plugins"] = $this->playerPlugins;
			$config = json_encode($config);	

			$plugins = "
			var kalturaPlayerPlugins = $config;
			";
			$bundleContent .= $plugins;
		}
		
		
		$protocol = infraRequestUtils::getProtocol();
		$host = myPartnerUtils::getCdnHost($this->partnerId, $protocol, 'api');
		$sourceMapLoaderURL = "$host/$this->sourceMapLoader/path/$this->bundle_name";
		$bundleContent = str_replace("//# sourceMappingURL=$this->bundle_name.min.js.map", "//# sourceMappingURL=$sourceMapLoaderURL", $bundleContent);
		
		return $bundleContent;
	}
	
	private function sendHeaders($content)
	{
		$max_age = 60 * 10;
		$contentParts = explode(",", $content, 2);
		$lastModified = $contentParts[0];
		// Support Etag and 304
		if (
			(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
				$_SERVER['HTTP_IF_MODIFIED_SINCE'] == infraRequestUtils::formatHttpTime($lastModified)) ||
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
		infraRequestUtils::sendCachingHeaders($max_age, false, $lastModified[0]);
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
		$config = $this->getRequestParameter(self::CONFIG_PARAM_NAME);		
		//enable passing nested config options
		foreach ($config as $key=>$val)
		{
			$config[$key] = json_decode($val);
		}

		$config["partnerId"] = $this->partnerId;		
		$config["uiConfId"] = $this->uiconfId;
		$config["plugins"] = $this->playerPlugins;
		
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
	
	private function setLatestOrBetaVersionNumber($confVars)
	{
		//if latest/beta version required set version number in config obj
		$isLatestVersionRequired = strpos($confVars, "{latest}") !== false;
		$isBetaVersionRequired = strpos($confVars, "{beta}") !== false;
		
		if ($isLatestVersionRequired || $isBetaVersionRequired) {
			$latestVersionsMapPath = $this->sourcesPath . "/latest.json";
			$latestVersionMap = file_exists($latestVersionsMapPath) ? json_decode(file_get_contents($latestVersionsMapPath), true) : null;
			
			$betaVersionsMapPath = $this->sourcesPath . "/beta.json";
			$betatVersionMap = file_exists($betaVersionsMapPath) ? json_decode(file_get_contents($betaVersionsMapPath), true) : null;
			
			foreach ($this->bundleConfig as $key => $val) 
			{
				if ($val == "{latest}" && $latestVersionMap != null) 
				{
					$this->bundleConfig[$key] = $latestVersionMap[$key];
				}
				
				if ($val == "{beta}" && $betatVersionMap != null) 
				{
					$this->bundleConfig[$key] = $betatVersionMap[$key];
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

		$playerConfig = json_decode($uiConf->getConfig(), true);
		$this->playerPlugins = $playerConfig["plugins"];
		
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
		$this->setLatestOrBetaVersionNumber($confVars);
		
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
