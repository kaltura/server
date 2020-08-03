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
	const LANGS_PARAM_NAME = "langs";
	const ENTRY_ID_PARAM_NAME = "entry_id";
	const KS_PARAM_NAME = "ks";
	const CONFIG_PARAM_NAME = "config";
	const REGENERATE_PARAM_NAME = "regenerate";
	const IFRAME_EMBED_PARAM_NAME = "iframeembed";
	const AUTO_EMBED_PARAM_NAME = "autoembed";
	const LATEST = "{latest}";
	const BETA = "{beta}";
	const CANARY = "{canary}";
	const PLAYER_V3_VERSIONS_TAG = 'playerV3Versions';
	const EMBED_PLAYKIT_UICONF_TAGS_KEY_NAME = 'uiConfTags';
	const PLAYKIT_KAVA = 'playkit-kava';
	const PLAYKIT_OTT_ANALYTICS = 'playkit-ott-analytics';
	const KALTURA_OVP_PLAYER = 'kaltura-ovp-player';
	const KALTURA_TV_PLAYER = 'kaltura-tv-player';
	const NO_ANALYTICS_PLAYER_VERSION = '0.56.0';

	private $bundleCache = null;
	private $sourceMapsCache = null;
	private $eTagHash = null;
	private $uiconfId = null;
	private $uiConf = null;
	private $partnerId = null;
	private $partner = null;
	private $bundle_name = null;
	private $bundle_i18n_name = null;
	private $bundlerUrl = null;
	private $sourcesPath = null;
	private $bundleConfig = null;
	private $uiConfLangs = null;
	private $sourceMapLoader = null;
	private $cacheVersion = null;
	private $playKitVersion = null;
	private $playerConfig = null;
	private $uiConfUpdatedAt = null;
	private $regenerate = false;
	private $uiConfTags = array(self::PLAYER_V3_VERSIONS_TAG);
	private $bundleConfigUpdated = false;
	private $confVarsArr = null;

	public function execute()
	{
		$this->initMembers();

		$bundleContent = $this->bundleCache->get($this->bundle_name);
		$i18nContent = $this->bundleCache->get($this->bundle_i18n_name);

		if (!$bundleContent || $this->regenerate)
		{
			list($bundleContent, $i18nContent) = kLock::runLocked($this->bundle_name, array("embedPlaykitJsAction", "buildBundleLocked"), array($this));
		}

		$lastModified = $this->getLastModified($bundleContent);

		//Format bundle contnet
		$bundleContent = $this->formatBundleContent($bundleContent, $i18nContent);

		// send cache headers
		$this->sendHeaders($bundleContent, $lastModified);

		echo($bundleContent);

		KExternalErrors::dieGracefully();
	}

	public static function buildBundleLocked($context)
	{
		// Save to the uiconf if the bundle config has been updated with the analytics plugins
		if ($context->bundleConfigUpdated)
		{
			if (isset($context->confVarsArr[self::VERSIONS_PARAM_NAME]))
			{
				$context->confVarsArr[self::VERSIONS_PARAM_NAME] = $context->bundleConfig;
			}
			else
			{
				$context->confVarsArr = $context->bundleConfig;
			}
			$context->uiConf->setConfVars(json_encode($context->confVarsArr));
			$context->uiConf->save();
		}
		
		//if bundle not exists or explicitly should be regenerated build it
		if(!$context->regenerate)
		{
			$bundleContent = $context->bundleCache->get($context->bundle_name);
			if ($bundleContent)
			{
				$i18nContent = $context->bundleCache->get($context->bundle_i18n_name);
				return array($bundleContent, $i18nContent);
			}
		}

		//build bundle and save in memcache
		$config = str_replace("\"", "'", json_encode($context->bundleConfig));
		if(!$config)
		{
			KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config . " wrong config object");
		}

		$url = $context->bundlerUrl . "/build?config=" . base64_encode($config) . "&name=" . $context->bundle_name . "&source=" . base64_encode($context->sourcesPath);
		$content = KCurlWrapper::getContent($url, array('Content-Type: application/json'), true);

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
		$i18nContent = isset($content['i18n']) ? base64_decode($content['i18n']) : "";
		$context->bundleCache->set($context->bundle_i18n_name, $i18nContent);
		if(!$bundleSaved)
		{
			KalturaLog::log("Error - failed to save bundle content in cache for config [".$config."]");
		}

		return array($bundleContent, $i18nContent);
	}

	private function formatBundleContent($bundleContent, $i18nContent)
	{
		$bundleContentParts = explode(",", $bundleContent, 2);
		$bundleContent = $this->appendConfig($bundleContentParts[1], $i18nContent);
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

	private function appendConfig($content, $i18nContent)
	{
		$uiConf = $this->playerConfig;
		$this->mergeEnvConfig($uiConf);
		$this->mergeI18nConfig($uiConf, $i18nContent);
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

	private function mergeEnvConfig($uiConf)
	{
		if (!property_exists($uiConf, "provider"))
		{
			$uiConf->provider = new stdClass();
			$uiConf->provider->env = new stdClass();
		}

		if (!property_exists($uiConf->provider, "env"))
		{
			$uiConf->provider->env = new stdClass();
		}
		foreach ($this->getEnvConfig() as $key => $value) {
			if (!(property_exists($uiConf->provider->env, $key) && $uiConf->provider->env->$key))
				$uiConf->provider->env->$key = $value;
		}
	}

	private function mergeI18nConfig($uiConf, $i18nContent)
	{
		$i18nArr = json_decode($i18nContent, true);
		if ($i18nArr)
		{
			$i18nArr = $this->filterI18nLangs($i18nArr);
			if (!property_exists($uiConf, "ui"))
			{
				$uiConf->ui = new stdClass();
			}

			if (!property_exists($uiConf->ui, "translations"))
			{
				$uiConf->ui->translations = new stdClass();
			}
			$uiConfI18nArr = json_decode(json_encode($uiConf->ui->translations), true);
			$uiConf->ui->translations = (object) $this->arrayMergeRecursive($i18nArr, $uiConfI18nArr);
		}
	}

	private function filterI18nLangs($i18nArr)
	{
		$langsParam = $this->getRequestParameter(self::LANGS_PARAM_NAME);
		$langArr = isset($langsParam) ? explode(",", $langsParam) : (!empty($this->uiConfLangs) ? $this->uiConfLangs : array("en"));
		$partialI18nArr = array();
		foreach ($langArr as $lang) {
			if (isset($i18nArr[$lang]))
			{
				$partialI18nArr[$lang] = $i18nArr[$lang];
			}
		}
		return $partialI18nArr;
	}

	/*
	* Recursive function that merges two associative arrays
	* - Unlike array_merge_recursive, a differing value for a key overwrites that key rather than creating an array with both values
	* - A scalar value will overwrite an array value
	*/
	private function arrayMergeRecursive( $arr1, $arr2 )
	{
		$keys = array_keys( $arr2 );
		foreach( $keys as $key ) {
			if( isset( $arr1[$key] )
				&& is_array( $arr1[$key] )
				&& is_array( $arr2[$key] )
			) {
				$arr1[$key] = $this->arrayMergeRecursive( $arr1[$key], $arr2[$key] );
			} else {
				$arr1[$key] = $arr2[$key];
			}
		}
		return $arr1;
	}

	private function setProductVersion($uiConf, $productVersion)
	{
		if(isset($productVersion)){
			if (!property_exists($uiConf, "productVersion"))
			{
				$uiConf->productVersion = new stdClass();
			}
			$uiConf->productVersion = $productVersion;
		}
	}

	private function getEnvConfig()
	{
		$tags = $this->uiConf->getTags();
		$publisherEnvType = $this->partner->getPublisherEnvironmentType();
		if (strpos($tags, "ott") || $publisherEnvType === PublisherEnvironmentType::OTT) {
			return $this->getOttEnvConfig();
		} else {
			return $this->getOvpEnvConfig();
		}
	}

	private function getOttEnvConfig()
	{
		$ottEnvConfig = json_decode($this->partner->getOttEnvironmentUrl(), true);
		if (!is_array($ottEnvConfig)) {
			$ottEnvConfig = array();
		}
		return $ottEnvConfig;
	}

	private function getOvpEnvConfig()
	{
		$ovpEnvConfig = json_decode($this->partner->getOvpEnvironmentUrl(), true);
		if (!is_array($ovpEnvConfig)) {
			$ovpEnvConfig = array();
		}
		return array_merge($this->getDefaultOvpEnvConfig(), $ovpEnvConfig);
	}

	private function getDefaultOvpEnvConfig()
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
			KExternalErrors::dieGracefully();
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

		if (!isset($config["provider"]))
		{
			$config["provider"] = new stdClass();
		}

		$config["provider"]->partnerId = $this->partnerId;
		$config["provider"]->uiConfId = $this->uiconfId;

		$ks = $this->getRequestParameter(self::KS_PARAM_NAME);

		if ($ks)
		{
			$config["provider"]->ks = $ks;
		}

		$config["targetId"] = $targetId;

		$config = json_encode($config);
		if ($config === false)
		{
			KExternalErrors::dieError(KExternalErrors::INVALID_PARAMETER, "Invalid config object");
		}

		$autoEmbedCode = "
		try {
			var kalturaPlayer = KalturaPlayer.setup($config);
			kalturaPlayer.loadMedia({entryId: \"" . $entry_id . "\"});
		} catch (e) {
			console.error(e.message);
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

	private function getLastConfig($uiConfs) {
		$uiconfs_content = isset($uiConfs) ? array_values($uiConfs) : null;
		$last_uiconf_content = (is_array($uiconfs_content) && reset($uiconfs_content)) ? reset($uiconfs_content) : null;
		$last_uiconf_config = isset($last_uiconf_content) ? $last_uiconf_content->getConfig() : '';
		$productVersionJson = isset($last_uiconf_content) ? json_decode($last_uiconf_content->getConfVars()) : null;
		$productVersion = $productVersionJson ? $productVersionJson->version : null;
		return array($last_uiconf_config, $productVersion);
	}

	private function getConfigByVersion($version){
		$config = array();
		foreach ($this->uiConfTags as $tag) {
			$versionUiConfs = uiConfPeer::getUiconfByTagAndVersion($tag, $version);
			list($versionLastUiConf,$tagVersionNumber) = $this->getLastConfig($versionUiConfs);
			$versionConfig = json_decode($versionLastUiConf, true);
			if (is_array($versionConfig)) {
				$config = array_merge($config, $versionConfig);
			}
			if(!isset($productVersion)) {
				$productVersion = $tagVersionNumber;
			}
		}
		return array($config,$productVersion);
	}

	private function maybeAddAnalyticsPlugins()
	{
		$ovpPlayerConfig = isset($this->bundleConfig[self::KALTURA_OVP_PLAYER]) ? $this->bundleConfig[self::KALTURA_OVP_PLAYER] : '';
		$tvPlayerConfig = isset($this->bundleConfig[self::KALTURA_TV_PLAYER]) ? $this->bundleConfig[self::KALTURA_TV_PLAYER] : '';
		if (!isset($this->bundleConfig[self::PLAYKIT_KAVA]) && ($ovpPlayerConfig || $tvPlayerConfig))
		{
			$playerVersion = $ovpPlayerConfig ? $ovpPlayerConfig : $tvPlayerConfig;
			$latestVersionMap = $this->getConfigByVersion("latest")[0];
			$betaVersionMap = $this->getConfigByVersion("beta")[0];
			$latestVersion = $latestVersionMap[self::KALTURA_OVP_PLAYER];
			$betaVersion = $betaVersionMap[self::KALTURA_OVP_PLAYER];

			// For player latest/beta >= 0.56.0 or canary
			if (($playerVersion == self::LATEST && version_compare($latestVersion, self::NO_ANALYTICS_PLAYER_VERSION) >= 0) ||
				($playerVersion == self::BETA && version_compare($betaVersion, self::NO_ANALYTICS_PLAYER_VERSION) >= 0) ||
				$playerVersion == self::CANARY)
			{
				$this->bundleConfig[self::PLAYKIT_KAVA] = $playerVersion;
				if ($tvPlayerConfig)
				{
					$this->bundleConfig[self::PLAYKIT_OTT_ANALYTICS] = $playerVersion;
				}
				$this->bundleConfigUpdated = true;
			}
			// For specific version >= 0.56.0
			else if (version_compare($playerVersion, self::NO_ANALYTICS_PLAYER_VERSION) >= 0)
			{
				$this->bundleConfig[self::PLAYKIT_KAVA] = $latestVersionMap[self::PLAYKIT_KAVA];
				if ($tvPlayerConfig)
				{
					$this->bundleConfig[self::PLAYKIT_OTT_ANALYTICS] = $latestVersionMap[self::PLAYKIT_OTT_ANALYTICS];
				}
				$this->bundleConfigUpdated = true;
			}
		}
	}

	private function setFixVersionsNumber()
	{
		//if latest/beta version required set version number in config obj
		$isLatestVersionRequired = array_search(self::LATEST, $this->bundleConfig) !== false;
		$isBetaVersionRequired = array_search(self::BETA, $this->bundleConfig) !== false;
		$isCanaryVersionRequired = array_search(self::CANARY, $this->bundleConfig) !== false;

		$isAllPackagesSameVersion = true;

		if ($isLatestVersionRequired || $isBetaVersionRequired || $isCanaryVersionRequired) {

			list($latestVersionMap, $latestProductVersion) = $this->getConfigByVersion("latest");
			list($betaVersionMap, $betaProductVersion) = $this->getConfigByVersion("beta");
			list($canaryVersionMap, $canaryProductVersion) = $this->getConfigByVersion("canary");

			//package version to compare, product version will save jut if all the versions in uiConf similar
			$packageVersion = reset( $this->bundleConfig );

			foreach ($this->bundleConfig as $key => $val)
			{
				if ($val == self::LATEST && $latestVersionMap != null && isset($latestVersionMap[$key])) {
					$this->bundleConfig[$key] = $latestVersionMap[$key];
				}

				if ($val == self::BETA && $betaVersionMap != null && isset($betaVersionMap[$key])) {
					$this->bundleConfig[$key] = $betaVersionMap[$key];
				}

				if ($val == self::CANARY && $canaryVersionMap != null && isset($canaryVersionMap[$key])) {
					$this->bundleConfig[$key] = $canaryVersionMap[$key];
				}

				if($packageVersion !== $val) {
					$isAllPackagesSameVersion = false;
				}
			}

			if($isAllPackagesSameVersion === true) {
				if($packageVersion === self::LATEST) {
					$this->setProductVersion($this->playerConfig, $latestProductVersion);
				}
				if($packageVersion === self::BETA) {
					$this->setProductVersion($this->playerConfig, $betaProductVersion);
				}
				if($packageVersion === self::CANARY) {
					$this->setProductVersion($this->playerConfig, $canaryProductVersion);
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
		$this->uiConf = uiConfPeer::retrieveByPK($this->uiconfId);
		if (!$this->uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);
		$this->playerConfig = json_decode($this->uiConf->getConfig());
		if (!$this->playerConfig) {
			$this->playerConfig = new stdClass();
		}
		$this->uiConfUpdatedAt = $this->uiConf->getUpdatedAt(null);

		//Get bundle configuration stored in conf_vars
		$confVars = $this->uiConf->getConfVars();
		if (!$confVars) {
			KExternalErrors::dieGracefully("Missing bundle configuration in uiConf, uiConfID: $this->uiconfId");
		}

		//Get partner ID from QS or from UI conf
		$this->partnerId = $this->getRequestParameter(self::PARTNER_ID_PARAM_NAME, $this->uiConf->getPartnerId());
		$this->partner = PartnerPeer::retrieveByPK($this->partnerId);
		if (!$this->partner)
			KExternalErrors::dieError(KExternalErrors::PARTNER_NOT_FOUND);

		//Get should force regenration
		$this->regenerate = $this->getRequestParameter(self::REGENERATE_PARAM_NAME);

		//Get the list of partner 0 uiconf tags for uiconfs that contain {latest} and {beta} lists
		$embedPlaykitConf = kConf::getMap(kConfMapNames::EMBED_PLAYKIT);
		if (isset($embedPlaykitConf[self::EMBED_PLAYKIT_UICONF_TAGS_KEY_NAME]))
		{
			$this->uiConfTags = $embedPlaykitConf[self::EMBED_PLAYKIT_UICONF_TAGS_KEY_NAME];
		}

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

		$this->confVarsArr = json_decode($confVars, true);
		$this->bundleConfig = $this->confVarsArr;
		if (isset($this->confVarsArr[self::VERSIONS_PARAM_NAME])) {
			$this->bundleConfig = $this->confVarsArr[self::VERSIONS_PARAM_NAME];
		}
		if (isset($this->confVarsArr[self::LANGS_PARAM_NAME])) {
			$this->uiConfLangs = $this->confVarsArr[self::LANGS_PARAM_NAME];
		}
		$this->mergeVersionsParamIntoConfig();
		if (!$this->bundleConfig) {
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, "unable to resolve bundle config");
		}

		$this->maybeAddAnalyticsPlugins();
		$this->setFixVersionsNumber();
		$this->setBundleName();
	}

	private function setBundleName()
	{
		//sort bundle config by key
		ksort($this->bundleConfig);

		//create base64 bundle name from json config
		$config_str = json_encode($this->bundleConfig);
		$this->bundle_name = md5($config_str);
		if($this->cacheVersion)
		{
			$this->bundle_name = $this->cacheVersion . "_" . $this->bundle_name;
		}
		$this->bundle_i18n_name = $this->bundle_name . "_i18n";
	}

	public function getRequestParameter($name, $default = null)
	{
		$returnValue = parent::getRequestParameter($name, $default);
		return $returnValue ? $returnValue : $default;
	}

}
