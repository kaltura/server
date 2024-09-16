<?php

class v2RedirectUtils
{
	const V2REDIRECT_PARAM_NAME = 'v2Redirect';
	const FLASHVARS_PARAM_NAME = 'flashvars';
	const SHOULD_TRANSLATE_PLUGINS = self::V2REDIRECT_PARAM_NAME .'translate';
	const SCRIPT_PLUGIN_NAME = 'playkit-player-scripts';
	const SCRIPT_PLUGIN_VERSION = '{latest}';

	static private function getV7PluginInfo($v2PluginName): array
	{
		$translation = ["info" => ["playkitscreen", "playkit-js-info"],
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
				if ($value) 
				{
					if (!$bundleConfig) 
					{
						$bundleConfig = [];
					}
					$key = trim(trim($key, '"'), "'");
					$v2PluginName = explode(".", $key);
					try
					{
						$v7PluginName = self::getV7PluginInfo($v2PluginName[0]);
						$bundleConfig = array_merge($bundleConfig, [$v7PluginName[0] => "{latest}"]);
						$v7PluginConfig = $v7PluginName[1];
						if (!isset($playerConfig->plugins)) 
						{
							$playerConfig->plugins = new stdClass();
						}
						if (!isset($playerConfig->plugins->$v7PluginConfig)) 
						{
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

	static function addV2toV7config($flashvars, $uiconfId)
	{
		$config = [];
		if($flashvars)
		{   foreach ($flashvars as $key => $value) 
			{
				$key = trim(trim($key, '"'), "'");
				$config[$key] = json_decode($value);
			}
		}
		$config["uiconf_id"] = $uiconfId;
		return $config;
	}

	private static function isVarPlugin($varKeyName)
	{
		return str_contains($varKeyName, ".plugin");
	}
}
