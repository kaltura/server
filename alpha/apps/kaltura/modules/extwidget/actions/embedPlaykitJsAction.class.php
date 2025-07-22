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
	const PLAYLIST_ID_PARAM_NAME = "playlist_id";
	const EXTERNAL_SOURCE_PARAM_NAME = "external_source";
	const EMBED_FACTORY_PARAM_NAME = "embed_factory";
	const KS_PARAM_NAME = "ks";
	const CONFIG_PARAM_NAME = "config";
	const REGENERATE_PARAM_NAME = "regenerate";
	const IFRAME_EMBED_PARAM_NAME = "iframeembed";
	const IFRAME_EMBED_TYPE = "iframeEmbedType";
	const AUTO_EMBED_PARAM_NAME = "autoembed";
	const INCLUDE_SOURCE_MAP_PARAM_NAME = 'includeSourceMap';
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
	const NO_UICONF_FOR_KALTURA_DATA = '1.9.0';
	const RAPT = "rapt";

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
	private $includeSourceMap = 'false';
	private $uiConfTags = array(self::PLAYER_V3_VERSIONS_TAG);

	public function execute()
	{
	    // Return 404 in case of error to avoid CDN caching
		KExternalErrors::setResponseErrorCode(KExternalErrors::HTTP_STATUS_NOT_FOUND);

		$this->initMembers();

		$bundleContent = $this->bundleCache->get($this->bundle_name);
		$i18nContent = $this->bundleCache->get($this->bundle_i18n_name);
		$extraModulesNames = unserialize($this->bundleCache->get($this->bundle_extra_modules_names));
		KalturaLog::debug("Fetch bundle content from cache for key [{$this->bundle_name}], result: [" . !empty($bundleContent) . "]");

		if (!$bundleContent || $this->regenerate)
		{
			list($bundleContent, $i18nContent, $extraModulesNames) = kLock::runLocked($this->bundle_name, array("embedPlaykitJsAction", "buildBundleLocked"), array($this), 2, 30);
		}

		$lastModified = $this->getLastModified($bundleContent);

		//Format bundle content
		$bundleContent = $this->formatBundleContent($bundleContent, $i18nContent, $extraModulesNames);

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
				$i18nContent = $context->bundleCache->get($context->bundle_i18n_name);
				$extraModulesNames = unserialize($context->bundleCache->get($context->bundle_extra_modules_names));
				return array($bundleContent, $i18nContent, $extraModulesNames);
			}
		}

		//build bundle and save in memcache
		$config = str_replace("\"", "'", json_encode($context->bundleConfig));
		if(!$config)
		{
			KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config . " wrong config object");
		}

		$url = $context->bundlerUrl . '/build?config=' . base64_encode($config) .
			'&name=' . $context->bundle_name .
			'&source=' . base64_encode($context->sourcesPath) .
			'&includeSourceMap=' . $context->includeSourceMap;
		
		$content = KCurlWrapper::getContent($url, array('Content-Type: application/json'), true);

		if (!$content)
		{
			KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config . " failed to get content from bundle builder");
		}

		$content = json_decode($content, true);
        if (isset($content['status'])) {
            if ($content['status'] != 0) {
                $message = $content['message'];
                KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config . ". " . $message);
            } else {
                $content = $content['payload'];
            }
        } else {
            if (!$content || !$content['bundle']) {
                KExternalErrors::dieError(KExternalErrors::BUNDLE_CREATION_FAILED, $config . " bundle created with wrong content");
            }
        }
		
		$bundleContent = time() . "," . base64_decode($content['bundle']);
		$bundleSaved =  $context->bundleCache->set($context->bundle_name, $bundleContent);
		
		$sourceMapContent = base64_decode($content['sourceMap']);
		$context->sourceMapsCache->set($context->bundle_name, $sourceMapContent);
		
		$i18nContent = isset($content['i18n']) ? base64_decode($content['i18n']) : "";
		$context->bundleCache->set($context->bundle_i18n_name, $i18nContent);
		
		$extraModules = isset($content['extraModules']) ? $content['extraModules'] : array();
		$extraModulesNames = self::getExtraModuleNames($extraModules);
		$context->bundleCache->set($context->bundle_extra_modules_names, serialize($extraModulesNames));
		if(!$bundleSaved)
		{
			KalturaLog::log("Error - failed to save bundle content in cache for config [".$config."]");
		}

		return array($bundleContent, $i18nContent, $extraModulesNames);
	}
	
	private static function getExtraModuleNames($extraModules = array())
	{
		$extraModuleNames = array();
		foreach($extraModules as $extraModule)
		{
			if(!$extraModule['name'])
			{
				continue;
			}
			$extraModuleNames[] = $extraModule['name'];
		}
		
		return $extraModuleNames;
	}

	private function formatBundleContent($bundleContent, $i18nContent, $extraModulesNames = null)
	{
		$bundleContentParts = explode(",", $bundleContent, 2);
		$bundleContent = $this->appendConfig($bundleContentParts[1], $i18nContent, $extraModulesNames);
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
			//Embed factory is only relevant in dynamic embed
			if ($this->getRequestParameter(self::EMBED_FACTORY_PARAM_NAME, false))
			{
				$configAsJson = $this->getPlayerConfigAsJson($i18nContent, $extraModulesNames);
				$bundleContent = 'window.KalturaPlayers=(window.KalturaPlayers||{});' .
								 "\nwindow.KalturaPlayers[$this->uiconfId]={};" .
								 "\nwindow.KalturaPlayers[$this->uiconfId]['config'] = $configAsJson;" .
								 "\nwindow.KalturaPlayers[$this->uiconfId]['lib'] = (() =>{
								 	$bundleContentParts[1]
									return KalturaPlayer;
									})()";
			}
		}

		$protocol = infraRequestUtils::getProtocol();
		$host = myPartnerUtils::getCdnHost($this->partnerId, $protocol, 'serviceUrl');
		$sourceMapLoaderURL = "$host/$this->sourceMapLoader/path/$this->bundle_name";
		$bundleContent = str_replace("//# sourceMappingURL=$this->bundle_name.min.js.map", "//# sourceMappingURL=$sourceMapLoaderURL", $bundleContent);

		return $bundleContent;
	}

	private function addUiConfData($uiConfData)
	{
		$uiConfData->uiConfData = new stdClass();
		$uiConfData->uiConfData->width = $this->uiConf->getWidth();
		$uiConfData->uiConfData->height = $this->uiConf->getHeight();
		$uiConfData->uiConfData->name = $this->uiConf->getName();
	}

	private function getPlayerConfigAsJson($i18nContent, $extraModulesNames = null)
	{
		$uiConf = $this->playerConfig;
		$this->mergeEnvConfig($uiConf);
		$this->mergeI18nConfig($uiConf, $i18nContent);
		$this->mergeExtraModuleNames($uiConf, $extraModulesNames);
		$this->addUiConfData($uiConf);
		return json_encode($uiConf);

	}

	private function appendConfig($content, $i18nContent, $extraModulesNames = null)
	{
		$uiConfJson = $this->getPlayerConfigAsJson($i18nContent, $extraModulesNames);

		if ($uiConfJson === false)
		{
			KExternalErrors::dieError(KExternalErrors::INVALID_PARAMETER, "Invalid config object");
		}
		$confNS = "window.__kalturaplayerdata";
		$content .= "
		$confNS = ($confNS || {});
		";

		$kalturaPlayerVersion = null;
		if (isset($this->bundleConfig[self::KALTURA_OVP_PLAYER]) ) {
			$kalturaPlayerVersion = $this->bundleConfig[self::KALTURA_OVP_PLAYER];
		} else if (isset($this->bundleConfig[self::KALTURA_TV_PLAYER])) {
			$kalturaPlayerVersion = $this->bundleConfig[self::KALTURA_TV_PLAYER];
		}

		if (!is_null($kalturaPlayerVersion) && version_compare($kalturaPlayerVersion, self::NO_UICONF_FOR_KALTURA_DATA) >= 0) {
			$content .= "$confNS=$uiConfJson;";
		} else {
			$content .= "$confNS.UIConf = ($confNS.UIConf||{}); $confNS.UIConf[\"" . $this->uiconfId . "\"]=$uiConfJson;";
		}
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

		//todo - add unisphereLoaderUrl
		$uiConf->provider->unisphereLoaderUrl =
			MicroServiceUnisphereLoader::buildServiceUrl(
				MicroServiceUnisphereLoader::$host,
				MicroServiceUnisphereLoader::$service,
				false);
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
	
	private function mergeExtraModuleNames($uiConf, $extraModulesNames)
	{
		if(!$extraModulesNames || !count($extraModulesNames))
		{
			return;
		}
		
		if(!property_exists($uiConf, 'plugins'))
		{
			$uiConf->plugins = new stdClass();
		}
		
		foreach($extraModulesNames as $extraModulesName)
		{
			$uiConf->plugins->$extraModulesName = new stdClass();
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
		$serviceUrl = myPartnerUtils::getCdnHost($this->partnerId, $protocol, 'serviceUrl').'/api_v3';
		// Default Kaltura CDN url:
		$cdnUrl = myPartnerUtils::getCdnHost($this->partnerId, $protocol);
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
			//no cdn caching for iframeEmbed
			$max_age = 0;
			header("Content-Type: text/html");
		}
		else
		{
			header("Content-Type: text/javascript");
		}

		header("Etag: " . $this->getOutputHash($content));

		// always set cross origin headers:
		header("Access-Control-Allow-Origin: *");

		// prevent indexing of direct player urls
		header('X-Robots-Tag: noindex');

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
		$playlist_id = $this->getRequestParameter(self::PLAYLIST_ID_PARAM_NAME);
		$external_source = $this->getRequestParameter(self::EXTERNAL_SOURCE_PARAM_NAME);
		$iframe_embed_type = $this->getRequestParameter(self::IFRAME_EMBED_TYPE);
		$loadContentMethod = "";
		if (!is_null($entry_id)) {
			$loadContentMethod = "kalturaPlayer.loadMedia({\"entryId\":\"$entry_id\"});";
		} elseif (!is_null($playlist_id)) {
			$loadContentMethod = "kalturaPlayer.loadPlaylist({\"playlistId\":\"$playlist_id\"});";
			if($iframe_embed_type === self::RAPT) {
				$loadContentMethod = "kalturaPlayer.loadMedia({\"playlistId\":\"$playlist_id\"});";
			}
		} elseif (!is_null($external_source)) {
			$loadContentMethod = "kalturaPlayer.setMedia({\"sources\":$external_source});";
		}
		else {
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, "One of the following params must be defined: entry_id, playlist_id, external_source");
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
		
		// Player setup
		$kalturaPlayer = "KalturaPlayer.setup(config);";
		if($iframe_embed_type === self::RAPT)
		{
			$kalturaPlayer = "PathKalturaPlayer.setup(config);";
		}
		
		// Player content loading
		$loadPlayerJs = "
			var kalturaPlayer = $kalturaPlayer;
			$loadContentMethod
		";
		
		$v2tov7ConfigJs = '';
		if($this->getRequestParameter(v2RedirectUtils::V2REDIRECT_PARAM_NAME))
		{
			$v2ToV7config = v2RedirectUtils::addV2toV7config($this->getRequestParameter(v2RedirectUtils::FLASHVARS_PARAM_NAME), $this->uiconfId);
			$v2tov7ConfigJs = 'config = window.__buildV7Config('.JSON_encode($v2ToV7config).',config)';
			if ($this->getRequestParameter(self::AUTO_EMBED_PARAM_NAME)) {
				$originalLoadPlayerJs = $loadPlayerJs;
				$loadPlayerJs = "
                  if (!document.getElementById(config.targetId)) {
                    document.write(`<div id='\${config.targetId}' style='width:560px; height:395px;'></div>`);
                  }
                  $originalLoadPlayerJs
                  ";
			}
		}

		$autoEmbedCode = "
		try {
			var config=$config;
			$v2tov7ConfigJs
			$loadPlayerJs
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
			 	<script type = "text/javascript" > window.originalRequestReferrer = "' . @$_SERVER['HTTP_REFERER'] . '"</script >
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
		$productVersion = isset($last_uiconf_content) ? $this->getProductVersionFromUiConf($last_uiconf_content->getConfVars()) : null;
		return array($last_uiconf_config, $productVersion);
	}

	private function getConfigByVersion($version)
	{
		$config = array();
		$corePackages = array();
		$productVersion = null;
		$loadVersionMapFromKConf = kConf::get("loadFromKConf", kConfMapNames::EMBED_PLAYKIT, null);
		foreach ($this->uiConfTags as $tag)
		{
			$loadVersionTagMapFromKConf = kConf::get("loadFromKConf_".$tag, kConfMapNames::EMBED_PLAYKIT, null);
			if($loadVersionMapFromKConf || $loadVersionTagMapFromKConf)
			{
				list($versionConfig,$tagVersionNumber) = $this->getVersionMap($tag, $version);
			}
			else
			{
				$versionUiConfs = uiConfPeer::getUiconfByTagAndVersion($tag, $version);
				list($versionLastUiConf,$tagVersionNumber) = $this->getLastConfig($versionUiConfs);
				$versionConfig = json_decode($versionLastUiConf, true);
			}

			if (is_array($versionConfig)) {
				$config = array_merge($config, $versionConfig);
			}
			if ($tag === self::PLAYER_V3_VERSIONS_TAG) {
				$corePackages = $versionConfig;
				$productVersion = $tagVersionNumber;
			}
		}
		return array($config,$productVersion,$corePackages);
	}

	private function getVersionMap($tag, $version)
	{
		$versionLastUiConf = kConf::get($tag."_".$version, kConfMapNames::EMBED_PLAYKIT, array());
		$tagVersionNumber = kConf::get($tag."_".$version."_productVersion", kConfMapNames::EMBED_PLAYKIT, "");
		return array($versionLastUiConf, $tagVersionNumber);
	}

	private function maybeAddAnalyticsPlugins()
	{
		$ovpPlayerConfig = isset($this->bundleConfig[self::KALTURA_OVP_PLAYER]) ? $this->bundleConfig[self::KALTURA_OVP_PLAYER] : '';
		$tvPlayerConfig = isset($this->bundleConfig[self::KALTURA_TV_PLAYER]) ? $this->bundleConfig[self::KALTURA_TV_PLAYER] : '';
		if (!isset($this->bundleConfig[self::PLAYKIT_KAVA]) && ($ovpPlayerConfig || $tvPlayerConfig))
		{
			$playerVersion = $ovpPlayerConfig ? $ovpPlayerConfig : $tvPlayerConfig;
			list($latestVersionMap) = $this->getConfigByVersion("latest");
			list($betaVersionMap) = $this->getConfigByVersion("beta");
			$latestVersion = isset($latestVersionMap[self::KALTURA_OVP_PLAYER]) ? $latestVersionMap[self::KALTURA_OVP_PLAYER] : null;
			$betaVersion = isset($betaVersionMap[self::KALTURA_OVP_PLAYER]) ? $betaVersionMap[self::KALTURA_OVP_PLAYER] : null;

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
			}
			// For specific version >= 0.56.0
			else if (version_compare($playerVersion, self::NO_ANALYTICS_PLAYER_VERSION) >= 0 &&
					!is_null($latestVersionMap))
			{
				$this->bundleConfig[self::PLAYKIT_KAVA] = $latestVersionMap[self::PLAYKIT_KAVA];
				if ($tvPlayerConfig)
				{
					$this->bundleConfig[self::PLAYKIT_OTT_ANALYTICS] = $latestVersionMap[self::PLAYKIT_OTT_ANALYTICS];
				}
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
		$packageVersion = null;

		if ($isLatestVersionRequired || $isBetaVersionRequired || $isCanaryVersionRequired) {

			list($latestVersionMap, $latestProductVersion, $corePackages) = $this->getConfigByVersion("latest");
			list($betaVersionMap, $betaProductVersion) = $this->getConfigByVersion("beta");
			list($canaryVersionMap, $canaryProductVersion) = $this->getConfigByVersion("canary");

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

				if ($corePackages != null && isset($corePackages[$key])) {
					if (is_null($packageVersion)) {
						$packageVersion = $val;
					} else if ($packageVersion != $val) {
						$isAllPackagesSameVersion = false;
					}
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


		} else {
			$productVersion = isset($this->uiConf) ? $this->getProductVersionFromUiConf($this->uiConf->getHtml5Url()) : null;
			if($productVersion != null)
			{
				$this->setProductVersion($this->playerConfig, $productVersion);
			}
		}
	}

	private function getProductVersionFromUiConf($productVersionString)
	{
		$productVersionJson = isset($productVersionString) ? json_decode($productVersionString) : null;
		$productVersion = $productVersionJson ? $productVersionJson->version : null;
		return $productVersion;
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
			KExternalErrors::dieError(KExternalErrors::MISSING_BUNDLE_CONFIGURATION, "" . $this->uiconfId);
		}

		//Get partner ID from QS or from UI conf
		$this->partnerId = $this->getRequestParameter(self::PARTNER_ID_PARAM_NAME, $this->uiConf->getPartnerId());
		$this->partner = PartnerPeer::retrieveByPK($this->partnerId);
		if (!$this->partner)
			KExternalErrors::dieError(KExternalErrors::PARTNER_NOT_FOUND);

		//Get should force regenration
		$this->regenerate = $this->getRequestParameter(self::REGENERATE_PARAM_NAME);
		
		//Should we include player source map in the request result
		$this->includeSourceMap = $this->getRequestParameter(self::INCLUDE_SOURCE_MAP_PARAM_NAME, 'false');

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

		$confVarsArr = json_decode($confVars, true);
		$this->bundleConfig = $confVarsArr;
		if (isset($confVarsArr[self::VERSIONS_PARAM_NAME])) {
			$this->bundleConfig = $confVarsArr[self::VERSIONS_PARAM_NAME];
		}
		if (isset($confVarsArr[self::LANGS_PARAM_NAME])) {
			$this->uiConfLangs = $confVarsArr[self::LANGS_PARAM_NAME];
		}

		$this->mergeVersionsParamIntoConfig();

		if($this->getRequestParameter(v2RedirectUtils::V2REDIRECT_PARAM_NAME))
		{
			$this->bundleConfig[v2RedirectUtils::SCRIPT_PLUGIN_NAME] =
				kConf::getArrayValue('v2RedirectPluginVersion',
					'playkit-js', 'local', v2RedirectUtils::SCRIPT_PLUGIN_VERSION);
			if($this->getRequestParameter(v2RedirectUtils::SHOULD_TRANSLATE_PLUGINS))
			{
				v2RedirectUtils::addV2toV7plugins(
					$this->getRequestParameter(v2RedirectUtils::FLASHVARS_PARAM_NAME),
					$this->bundleConfig,
					$this->playerConfig);
			}
		}

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
		$this->bundle_extra_modules_names = $this->bundle_name . "_extramodules";
	}

	public function getRequestParameter($name, $default = null)
	{
		$returnValue = parent::getRequestParameter($name, $default);
		return $returnValue ? $returnValue : $default;
	}
}
