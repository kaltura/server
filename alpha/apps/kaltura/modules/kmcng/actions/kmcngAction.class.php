<?php
/**
 * @package    Core
 * @subpackage KMCNG
 */
class kmcngAction extends kalturaAction
{
	const SYSTEM_DEFAULT_PARTNER = 0;
	
	public function execute ( )
	{
		$kmcngParams = kConf::get('kmcng');

		if (!$kmcngParams)
		{
			KalturaLog::warning("kmcng config doesn't exist in configuration.");
			return sfView::ERROR;
		}

		// Check for forced HTTPS
		if ((!$kmcngParams["kmcng_debug_mode"]))
		{
			if ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on'))
			{
				header("Location: " . infraRequestUtils::PROTOCOL_HTTPS . "://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]);
				die();
			}
			header("Strict-Transport-Security: max-age=63072000; includeSubdomains; preload");
		}

		//disable cache
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		if (!$kmcngParams["kmcng_version"])
		{
			KalturaLog::warning("kmcng_version doesn't exist in configuration.");
			return sfView::ERROR;
		}

		$kmcngVersion = $kmcngParams["kmcng_version"];
		$baseDir =kConf::get("BASE_DIR", 'system');
		$basePath = $baseDir . "/apps/kmcng/$kmcngVersion/";
		$deployUrl = "/apps/kmcng/$kmcngVersion/";

		$path = $basePath . "index.html";
		$content = file_get_contents($path);
		if ($content === false)
		{
			KalturaLog::warning("Couldn't locate Kmcng path: $path");
			return sfView::ERROR;
		}

		$config = $this->initConfig($deployUrl, $kmcngParams);
		$config = json_encode($config);
		$config = str_replace("\\/", '/', $config);

		$content = str_replace("<base href=\"/\">", "<base href=\"/index.php/kmcng/\">", $content);

		$content = preg_replace("/src=\"(?!(http:)|(https:)|\/)/i", "src=\"{$deployUrl}", $content);
		$content = preg_replace("/href=\"(?!(http:)|(https:)|\/)/i", "href=\"{$deployUrl}", $content);

		$content = str_replace("var kmcConfig = null", "var kmcConfig = " . $config, $content);
		echo $content;
	}

	private function initConfig($deployUrl, $kmcngParams)
	{
		$this->liveAUiConf = uiConfPeer::getUiconfByTagAndVersion('livea_player', kConf::get("liveanalytics_version"));
		$this->content_uiconfs_livea = isset($this->liveAUiConf) ? array_values($this->liveAUiConf) : null;
		$this->content_uiconf_livea = (is_array($this->content_uiconfs_livea) && reset($this->content_uiconfs_livea)) ? reset($this->content_uiconfs_livea) : null;

		$this->previewUIConf = uiConfPeer::getUiconfByTagAndVersion('KMCng',  $kmcngParams["kmcng_version"]);
		$this->content_uiconfs_preview = isset($this->previewUIConf) ? array_values($this->previewUIConf) : null;
		$this->content_uiconf_preview= (is_array($this->content_uiconfs_preview) && reset($this->content_uiconfs_preview)) ? reset($this->content_uiconfs_preview) : null;

		$config = array();

		$KalturaServerConfig = array();
		$KalturaServerConfig['uri'] = kConf::get("www_host");
		$KalturaServerConfig['deployUrl'] = $deployUrl;
		$KalturaServerConfig['previewUIConf'] =	$this->content_uiconf_preview->getId();
		$KalturaServerConfig['freeTrialExpiration'] = array();
		$KalturaServerConfig['freeTrialExpiration']['enabled'] = false;
		$KalturaServerConfig['freeTrialExpiration']['trialPeriodInDays'] = 30;

		$login = array();
		$login['limitAccess'] = array();
		$login['limitAccess']["enabled"] = true;
		$login['limitAccess']["verifyBetaServiceUrl"]= "/index.php/kmcng/getpartner?pid=";

		$KalturaServerConfig["login"]= $login;


		$cdnServers = array();
		$cdnServers['serverUri'] = "http://" . kConf::get("cdn_api_host");
		if($kmcngParams["kmcng_debug_mode"])
			$cdnServers['securedServerUri'] = "http://" . kConf::get("cdn_api_host");
		else
			$cdnServers['securedServerUri'] = "https://" . kConf::get("cdn_api_host_https");

		$externalApps = array();
		$studio =array();
		if (kConf::hasParam("studio_version") && kConf::hasParam("html5_version") )
		{
			$studio["enabled"] = true;
			$studio["uri"] =  '/apps/studio/' . kConf::get("studio_version") . "/index.html";
			$html5Version = kConf::get("html5_version");
			$studio["html5_version"] = $html5Version;
			$studio["html5lib"] = $cdnServers['securedServerUri'] ."/html5/html5lib/".$html5Version."/mwEmbedLoader.php";
		}
		else
		{
			$studio["enabled"] = false;
			$studio["uri"] =  "";
			$studio["html5_version"] = "";
			$studio["html5lib"] = "";
		}

		$studioV3 =array();
		if (kConf::hasParam("studio_v3_version") && kConf::hasParam("html5_version") )
		{
			$studioV3["enabled"] = true;
			$studioV3["uri"] =  '/apps/studioV3/' . kConf::get("studio_v3_version") . "/index.html";
			$html5Version = kConf::get("html5_version");
			$studioV3["html5_version"] = $html5Version;
			$studioV3["html5lib"] = $cdnServers['securedServerUri'] ."/html5/html5lib/".$html5Version."/mwEmbedLoader.php";
		}
		else
		{
			$studioV3["enabled"] = false;
			$studioV3["uri"] =  "";
			$studioV3["html5_version"] = "";
			$studioV3["html5lib"] = "";
		}

		$liveAnalytics =array();
		// TODO Future use - remove the false flag
		if (false && kConf::hasParam("liveanalytics_version") && isset($this->content_uiconf_livea) )
		{
			$liveAnalytics["enabled"] = true;
			$liveAnalytics["uri"] =  '/apps/liveanalytics/' . kConf::get("liveanalytics_version") . "/index.html";
			$liveAnalytics["uiConfId"] = $this->content_uiconf_livea;
		}
		else
		{
			$liveAnalytics["enabled"] = false;
			$liveAnalytics["uri"] =  "";
			$liveAnalytics["uiConfId"] = 0;
		}

		$liveDashboard =array();
		if (kConf::hasParam("live_dashboard_version") )
		{
			$liveDashboard["enabled"] = true;
			$liveDashboard["uri"] =  '/apps/liveDashboard/' . kConf::get("live_dashboard_version") . "/index.html";
		}
		else
		{
			$liveDashboard["enabled"] = false;
			$liveDashboard["uri"] =  "";
		}

		$kava =array();
		// TODO Future use - remove the false flag
		if (false && kConf::hasParam("druid_url"))
		{
			$kava["enabled"] =true;
			$kava["uri"] =kConf::get("druid_url");
		}
		else
		{
			$kava["enabled"] =false;
			$kava["uri"] ="";
		}

		$clipAndTrim =array();
		if ($kmcngParams["kmcng_kea_version"])
		{
			$clipAndTrim["enabled"] =true;
			$clipAndTrim["uri"] =  '/apps/kea/' . $kmcngParams["kmcng_kea_version"] . "/index.html";
		}
		else
		{
			$clipAndTrim["enabled"] =false;
			$clipAndTrim["uri"] ="";
		}

		$advertisements =array();
		if ($kmcngParams["kmcng_kea_version"])
		{
			$advertisements["enabled"] =true;
			$advertisements["uri"] =  '/apps/kea/' . $kmcngParams["kmcng_kea_version"] . "/index.html";
		}
		else
		{
			$advertisements["enabled"] =false;
			$advertisements["uri"] ="";
		}

		$usageDashboard =array();
		if (kConf::get("usagedashboard_version") && kConf::hasParam("map_zoom_levels") && kConf::hasParam("cdn_static_hosts") && isset($this->content_uiconf_livea))
		{
			$usageDashboard["enabled"] =true;
			$usageDashboard["uri"] =  '/apps/usage-dashboard/' . kConf::get("usagedashboard_version") . "/index.html";
			$usageDashboard["uiConfId"] = $this->content_uiconf_livea;
			$usageDashboard["map_urls"] = array_map(function($s) {return "$s/content/static/maps/v1";}, kConf::get("cdn_static_hosts"));
			$usageDashboard["map_zoom_levels"] = kConf::get("map_zoom_levels");
		}
		else
		{
			$usageDashboard["enabled"] =false;
			$usageDashboard["uri"] ="";
			$usageDashboard["uiConfId"] = 0;
			$usageDashboard["map_urls"] = array();
			$usageDashboard["map_zoom_levels"] ="";
		}

		$externalApps["studio"] = $studio;
		$externalApps["studioV3"] = $studioV3;
		$externalApps["liveAnalytics"] = $liveAnalytics;
		$externalApps["liveDashboard"] = $liveDashboard;
		$externalApps["kava"] = $kava;
		$externalApps["usageDashboard"] = $usageDashboard;
		$externalApps["clipAndTrim"] = $clipAndTrim;
		$externalApps["advertisements"] = $advertisements;

		$externalLinks = array();

		$previewAndEmbed = array();
		$previewAndEmbed["embedTypes"] = $kmcngParams["embedTypes"];
		$previewAndEmbed["deliveryProtocols"] =  $kmcngParams["deliveryProtocols"];

		$kaltura = array();
		$kaltura["kmcOverview"] =  $kmcngParams["kmcOverview"];
		$kaltura["mediaManagement"] =  $kmcngParams["mediaManagement"];
		$kaltura["userManual"] =  $kmcngParams["userManual"];
		$kaltura["support"] =  $kmcngParams["support"];
		$kaltura["signUp"] =  $kmcngParams["signUp"];
		$kaltura["contactUs"] = $kmcngParams["contactUs"];
		$kaltura["upgradeAccount"] = $kmcngParams["upgradeAccount"];
		$kaltura["contactSalesforce"] = $kmcngParams["contactSalesforce"];

		$entitlements = array();
		$entitlements["manage"] =  $kmcngParams["manage"];

		$uploads = array();
		$uploads["needHighSpeedUpload"] = $kmcngParams["needHighSpeedUpload"];
		$uploads["highSpeedUpload"] = $kmcngParams["highSpeedUpload"];
		$uploads["bulkUploadSamples"] =  $kmcngParams["bulkUploadSamples"];
		$live = array();
		$live["akamaiEdgeServerIpURL"] =  $kmcngParams["akamaiEdgeServerIpURL"];

		$externalLinks["previewAndEmbed"] = $previewAndEmbed;
		$externalLinks["kaltura"] = $kaltura;
		$externalLinks["entitlements"] = $entitlements;
		$externalLinks["uploads"] = $uploads;
		$externalLinks["live"] = $live;

		$config['kalturaServer'] = $KalturaServerConfig;
		$config['cdnServers'] = $cdnServers;
		$config["externalApps"]=$externalApps;
		$config["externalLinks"]=$externalLinks;

		return $config;
	}

}