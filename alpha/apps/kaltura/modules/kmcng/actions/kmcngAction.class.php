<?php
/**
 * @package    Core
 * @subpackage KMCNG
 */
class kmcngAction extends kalturaAction
{
	const LIVE_ANALYTICS_UICONF_TAG = 'livea_player';
	const PLAYER_V3_VERSIONS_TAG = 'playerV3Versions';
	const PLAYER_V3_OVP_VERSIONS_TAG = 'playerV3OvpVersions';

	public static function getDirectivesAndAllowlists($directivesToDisable): string
	{
		$permissionPolicyHeader = "";
		$directives = [];
		foreach ($directivesToDisable as $directive => $allowList)
		{
			$directives[] = $directive . '=' . $allowList;
		}
		$permissionPolicyHeader .= implode(',', $directives);
		return $permissionPolicyHeader;
	}

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

		//security
		header("Referrer-Policy: strict-origin");
		header("X-Content-Type-Options: nosniff");
		header("Cross-Origin-Embedder-Policy: unsafe-none");
		header("Cross-Origin-Resource-Policy: same-origin");
		header("Cross-Origin-Opener-Policy: unsafe-none");

		if (kConf::hasParam('kmcng_permissions_policy_directives'))
		{
			$directivesToDisable = kConf::get('kmcng_permissions_policy_directives');
			header("Permissions-Policy: " . self::getDirectivesAndAllowlists($directivesToDisable));
		}

		if (!isset($kmcngParams["kmcng_version"]))
		{
			KalturaLog::warning("kmcng_version doesn't exist in configuration.");
			return sfView::ERROR;
		}

		$kmcngVersion = $kmcngParams["kmcng_version"];
		$deployUrl = "/apps/kmcng/$kmcngVersion/";
		
		$appsHost = kConf::get('apps_host', kConfMapNames::RUNTIME_CONFIG, null);
		if ($appsHost)
		{
			$baseDir = $appsHost;
			$path = $baseDir . $deployUrl . 'index.html';
			
			$content = KCurlWrapper::getContent($path, null, true);
		}
		else
		{
			$baseDir = kConf::get('BASE_DIR', 'system');
			$path = $baseDir . $deployUrl . 'index.html';
			$content = file_get_contents($path);
		}
		
		if ($content === false)
		{
			KalturaLog::warning("Couldn't locate kmcng path: $path");
			return sfView::ERROR;
		}

		$config = $this->initConfig($deployUrl, $kmcngParams, $enforceSecureProtocol, $requestSecureProtocol);
		$config = json_encode($config);
		$config = str_replace("\\/", '/', $config);
		
		$randNum = strval(rand(11111111, 99999999));
		
		if (isset($kmcngParams['kmcng_content_security_policy']))
		{
			$kmcngContentSecurityPolicy = str_replace("%NONCE_TOKEN%", $randNum, $kmcngParams['kmcng_content_security_policy']);
			header("Content-Security-Policy: " . $kmcngContentSecurityPolicy);
		}
		
		$content = str_replace("%NONCE_TOKEN%", $randNum, $content);
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
		
		$productionSettings = kConf::get('production', 'admin', null);
		$epUrl = $productionSettings['settings']['epUrl'];
		
		$loadVersionMapFromKConf = kConf::get("loadFromKConf", kConfMapNames::EMBED_PLAYKIT, null);

		list($playerVersionsMapVersionConfig, $playerVersionsMapConfVars) = $this->getConfigByTagAndVersion($loadVersionMapFromKConf, self::PLAYER_V3_VERSIONS_TAG, "latest");
		list($playerBetaVersionsMapVersionConfig, $playerBetaConfVars) = $this->getConfigByTagAndVersion($loadVersionMapFromKConf, self::PLAYER_V3_VERSIONS_TAG, "beta");

		list($playerOvpVersionsMapVersionConfig, $playerOvpConfVars) = $this->getConfigByTagAndVersion($loadVersionMapFromKConf, self::PLAYER_V3_OVP_VERSIONS_TAG, "latest");
		list($playerBetaOvpVersionsMapVersionConfig, $playerBetaOvpConfVars) = $this->getConfigByTagAndVersion($loadVersionMapFromKConf, self::PLAYER_V3_OVP_VERSIONS_TAG, "beta");

		$studio = null;
		$html5_version = kConf::getArrayValue('html5_version', 'playerApps', kConfMapNames::APP_VERSIONS, null);
		$studio_version = kConf::getArrayValue('studio_version', 'playerApps', kConfMapNames::APP_VERSIONS, null);
		$studio_v3_version = kConf::getArrayValue('studio_v3_version', 'playerApps', kConfMapNames::APP_VERSIONS, null);
		$studio_v7_version = kConf::getArrayValue('studio_v7_version', 'playerApps', kConfMapNames::APP_VERSIONS, null);

		if(!$html5_version)
			KalturaLog::warning("The html player version was not found");
		if(!$studio_version && !$studio_v3_version)
			KalturaLog::warning("The studio version was not found");

		if ($studio_version && $html5_version)
		{
			$studio = array(
				"uri" => '/apps/studio/' . $studio_version . "/index.html",
				"html5_version" => $html5_version,
				"html5lib" => $secureCDNServerUri . "/html5/html5lib/" . $html5_version . "/mwEmbedLoader.php"
			);
		}

		$studioV3 = null;
		if ($studio_v3_version && $html5_version)
		{
			$studioV3 = array(
				"uri" => '/apps/studioV3/' . $studio_v3_version . "/index.html",
				"html5_version" => $html5_version,
				"html5lib" => $secureCDNServerUri . "/html5/html5lib/" . $html5_version . "/mwEmbedLoader.php",
				"playerVersionsMap" => $playerVersionsMapVersionConfig,
				"playerBetaVersionsMap" => $playerBetaVersionsMapVersionConfig,
				"playerConfVars" => $playerVersionsMapConfVars,
				"playerBetaConfVars" => $playerBetaConfVars,
				"playerOvpVersionsMap" => $playerOvpVersionsMapVersionConfig,
				"playerBetaOvpVersionsMap" => $playerBetaOvpVersionsMapVersionConfig,
				"playerOvpConfVars" => $playerOvpConfVars,
				"playerBetaOvpConfVars" => $playerBetaOvpConfVars
			);
		}

		$studioV7 = null;
		if ($studio_v7_version && $html5_version)
		{
			$studioV7 = $studioV3;
			$studioV7['uri'] = '/apps/player-studio-v7/' . $studio_v7_version . "/index.html";
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

		$playerWrapper = array(
                    "uri" => '/apps/kmcng/' . $kmcngParams["kmcng_version"] . "/public/playerWrapper.html"
                );

		$config = array(
			'ks' =>  ($this->getRequest()->getMethod() == sfRequest::POST && $this->getRequest()->getParameter('ks')) ? $this->getKs() : null,
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
			'kpfServer' => array('kpfPackageManagerBaseUrl' => kconf::get('kpf_package_manager_base_url','local',null), 'kpfPurchaseManagerBaseUrl' => kconf::get('kpf_purchase_manager_base_url', 'local', null)) ,
			'analyticsServer' => array('uri' => kConf::get('analytics_host', 'local',  '')),
			'epServer' => array('uri' => $epUrl),
			"externalApps" => array(
				"studioV2" => $studio,
				"studioV3" => $studioV3,
				"studioV7" => $studioV7,
				"liveAnalytics" => $liveAnalytics,
				"liveDashboard" => $liveDashboard,
				"usageDashboard" => $usageDashboard,
				"editor" => $editor,
				"reach" => $reach,
				"playerWrapper" => $playerWrapper,
				"kmcAnalytics" => $kmcAnalytics
			),
			"externalLinks" => array(
				"previewAndEmbed" => $kmcngParams['previewAndEmbed'],
				"kaltura" => $kmcngParams['kaltura'],
				"entitlements" => $kmcngParams['entitlements'],
				"uploads" => $kmcngParams['uploads'],
				"live" => $kmcngParams['live']
			),
			'externalServices' => array(
				'appRegistryEndpoint' => array('uri' => MicroServiceAppRegistry::buildServiceUrl(MicroServiceAppRegistry::$host, MicroServiceAppRegistry::$service)),
				'appSubscriptionEndpoint' => array('uri' => MicroServiceAppSubscription::buildServiceUrl(MicroServiceAppSubscription::$host, MicroServiceAppSubscription::$service)),
				'authManagerEndpoint' => array('uri' => MicroServiceAuthManager::buildServiceUrl(MicroServiceAuthManager::$host, MicroServiceAuthManager::$service)),
				'authProfileEndpoint' => array('uri' => MicroServiceAuthProfile::buildServiceUrl(MicroServiceAuthProfile::$host, MicroServiceAuthProfile::$service)),
				'spaProxyEndpoint' => array('uri' => MicroServiceSpaProxy::buildServiceUrl(MicroServiceSpaProxy::$host, MicroServiceSpaProxy::$service)),
				'userProfileEndpoint' => array('uri' => MicroServiceUserProfile::buildServiceUrl(MicroServiceUserProfile::$host, MicroServiceUserProfile::$service)),
				'userReportsEndpoint' => array('uri' => MicroServiceUserReports::buildServiceUrl(MicroServiceUserReports::$host, MicroServiceUserReports::$service)),
				'mrEndpoint' => array('uri' => MicroServiceMediaRepurposing::buildServiceUrl(MicroServiceMediaRepurposing::$host, MicroServiceMediaRepurposing::$service)),
				'vendorIntegrationsEndpoint' => array('uri' => MicroServiceVendorIntegrations::buildServiceUrl(MicroServiceVendorIntegrations::$host, MicroServiceVendorIntegrations::$service)),
				'unisphereLoaderEndpoint' => array('uri' => MicroServiceUnisphereLoader::buildServiceUrl(MicroServiceUnisphereLoader::$host, MicroServiceUnisphereLoader::$service, false)),
				'checklistEndpoint' => array('uri' => MicroServiceChecklist::buildServiceUrl(MicroServiceChecklist::$host, false, false),
                                            'checklistItem' => kConf::get('kmcng_checklist_item','local','kmc-ng-v1'),
                                            'scriptUri' => MicroServiceChecklist::buildScriptUrl(MicroServiceChecklist::$host, false, false)),
			),
		);

		return $config;
	}

	private function getConfigByTagAndVersion($loadVersionMapFromKConf, $tag, $version)
	{
		$versionConfig = json_encode(array());
		$confVars = json_encode(array());

		if($loadVersionMapFromKConf)
		{
			$versionConfig = json_encode(kConf::get($tag."_".$version, kConfMapNames::EMBED_PLAYKIT, array()), true);
			$versionTag = kConf::get($tag."_".$version."_productVersion", kConfMapNames::EMBED_PLAYKIT, "");
			$confVars = json_encode(array("version" => $versionTag));
		}
		else
		{
			$uiConf = uiConfPeer::getUiconfByTagAndVersion($tag, $version);
			$uiConfVersions = isset($uiConf) ? array_values($uiConf) : null;
			$uiConfVersion = (is_array($uiConfVersions) && reset($uiConfVersions)) ? reset($uiConfVersions) : null;
			if($uiConfVersion)
			{
				$versionConfig = isset($uiConfVersion) ? $uiConfVersion->getConfig() : '';
				$confVars = isset($uiConfVersion) ? $uiConfVersion->getConfVars() : '';
			}
		}

		return array($versionConfig, $confVars);
	}


	private function getKs()
	{
		$ks = $this->getRequest()->getParameter('ks');
		$ksObj = kSessionUtils::crackKs($ks);
		$action = $this->getRequest()->getParameter('actions');
		// set login fields for persist-login-by-ks action
		if($ksObj && $action == 'persist-login-by-ks') {
			$ksUserId = $ksObj->user;
			$ksPartnerId = $ksObj->partner_id;
			$partner = PartnerPeer::retrieveByPK($ksPartnerId);
			$kuser = null;
			if($partner)
			{
				$kuser = kuserPeer::getKuserByPartnerAndUid($ksPartnerId, $ksUserId, true);
			}
			$loginData = null;
			if($kuser)
			{
				$loginData = $kuser->getLoginData();
			}
			if($loginData)
			{
				UserLoginDataPeer::setLastLoginFields($loginData, $kuser);
			}
		}

		return $ks;
	}
}
