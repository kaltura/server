<?php
/**
 * @package    Core
 * @subpackage KMCNG
 */
class kmcngAction extends kalturaAction
{
	public function execute()
	{
		if (!kConf::hasParam('kmcng'))
		{
			KalturaLog::warning("kmcng config doesn't exist in configuration.");
			return sfView::ERROR;
		}

		$kmcngParams = kConf::get('kmcng');

		// Check for forced HTTPS
		if (!isset($kmcngParams["kmcng_debug_mode"]))
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

		if (!isset($kmcngParams["kmcng_version"]))
		{
			KalturaLog::warning("kmcng_version doesn't exist in configuration.");
			return sfView::ERROR;
		}

		$kmcngVersion = $kmcngParams["kmcng_version"];
		$baseDir = kConf::get("BASE_DIR", 'system');
		$basePath = $baseDir . "/apps/kmcng/$kmcngVersion/";
		$deployUrl = "/apps/kmcng/$kmcngVersion/";

		$path = $basePath . "index.html";
		$content = file_get_contents($path);
		if ($content === false)
		{
			KalturaLog::warning("Couldn't locate kmcng path: $path");
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

		$this->previewUIConf = uiConfPeer::getUiconfByTagAndVersion('KMCng', $kmcngParams["kmcng_version"]);
		$this->content_uiconfs_preview = isset($this->previewUIConf) ? array_values($this->previewUIConf) : null;
		$this->content_uiconf_preview = (is_array($this->content_uiconfs_preview) && reset($this->content_uiconfs_preview)) ? reset($this->content_uiconfs_preview) : null;

		$secureServerUri = "https://" . kConf::get("cdn_api_host_https");
		if (isset($kmcngParams["kmcng_debug_mode"]))
			$secureServerUri = "http://" . kConf::get("cdn_api_host");

		$studio = array();
		if (kConf::hasParam("studio_version") && kConf::hasParam("html5_version"))
		{
			$studio["enabled"] = true;
			$studio["uri"] = '/apps/studio/' . kConf::get("studio_version") . "/index.html";
			$html5Version = kConf::get("html5_version");
			$studio["html5_version"] = $html5Version;
			$studio["html5lib"] = $secureServerUri . "/html5/html5lib/" . $html5Version . "/mwEmbedLoader.php";
		} else
		{
			$studio["enabled"] = false;
			$studio["uri"] = "";
			$studio["html5_version"] = "";
			$studio["html5lib"] = "";
		}

		$studioV3 = array();
		if (kConf::hasParam("studio_v3_version") && kConf::hasParam("html5_version"))
		{
			$studioV3["enabled"] = true;
			$studioV3["uri"] = '/apps/studioV3/' . kConf::get("studio_v3_version") . "/index.html";
			$html5Version = kConf::get("html5_version");
			$studioV3["html5_version"] = $html5Version;
			$studioV3["html5lib"] = $secureServerUri . "/html5/html5lib/" . $html5Version . "/mwEmbedLoader.php";
		} else
		{
			$studioV3["enabled"] = false;
			$studioV3["uri"] = "";
			$studioV3["html5_version"] = "";
			$studioV3["html5lib"] = "";
		}

		$liveAnalytics = array();
		// TODO Future use - remove the false flag
		if (false && kConf::hasParam("liveanalytics_version") && isset($this->content_uiconf_livea))
		{
			$liveAnalytics["enabled"] = true;
			$liveAnalytics["uri"] = '/apps/liveanalytics/' . kConf::get("liveanalytics_version") . "/index.html";
			$liveAnalytics["uiConfId"] = $this->content_uiconf_livea;
		} else
		{
			$liveAnalytics["enabled"] = false;
			$liveAnalytics["uri"] = "";
			$liveAnalytics["uiConfId"] = 0;
		}

		$liveDashboard = array();
		if (kConf::hasParam("live_dashboard_version"))
		{
			$liveDashboard["enabled"] = true;
			$liveDashboard["uri"] = '/apps/liveDashboard/' . kConf::get("live_dashboard_version") . "/index.html";
		} else
		{
			$liveDashboard["enabled"] = false;
			$liveDashboard["uri"] = "";
		}

		$clipAndTrim = array();
		if ($kmcngParams["kmcng_kea_version"])
		{
			$clipAndTrim["enabled"] = true;
			$clipAndTrim["uri"] = '/apps/kea/' . $kmcngParams["kmcng_kea_version"] . "/index.html";
		} else
		{
			$clipAndTrim["enabled"] = false;
			$clipAndTrim["uri"] = "";
		}

		$advertisements = array();
		if ($kmcngParams["kmcng_kea_version"])
		{
			$advertisements["enabled"] = true;
			$advertisements["uri"] = '/apps/kea/' . $kmcngParams["kmcng_kea_version"] . "/index.html";
		} else
		{
			$advertisements["enabled"] = false;
			$advertisements["uri"] = "";
		}

		$usageDashboard = array();
		if (kConf::get("usagedashboard_version") && kConf::hasParam("map_zoom_levels") && kConf::hasParam("cdn_static_hosts") && isset($this->content_uiconf_livea))
		{
			$usageDashboard["enabled"] = true;
			$usageDashboard["uri"] = '/apps/usage-dashboard/' . kConf::get("usagedashboard_version") . "/index.html";
			$usageDashboard["uiConfId"] = $this->content_uiconf_livea;
			$usageDashboard["map_urls"] = array_map(function ($s)
			{
				return "$s/content/static/maps/v1";
			}, kConf::get("cdn_static_hosts"));
			$usageDashboard["map_zoom_levels"] = kConf::get("map_zoom_levels");
		} else
		{
			$usageDashboard["enabled"] = false;
			$usageDashboard["uri"] = "";
			$usageDashboard["uiConfId"] = 0;
			$usageDashboard["map_urls"] = array();
			$usageDashboard["map_zoom_levels"] = "";
		}

		$previewAndEmbed = array();
		foreach ($kmcngParams['previewAndEmbed'] as $key => $value)
			$previewAndEmbed["$key"] = $value;

		$kaltura = array();
		foreach ($kmcngParams['kaltura'] as $key => $value)
			$kaltura["$key"] = $value;

		$entitlements = array();
		foreach ($kmcngParams['entitlements'] as $key => $value)
			$entitlements["$key"] = $value;

		$uploads = array();
		foreach ($kmcngParams['uploads'] as $key => $value)
			$uploads["$key"] = $value;

		$live = array();
		foreach ($kmcngParams['live'] as $key => $value)
			$live["$key"] = $value;

		$config = array(
			'kalturaServer' => array(
				'uri' => kConf::get("www_host"),
				'deployUrl' => $deployUrl,
				'previewUIConf' => $this->content_uiconf_preview->getId(),
				'freeTrialExpiration' => array(
					'enabled' => false,
					'trialPeriodInDays' => 30
				),
				'login' => array(
					'limitAccess' => array(
						'enabled' => false,
						'verifyBetaServiceUrl' => ""
					))),
			'cdnServers' => array(
				'serverUri' => "http://" . kConf::get("cdn_api_host"),
				'securedServerUri' => $secureServerUri
			),
			"externalApps" => array(
				"studio" => $studio,
				"studioV3" => $studioV3,
				"liveAnalytics" => $liveAnalytics,
				"liveDashboard" => $liveDashboard,
				"kava" => array(
					"enabled" => false,
					"uri" => ""
				),
				"usageDashboard" => $usageDashboard,
				"clipAndTrim" => $clipAndTrim,
				"advertisements" => $advertisements
			),
			"externalLinks" => array(
				"previewAndEmbed" => $previewAndEmbed,
				"kaltura" => $kaltura,
				"entitlements" => $entitlements,
				"uploads" => $uploads,
				"live" => $live
			)
		);

		return $config;
	}
}