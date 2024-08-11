<?php

class v2Tov7Utils
{
	const V2TOV7_PARAM_NAME = 'v2tov7';
	const FLASHVARS_PARAM_NAME = 'flashvars';
	const SHOULD_TRANSLATE_PLUGINS = self::V2TOV7_PARAM_NAME ."translate";

	static private function getV7PluginInfo($v2PluginName): array
	{
		KalturaLog::log("Searching for " . $v2PluginName . " " . strlen($v2PluginName));
		$translation = self::v2toV7PluginMap();
		if(isset($translation[$v2PluginName]))
		{
			$v7PluginInfo = $translation[$v2PluginName];
			KalturaLog::log("Found " . print_r($v7PluginInfo, true));
			return $v7PluginInfo;
		}
		else
		{
			throw new Exception ($v2PluginName);
		}
	}

	static function addV2toV7plugins($flashvars, &$bundleConfig, &$playerConfig)
	{
		if(!$flashvars)
		{
			return;
		}
		$unHandledPlugins = array();

		//Merge v7 config
		foreach ($flashvars as $key => $value)
		{
			if(self::isVarPlugin($key))
			{
				//get plugin name
				KalturaLog::log("V2 to V7 adding plugin: " . $key. " value:" . $value);
				if ($value) {
					if (!$bundleConfig) {
						$bundleConfig = [];
					}
					$key = trim(trim($key, '"'), "'");
					$v2PluginName = explode(".", $key);
					try
					{
						$v7PluginName = self::getV7PluginInfo($v2PluginName[0]);
						$bundleConfig = array_merge($bundleConfig, [$v7PluginName[0] => "{latest}"]);
						$v7PluginConfig = $v7PluginName[1];
						if (!isset($playerConfig->plugins)) {
							$playerConfig->plugins = new stdClass();
						}
						if (!isset($playerConfig->plugins->$v7PluginConfig)) {
							$playerConfig->plugins->$v7PluginConfig = new stdClass();
						}
					}
					catch (Exception $e){
						$unHandledPlugins[] = $e->getMessage();
					}
				}
			}
		}
		if(sizeof($unHandledPlugins))
		{
			throw new Exception ("Unhandled plugins: " . implode(", ", $unHandledPlugins));
		}
	}

	static function addV2toV7config($config, $flashvars, $uiconfId)
	{
		//Merge v7 config
		if (!isset($config["provider"])) {
			$config["provider"] = new stdClass();
		}
		$unHandledVars = array();
		$config["uiconf_id"] = $uiconfId;
		if($flashvars)
		{
			foreach ($flashvars as $key => $value) {
				if(self::isVarPlugin($key))
				{
					continue;
				}
				$key = trim(trim($key, '"'), "'");
				$providerParams = ["partnerId", "uiconf_id", "entry_id", "cache_st", "wid", "ks", "autoPlay", "playlistAPI.autoContinue"];
				if (in_array($key, $providerParams)) {
					if ($value) {
						KalturaLog::log("V2toV7 adding {$key}: " . $value);
						$config["provider"]->$key = $value;
					}
				}
				else{
					$unHandledVars[] = $key;
				}
			}
			if(sizeof($unHandledVars))
			{
				throw new Exception("UnHandled params:" . implode(",",$unHandledVars));
			}
		}

		return $config;
	}

	private static function isVarPlugin($varKeyName)
	{
		return str_contains($varKeyName, ".plugin");
	}

	private static function v2toV7PluginMap()
	{
		return
			["info" => ["playkitscreen", "playkit-js-info"],
			"quiz"          => ["playkit-ivq", "ivq"],
			"moderation"    => ["playkit-moderation", "playkit-js-moderation"],
			"playlistAPI"   => ["playkit-playlist", "playlist"],
			"liveStatus"    => ["playkit-kaltura-live","kaltura-live"],
			"related"       => ["playkit-related", "related"],
			"dualScreen"    => ["playkit-dual-screen", "dualscreen"],
			"video360"      => ["playkit-vr" ,"vr"],
			"raptMedia"     => ["rapt", "rapt"],
			"transcript"    => ["playkit-transcript", "playkit-js-transcript"],
			"qna"           => ["playkit-qna", "qna"],
			"bumper"        => [ "playkit-bumper" , "bumper" ],
			"infoScreen" => ["playkit-info", "playkit-js-info"]];
	}

	public static function getBundledFacade()
	{
		//build key based on version
		$facadeVersion = kConf::getArrayValue('v2tov7FacadeVersion','playkit-js');
		$facadeVersion .= "/v2tov7Facade.js";

		//try get value from local memcache
		$cacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PLAYKIT_JS);
		$bundledFacade = $cacheStore->get($facadeVersion);
		if(strlen($bundledFacade))
		{
			return $bundledFacade;
		}

		//if not local - get it from remote location
		$remoteUrl = kConf::getArrayValue('v2tov7FacadeRemoteUrl','playkit-js');
		$remoteUrl .= '/' . $facadeVersion;

		$curlWrapper = new KCurlWrapper();

		$content = $curlWrapper->exec($remoteUrl,null, null, true);
		if(KCurlHeaderResponse::isError($curlWrapper->getHttpCode()))
		{
			throw new Exception ('Cannot find V2 to V7 facade in the following URL: ' . $remoteUrl . "Error code:" . $curlWrapper->getHttpCode());
		}

		//store in local cache for next time
		$cacheStore->set($facadeVersion,$content);
		return $content;
	}
}