<?php
/**
 * @package    Core
 * @subpackage KMC
 */
class kmc4Action extends kalturaAction
{
	const CURRENT_KMC_VERSION = 4;
	const LIVE_ANALYTICS_UICONF_TAG = 'livea_player';
	const LIVE_DASHBOARD_UICONF_TAG = 'lived_player';
	
	private $confs = array();
	
	const SYSTEM_DEFAULT_PARTNER = 0;
	
	public function execute ( ) 
	{
		
		sfView::SUCCESS;

		/** check parameters and verify user is logged-in **/
		$this->ks = $this->getP ( "kmcks" );
		if(!$this->ks)
		{
			// if kmcks from cookie doesn't exist, try ks from REQUEST
			$this->ks = $this->getP('ks');
		}
		
		/** if no KS found, redirect to login page **/
		if (!$this->ks)
		{
			$this->redirect( "kmc/kmc" );
			die();
		}
		$ksObj = kSessionUtils::crackKs($this->ks);
		// Set partnerId from KS
		$this->partner_id = $ksObj->partner_id;

		// Check if the KMC can be framed
		$allowFrame = PermissionPeer::isValidForPartner(PermissionName::FEATURE_KMC_ALLOW_FRAME, $this->partner_id);
		if(!$allowFrame) {
			header( 'X-Frame-Options: DENY' );
		}
		// Check for forced HTTPS
		$force_ssl = PermissionPeer::isValidForPartner(PermissionName::FEATURE_KMC_ENFORCE_HTTPS, $this->partner_id);
		if( $force_ssl && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') ) {
			header( "Location: " . infraRequestUtils::PROTOCOL_HTTPS . "://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"] );
			die();
		}
		/** END - check parameters and verify user is logged-in **/
		
		/** Get array of allowed partners for the current user **/
		$allowedPartners = array();
		$this->full_name = "";
		$currentUser = kuserPeer::getKuserByPartnerAndUid($this->partner_id, $ksObj->user, true);
		if($currentUser) {
			$partners = myPartnerUtils::getPartnersArray($currentUser->getAllowedPartnerIds());
			foreach ($partners as $partner)
				$allowedPartners[] = array('id' => $partner->getId(), 'name' => $partner->getName());
				
			$this->full_name = $currentUser->getFullName();
		}
		$this->showChangeAccount = (count($allowedPartners) > 1 ) ? true : false;

		// Load partner
		$this->partner = $partner = PartnerPeer::retrieveByPK($this->partner_id);
		if (!$partner)
			KExternalErrors::dieError(KExternalErrors::PARTNER_NOT_FOUND);
		
		if (!$partner->validateApiAccessControl())
			KExternalErrors::dieError(KExternalErrors::SERVICE_ACCESS_CONTROL_RESTRICTED);
		
		kmcUtils::redirectPartnerToCorrectKmc($partner, $this->ks, null, null, null, self::CURRENT_KMC_VERSION);
		$this->templatePartnerId = $this->partner ? $this->partner->getTemplatePartnerId() : self::SYSTEM_DEFAULT_PARTNER;
		$ignoreEntrySeoLinks = PermissionPeer::isValidForPartner(PermissionName::FEATURE_IGNORE_ENTRY_SEO_LINKS, $this->partner_id);
		$useEmbedCodeProtocolHttps = PermissionPeer::isValidForPartner(PermissionName::FEATURE_EMBED_CODE_DEFAULT_PROTOCOL_HTTPS, $this->partner_id);
		$showFlashStudio = PermissionPeer::isValidForPartner(PermissionName::FEATURE_SHOW_FLASH_STUDIO, $this->partner_id);
		$showHTMLStudio = PermissionPeer::isValidForPartner(PermissionName::FEATURE_SHOW_HTML_STUDIO, $this->partner_id);
		$deliveryTypes = $partner->getDeliveryTypes();
		$embedCodeTypes = $partner->getEmbedCodeTypes();
		$defaultDeliveryType = ($partner->getDefaultDeliveryType()) ? $partner->getDefaultDeliveryType() : 'http';
		$defaultEmbedCodeType = ($partner->getDefaultEmbedCodeType()) ? $partner->getDefaultEmbedCodeType() : 'auto';
		$this->previewEmbedV2 = PermissionPeer::isValidForPartner(PermissionName::FEATURE_PREVIEW_AND_EMBED_V2, $this->partner_id);
		
		/** set values for template **/
		$this->service_url = requestUtils::getRequestHost();
		$this->host = $this->stripProtocol( $this->service_url );
		$this->embed_host = $this->stripProtocol( myPartnerUtils::getHost($this->partner_id) );
		if (kConf::hasParam('cdn_api_host') && kConf::hasParam('www_host') && $this->host == kConf::get('cdn_api_host')) {
	        $this->host = kConf::get('www_host');
		}
		if($this->embed_host == kConf::get("www_host") && kConf::hasParam('cdn_api_host')) {
			$this->embed_host = kConf::get('cdn_api_host');
		}
		$this->embed_host_https = (kConf::hasParam('cdn_api_host_https')) ? kConf::get('cdn_api_host_https') : kConf::get('www_host');	

		$this->cdn_url = myPartnerUtils::getCdnHost($this->partner_id);
		$this->cdn_host = $this->stripProtocol( $this->cdn_url );
		$this->rtmp_host = kConf::get("rtmp_url");
		$this->flash_dir = $this->cdn_url . myContentStorage::getFSFlashRootPath ();

		/** set payingPartner flag **/
		$this->payingPartner = 'false';
		if($partner && $partner->getPartnerPackage() != PartnerPackages::PARTNER_PACKAGE_FREE)
		{
			$this->payingPartner = 'true';
			$ignoreSeoLinks = true;
		} else {
			$ignoreSeoLinks = $this->partner->getIgnoreSeoLinks();
		}

		/** get partner languae **/
		$language = null;
		if ($partner->getKMCLanguage())
			$language = $partner->getKMCLanguage();

		$first_login = $partner->getIsFirstLogin();
		if ($first_login === true)
		{
			$partner->setIsFirstLogin(false);
			$partner->save();
		}
		
		/** get logout url **/
		$logoutUrl = null; 
		if ($partner->getLogoutUrl())
			$logoutUrl = $partner->getLogoutUrl();
		
		$this->kmc_swf_version = kConf::get('kmc_version');

		$akamaiEdgeServerIpURL = null;
		if( kConf::hasParam('akamai_edge_server_ip_url') ) {
			$akamaiEdgeServerIpURL = kConf::get('akamai_edge_server_ip_url');
		}
		
	/** uiconf listing work **/
		/** fill $confs with all uiconf objects for all modules **/
		$kmcGeneralUiConf = kmcUtils::getAllKMCUiconfs('kmc',   $this->kmc_swf_version, self::SYSTEM_DEFAULT_PARTNER);
		$kmcGeneralTemplateUiConf = kmcUtils::getAllKMCUiconfs('kmc',   $this->kmc_swf_version, $this->templatePartnerId);

		
		/** for each module, create separated lists of its uiconf, for each need **/
		/** kmc general uiconfs **/
		$this->kmc_general = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_kmcgeneral", false, $kmcGeneralUiConf);
		$this->kmc_permissions = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_kmcpermissions", false, $kmcGeneralUiConf);
		/** P&E players: **/
		//$this->content_uiconfs_previewembed = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_previewembed", true, $kmcGeneralUiConf);
		//$this->content_uiconfs_previewembed_list = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_previewembed_list", true, $kmcGeneralUiConf);
		$this->content_uiconfs_flavorpreview = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_flavorpreview", false, $kmcGeneralUiConf);

		/* KCW uiconfs */
		$this->content_uiconfs_upload_webcam = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_uploadWebCam", false, $kmcGeneralUiConf);
		$this->content_uiconfs_upload_import = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_uploadImport", false, $kmcGeneralUiConf);

		$this->content_uiconds_clipapp_kdp = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_kdpClipApp", false, $kmcGeneralUiConf);
		$this->content_uiconds_clipapp_kclip = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_kClipClipApp", false, $kmcGeneralUiConf);
		
		$this->studioUiConf = kmcUtils::getStudioUiconf(kConf::get("studio_version"));
		$this->content_uiconfs_studio_v2 = isset($this->studioUiConf) ? array_values($this->studioUiConf) : null;
		$this->content_uiconf_studio_v2 = (is_array($this->content_uiconfs_studio_v2) && reset($this->content_uiconfs_studio_v2)) ? reset($this->content_uiconfs_studio_v2) : null;
		
		$this->liveAUiConf = kmcUtils::getLiveUiconfByTag(self::LIVE_ANALYTICS_UICONF_TAG);
		$this->content_uiconfs_livea = isset($this->liveAUiConf) ? array_values($this->liveAUiConf) : null;
		$this->content_uiconf_livea = (is_array($this->content_uiconfs_livea) && reset($this->content_uiconfs_livea)) ? reset($this->content_uiconfs_livea) : null;
		
		$this->liveDUiConf = kmcUtils::getLiveUiconfByTag(self::LIVE_DASHBOARD_UICONF_TAG);
		$this->content_uiconfs_lived = isset($this->liveDUiConf) ? array_values($this->liveDUiConf) : null;
		$this->content_uiconf_lived = (is_array($this->content_uiconfs_lived) && reset($this->content_uiconfs_lived)) ? reset($this->content_uiconfs_lived) : null;

		$kmcVars = array(
			'kmc_version'				=> $this->kmc_swf_version,
			'kmc_general_uiconf'		=> $this->kmc_general->getId(),
			'kmc_permissions_uiconf'	=> $this->kmc_permissions->getId(),
			'allowed_partners'			=> $allowedPartners,
			'kmc_secured'				=> (bool) kConf::get("kmc_secured_login"),
			'enableLanguageMenu'		=> true,
			'service_url'				=> $this->service_url,
			'host'						=> $this->host,
			'cdn_host'					=> $this->cdn_host,
			'rtmp_host'					=> $this->rtmp_host,
			'embed_host'				=> $this->embed_host,
			'embed_host_https'			=> $this->embed_host_https,
			'flash_dir'					=> $this->flash_dir,
			'getuiconfs_url'			=> '/index.php/kmc/getuiconfs',
			'terms_of_use'				=> kConf::get('terms_of_use_uri'),
			'ks'						=> $this->ks,
			'partner_id'				=> $this->partner_id,
			'first_login'				=> (bool) $first_login,
			'whitelabel'				=> $this->templatePartnerId,
			'ignore_seo_links'			=> (bool) $ignoreSeoLinks,
			'ignore_entry_seo'			=> (bool) $ignoreEntrySeoLinks,
			'embed_code_protocol_https'	=> (bool) $useEmbedCodeProtocolHttps,
			'delivery_types'			=> $deliveryTypes,
			'embed_code_types'			=> $embedCodeTypes,
			'default_delivery_type'		=> $defaultDeliveryType,
			'default_embed_code_type'	=> $defaultEmbedCodeType,
			'kcw_webcam_uiconf'			=> $this->content_uiconfs_upload_webcam->getId(),
			'kcw_import_uiconf'			=> $this->content_uiconfs_upload_import->getId(),
			'default_kdp'				=> array(
				'id'					=> $this->content_uiconfs_flavorpreview->getId(),
				'height'				=> $this->content_uiconfs_flavorpreview->getHeight(),
				'width'					=> $this->content_uiconfs_flavorpreview->getWidth(),
				'swf_version'			=> $this->content_uiconfs_flavorpreview->getswfUrlVersion(),
			),
			'clipapp'					=> array(
				'version'				=> kConf::get("clipapp_version"),
				'kdp'					=> $this->content_uiconds_clipapp_kdp->getId(),
				'kclip'					=> $this->content_uiconds_clipapp_kclip->getId(),
			),
			'studio'					=> array(
                'version'				=> kConf::get("studio_version"),
                'uiConfID'				=> isset($this->content_uiconf_studio_v2) ? $this->content_uiconf_studio_v2->getId() : '',
                'config'				=> isset($this->content_uiconf_studio_v2) ? $this->content_uiconf_studio_v2->getConfig() : '',
                'showFlashStudio'		=> $showFlashStudio,
                'showHTMLStudio'		=> $showHTMLStudio,
            ),
			'liveanalytics'					=> array(
                'version'				=> kConf::get("liveanalytics_version"),
                'player_id'				=> isset($this->content_uiconf_livea) ? $this->content_uiconf_livea->getId() : '',
					
				'map_zoom_levels' => kConf::hasParam ("map_zoom_levels") ? kConf::get ("map_zoom_levels") : '',
			    'map_urls' => kConf::hasParam ("cdn_static_hosts") ? array_map(function($s) {return "$s/content/static/maps/v1";}, kConf::get ("cdn_static_hosts")) : '',
            ),
			'usagedashboard'			=> array(
				'version'				=> kConf::get("usagedashboard_version"),
			),
			'liveDashboard'             => array(
                'version'				=> kConf::get("live_dashboard_version"),
				'uiConfId'				=> isset($this->content_uiconf_lived) ? $this->content_uiconf_lived->getId() : '',
            ),
			'disable_analytics'			=> (bool) kConf::get("kmc_disable_analytics"),
			'google_analytics_account'	=> kConf::get("ga_account"),
			'language'					=> $language,
			'logoutUrl'					=> $logoutUrl,
			'allowFrame'				=> (bool) $allowFrame,
			'akamaiEdgeServerIpURL'		=> $akamaiEdgeServerIpURL,
			'logoUrl' 					=> kmcUtils::getWhitelabelData( $partner, 'logo_url'),
			'supportUrl' 				=> kmcUtils::getWhitelabelData( $partner, 'support_url'),
		);
		
		$this->kmcVars = $kmcVars;
	}

	private function stripProtocol( $url )
	{
		$url_data = parse_url( $url );
		if( $url_data !== false ){
			$port = (isset($url_data['port'])) ? ':' . $url_data['port'] : '';
			return $url_data['host'] . $port;
		} else {
			return $url;
		}
	}
    
}
