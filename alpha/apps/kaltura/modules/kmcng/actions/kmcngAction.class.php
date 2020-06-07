<?php
/**
 * @package    Core
 * @subpackage KMCNG
 */
class kmcngAction extends kalturaAction
{
	const LIVE_ANALYTICS_UICONF_TAG = 'livea_player';
	const PLAYER_V3_VERSIONS_TAG = 'playerV3Versions';

	public function execute()
	{
		if (!kConf::hasParam('kmcng'))
		{
			KalturaLog::warning("kmcng config doesn't exist in configuration.");
			return sfView::ERROR;
		}

		$kmcngParams = kConf::get('kmcng');
		$isSecuredLogin = kConf::get('kmc_secured_login');
		$enforceSecureProtocol = isset($isSecuredLogin) && $isSecuredLogin == "1";
		$requestSecureProtocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');

		// Check for forced HTTPS

		if ($enforceSecureProtocol)
		{
			if (!$requestSecureProtocol)
			{
				header("Location: " . infraRequestUtils::PROTOCOL_HTTPS . "://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]);
				die();
			}
			header("Strict-Transport-Security: max-age=63072000; includeSubdomains; preload");
		}

		header("X-XSS-Protection: 1; mode=block");
		header("X-Frame-Options: deny");

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

		$config = $this->initConfig($deployUrl, $kmcngParams, $enforceSecureProtocol, $requestSecureProtocol);
		$config = json_encode($config);
		$config = str_replace("\\/", '/', $config);

		$content = str_replace("<base href=\"/\">", "<base href=\"/index.php/kmcng/\">", $content);
		$content = preg_replace("/src=\"(?!(http:)|(https:)|\/)/i", "src=\"{$deployUrl}", $content);
		$content = preg_replace("/href=\"(?!(http:)|(https:)|\/)/i", "href=\"{$deployUrl}", $content);

		$content = str_replace("var kmcConfig = null", "var kmcConfig = " . $config, $content);
		echo $content;
	}

	private function initConfig($deployUrl, $kmcngParams, $enforceSecureProtocol, $requestSecureProtocol)
	{
		$this->liveAUiConf = uiConfPeer::getUiconfByTagAndVersion(self::LIVE_ANALYTICS_UICONF_TAG, kConf::get("liveanalytics_version"));
		$this->contentUiconfsLivea = isset($this->liveAUiConf) ? array_values($this->liveAUiConf) : null;
		$this->contentUiconfLivea = (is_array($this->contentUiconfsLivea) && reset($this->contentUiconfsLivea)) ? reset($this->contentUiconfsLivea) : null;

		$this->previewUIConf = uiConfPeer::getUiconfByTagAndVersion('KMCngV2', $kmcngParams["kmcng_version"]);
		if (empty($this->previewUIConf))
		{
			$this->previewUIConf = uiConfPeer::getUiconfByTagAndVersion('KMCng', $kmcngParams["kmcng_version"]);
		}
		$this->contentUiconfsPreview = isset($this->previewUIConf) ? array_values($this->previewUIConf) : null;
		$this->contentUiconfPreview = (is_array($this->contentUiconfsPreview) && reset($this->contentUiconfsPreview)) ? reset($this->contentUiconfsPreview) : null;

		$this->previewUIConfV7 = uiConfPeer::getUiconfByTagAndVersion('KMCngV7', $kmcngParams["kmcng_version"]);
		$this->contentUiconfsPreviewV7 = isset($this->previewUIConfV7) ? array_values($this->previewUIConfV7) : null;
		$this->contentUiconfPreviewV7 = (is_array($this->contentUiconfsPreviewV7)) ? reset($this->contentUiconfsPreviewV7) : null;

		$secureCDNServerUri = "https://" . kConf::get("cdn_api_host_https");
		if (!$enforceSecureProtocol && !$requestSecureProtocol)
			$secureCDNServerUri = "http://" . kConf::get("cdn_api_host");

		$serverAPIUri = kConf::get("www_host");
		if (isset($kmcngParams["kmcng_custom_uri"]))
			$serverAPIUri = $kmcngParams["kmcng_custom_uri"];

		$this->playerV3VersionsUiConf = uiConfPeer::getUiconfByTagAndVersion(self::PLAYER_V3_VERSIONS_TAG, "latest");
		$this->content_uiconfs_player_v3_versions = isset($this->playerV3VersionsUiConf) ? array_values($this->playerV3VersionsUiConf) : null;
		$this->content_uiconf_player_v3_versions = (is_array($this->content_uiconfs_player_v3_versions) && reset($this->content_uiconfs_player_v3_versions)) ? reset($this->content_uiconfs_player_v3_versions) : null;


		$studio = null;
		if (kConf::hasParam("studio_version") && kConf::hasParam("html5_version"))
		{
			$studio = array(
				"uri" => '/apps/studio/' . kConf::get("studio_version") . "/index.html",
				"html5_version" => kConf::get("html5_version"),
				"html5lib" => $secureCDNServerUri . "/html5/html5lib/" . kConf::get("html5_version") . "/mwEmbedLoader.php"
			);
		}

		$studioV3 = null;
		if (kConf::hasParam("studio_v3_version") && kConf::hasParam("html5_version"))
		{
			$studioV3 = array(
				"uri" => '/apps/studioV3/' . kConf::get("studio_v3_version") . "/index.html",
				"html5_version" => kConf::get("html5_version"),
				"html5lib" => $secureCDNServerUri . "/html5/html5lib/" . kConf::get("html5_version") . "/mwEmbedLoader.php",
				"playerVersionsMap" => isset($this->content_uiconf_player_v3_versions) ? $this->content_uiconf_player_v3_versions->getConfig() : ''
			);
		}

		$liveAnalytics = null;
		if (kConf::hasParam("liveanalytics_version"))
		{
			$liveAnalytics = array(
				"uri" => '/apps/liveanalytics/' . kConf::get("liveanalytics_version") . "/index.html",
				"uiConfId" => isset($this->contentUiconfLivea) ? strval($this->contentUiconfLivea->getId()) : null,
				"mapUrls" => kConf::hasParam ("cdn_static_hosts") ? array_map(function($s) {return "$s/content/static/maps/v1";}, kConf::get ("cdn_static_hosts")) : array(),
                "mapZoomLevels" => kConf::hasParam("map_zoom_levels") ? kConf::get("map_zoom_levels") : ''
			);
		}

		$liveDashboard = null;
		if (kConf::hasParam("live_dashboard_version"))
		{
			$liveDashboard = array(
				"uri" => '/apps/liveDashboard/' . kConf::get("live_dashboard_version") . "/index.html"
			);
		}

		$editor = null;
		if (isset($kmcngParams["kmcng_kea_version"]))
		{
			$editor = array(
				"uri" => '/apps/kea/' . $kmcngParams["kmcng_kea_version"] . "/index.html"
			);
		}

		$reach = null;
		if (isset($kmcngParams["kmcng_reach_version"]))
		{
			$reach = array(
				"uri" => '/apps/reach/' . $kmcngParams["kmcng_reach_version"] . "/index.html"
			);
		}

		$usageDashboard = null;
		if (kConf::hasParam("usagedashboard_version"))
		{
			$usageDashboard = array(
				"uri" => '/apps/usage-dashboard/' . kConf::get("usagedashboard_version") . "/index.html"
			);
		}

		$kmcAnalytics = null;
		if (kConf::hasParam("kmc_analytics_version"))
		{
			$kmcAnalytics = array(
				"uri" => '/apps/kmc-analytics/' . kConf::get("kmc_analytics_version") . "/index.html"
			);
		}

		$config = array(
			'kalturaServer' => array(
				'uri' => $serverAPIUri,
				'deployUrl' => $deployUrl,
				'resetPasswordUri'=> "/index.php/kmcng/resetpassword/setpasshashkey/{hash}",
				'previewUIConf' => ($this->contentUiconfPreview) ? $this->contentUiconfPreview->getId() : '',
				'previewUIConfV7' => ($this->contentUiconfPreviewV7) ? $this->contentUiconfPreviewV7->getId() : '',
				),
			'cdnServers' => array(
				'serverUri' => "http://" . kConf::get("cdn_api_host"),
				'securedServerUri' => $secureCDNServerUri
			),
			"externalApps" => array(
				"studioV2" => $studio,
				"studioV3" => $studioV3,
				"liveAnalytics" => $liveAnalytics,
				"liveDashboard" => $liveDashboard,
				"usageDashboard" => $usageDashboard,
				"editor" => $editor,
				"reach" => $reach,
				"kmcAnalytics" => $kmcAnalytics
			),
			"externalLinks" => array(
				"previewAndEmbed" => $kmcngParams['previewAndEmbed'],
				"kaltura" => $kmcngParams['kaltura'],
				"entitlements" => $kmcngParams['entitlements'],
				"uploads" => $kmcngParams['uploads'],
				"live" => $kmcngParams['live']
			)
		);

		return $config;
	}
}
