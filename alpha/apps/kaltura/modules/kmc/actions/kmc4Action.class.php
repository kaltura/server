<?php
/**
 * @package    Core
 * @subpackage KMC
 */
require_once ( "kalturaAction.class.php" );

/**
 * @package    Core
 * @subpackage KMC
 */
class kmc4Action extends kalturaAction
{
	const CURRENT_KMC_VERSION = 4;
	
	private $confs = array();
	
	const SYSTEM_DEFAULT_PARTNER = 0;
	
	public function execute ( ) 
	{
		
		sfView::SUCCESS;

	/** check parameters and verify user is logged-in **/
		$this->partner_id = $this->getP ( "pid" );
		$this->subp_id = $this->getP ( "subpid", ((int)$this->partner_id)*100 );
		$this->uid = $this->getP ( "uid" );
		$this->ks = $this->getP ( "kmcks" );
		if(!$this->ks)
		{
			// if kmcks from cookie doesn't exist, try ks from REQUEST
			$this->ks = $this->getP('ks');
		}
		$this->screen_name = $this->getP ( "screen_name" );
		$this->email = $this->getP ( "email" );

		
		/** if no KS found, redirect to login page **/
		if (!$this->ks)
		{
			$this->redirect( "kmc/kmc" );
			die();
		}
		$ksObj = kSessionUtils::crackKs($this->ks);
		
	/** END - check parameters and verify user is logged-in **/
		
	/** Get array of allowed partners for the current user **/
		$this->allowedPartners = array();
		$currentUser = kuserPeer::getKuserByPartnerAndUid($this->partner_id, $ksObj->user, true);
		if($currentUser) {
			$partners = myPartnerUtils::getPartnersArray($currentUser->getAllowedPartnerIds());
			foreach ($partners as $partner)
				$this->allowedPartners[] = array('id' => $partner->getId(), 'name' => $partner->getName());
				
			$this->full_name = $currentUser->getFullName();
		}

	/** load partner from DB, and set templatePartnerId **/
		$this->partner = $partner = null;
		$this->templatePartnerId = self::SYSTEM_DEFAULT_PARTNER;
		$this->ignoreSeoLinks = false;
		$this->ignoreEntrySeoLinks = false;
		$this->useEmbedCodeProtocolHttps = false;
		$this->deliveryTypes = null;
		$this->embedCodeTypes = null;
		$this->defaultDeliveryType = 'http';
		$this->defaultEmbedCodeType = 'legacy';
		
		if ($this->partner_id !== NULL)
		{
			$this->partner = $partner = PartnerPeer::retrieveByPK($this->partner_id);
			kmcUtils::redirectPartnerToCorrectKmc($partner, $this->ks, $this->uid, $this->screen_name, $this->email, self::CURRENT_KMC_VERSION);
			$this->templatePartnerId = $this->partner ? $this->partner->getTemplatePartnerId() : self::SYSTEM_DEFAULT_PARTNER;
			$this->ignoreSeoLinks = $this->partner->getIgnoreSeoLinks();
			$this->ignoreEntrySeoLinks = PermissionPeer::isValidForPartner(PermissionName::FEATURE_IGNORE_ENTRY_SEO_LINKS, $this->partner_id);
			$this->useEmbedCodeProtocolHttps = PermissionPeer::isValidForPartner(PermissionName::FEATURE_EMBED_CODE_DEFAULT_PROTOCOL_HTTPS, $this->partner_id);
			$this->deliveryTypes = $partner->getDeliveryTypes();
			$this->embedCodeTypes = $partner->getEmbedCodeTypes();
			$this->defaultDeliveryType = $partner->getDefaultDeliveryType();
			$this->defaultEmbedCodeType = $partner->getDefaultEmbedCodeType();
		}
	/** END - load partner from DB, and set templatePartnerId **/

	/** set default flags **/
		$this->payingPartner = 'false';
		$this->kdp508_players = array();
		$this->first_login = false;
	/** END - set default flags **/
	
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
	$this->rtmp_host = myPartnerUtils::getRtmpUrl($this->partner_id);
	$this->flash_dir = $this->cdn_url . myContentStorage::getFSFlashRootPath ();

	/** set payingPartner flag **/
		if($partner && $partner->getPartnerPackage() != PartnerPackages::PARTNER_PACKAGE_FREE)
		{
			$this->payingPartner = 'true';
		}
	/** END - set payingPartner flag **/

	/** get partner languae **/
		$this->language = null; 
		if ($partner->getKMCLanguage())
			$this->language = $partner->getKMCLanguage();
	/** END - get partner languae **/		
	
	/** set first_login flag **/
		$this->first_login = $partner->getIsFirstLogin();
		if ($this->first_login === true)
		{
			$partner->setIsFirstLogin(false);
			$partner->save();
		}
	/** END - set first_login flag **/
		
	/** get logout url **/
		$this->logoutUrl = null; 
		if ($partner->getLogoutUrl())
			$this->logoutUrl = $partner->getLogoutUrl();
	/** END - get logout url**/	
		
	/** partner-specific: change KDP version for partners working with auto-moderaion **/
		// set content kdp version according to partner id
		$moderated_partners = array( 31079, 28575, 32774 );
		$this->content_kdp_version = 'v2.7.0';
		if(in_array($this->partner_id, $moderated_partners))
		{
			$this->content_kdp_version = 'v2.1.2.29057';
		}
	/** END - partner-specific: change KDP version for partners working with auto-moderaion **/
		
		$this->kmc_swf_version = kConf::get('kmc_version');
		
	/** uiconf listing work **/
		/** fill $this->confs with all uiconf objects for all modules **/
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

		/** content KCW,KSE,KAE **/
		//$this->content_uiconfs_upload = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_upload", false, $kmcGeneralUiConf);
		//$this->simple_editor = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_simpleedit", false, $kmcGeneralUiConf);
		//$this->advanced_editor = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_advanceedit", false, $kmcGeneralUiConf);
		
	/** END - uiconf listing work **/
		
		/** get templateXmlUrl for whitelabeled partners **/
		//$this->appstudio_templatesXmlUrl = $this->getAppStudioTemplatePath();
	}

	private function stripProtocol( $url )
	{
		$url_data = parse_url( $url );
		if( $url_data !== false ){
			$port = ($url_data['port']) ? ':' . $url_data['port'] : '';
			return $url_data['host'] . $port;
		} else {
			return $url;
		}
	}

	private function getAppStudioTemplatePath()
	{
		$template_partner_id = (isset($this->templatePartnerId))? $this->templatePartnerId: self::SYSTEM_DEFAULT_PARTNER;
		if (!$template_partner_id)
			return false;
	
		$c = new Criteria();
		$c->addAnd(uiConfPeer::PARTNER_ID, $template_partner_id );
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::OBJ_TYPE , uiConf::UI_CONF_TYPE_KMC_APP_STUDIO );
		$c->addAnd(uiConfPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
	
		$uiConf = uiConfPeer::doSelectOne($c);
		if ($uiConf)
		{
			$sync_key = $uiConf->getSyncKey( uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA );
			if ($sync_key)
			{
				$file_sync = kFileSyncUtils::getLocalFileSyncForKey( $sync_key , true );
				if ($file_sync)
				{
					return "/".$file_sync->getFilePath();
				}
			}
	
		}
	
		return false;
	}
    
}
