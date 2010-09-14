<?php

require_once ( "kalturaAction.class.php" );

class kmc3Action extends kalturaAction
{
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
	/** END - check parameters and verify user is logged-in **/

	/** load partner from DB, and set templatePartnerId **/
		$this->partner = $partner = null;
		$this->templatePartnerId = self::SYSTEM_DEFAULT_PARTNER;
		if ($this->partner_id !== NULL)
		{
			$this->partner = $partner = PartnerPeer::retrieveByPK($this->partner_id);
			kmcUtils::redirectPartnerToCorrectKmc($partner, $this->ks, $this->uid, $this->screen_name, $this->email, 3);
			$this->templatePartnerId = $this->partner ? $this->partner->getTemplatePartnerId() : self::SYSTEM_DEFAULT_PARTNER;
		}
	/** END - load partner from DB, and set templatePartnerId **/

	/** set default flags **/
		$this->allow_reports = false;
		$this->payingPartner = 'false';
		$this->embed_code  = "";
		$this->enable_live_streaming = 'false';
		$this->kmc_enable_custom_data = 'false';
		$this->kdp508_players = array();
		$this->first_login = false;
		$this->enable_vast = 'false';
	/** END - set default flags **/
	
	/** set values for template **/
	$this->service_url = myPartnerUtils::getHost($this->partner_id);
	$this->host = str_replace ( "http://" , "" , $this->service_url );
	$this->cdn_url = myPartnerUtils::getCdnHost($this->partner_id);
	$this->cdn_host = str_replace ( "http://" , "" , $this->cdn_url );
	$this->rtmp_host = myPartnerUtils::getRtmpUrl($this->partner_id);
	$this->flash_dir = $this->cdn_url . myContentStorage::getFSFlashRootPath ();
		
	/** set embed_code value **/
		if ( $this->partner_id !== null )
		{
			$widget = widgetPeer::retrieveByPK( "_" . $this->partner_id );
			if ( $widget )
			{
				$this->embed_code = $widget->getWidgetHtml( "kaltura_player" );
				
				$ui_conf = $widget->getuiConf();
			}
		}
	/** END - set embed_code value **/

	/** set payingPartner flag **/
		if($partner && $partner->getPartnerPackage() != PartnerPackages::PARTNER_PACKAGE_FREE)
		{
			$this->payingPartner = 'true';
		}
	/** END - set payingPartner flag **/
		
	/** set enable_live_streaming flag **/
		if(kConf::get('kmc_content_enable_live_streaming') && $partner)
		{
			if ($partner->getLiveStreamEnabled())
			{
				$this->enable_live_streaming = 'true';
			}
		}
	/** END - set enable_live_streaming flag **/

	/** set enable_live_streaming flag **/
		if($partner && $partner->getEnableVast())
		{
			$this->enable_vast = 'true';
		}
	/** END - set enable_live_streaming flag **/
		
	/** set kmc_enable_custom_data flag **/
		$defaultPlugins = kConf::get('default_plugins');
		if(is_array($defaultPlugins) && in_array('MetadataPlugin', $defaultPlugins) && $partner)
		{
			if ($partner->getPluginEnabled(MetadataPlugin::PLUGIN_NAME) && $partner->getKmcVersion() == 3)
			{
				$this->kmc_enable_custom_data = 'true';
			}
		}
	/** END - set kmc_enable_custom_data flag **/

	/** set allow_reports flag **/
		// 2009-08-27 is the date we added ON2 to KMC trial account
		// TODO - should be depracated
		if(strtotime($partner->getCreatedAt()) >= strtotime('2009-08-27') ||
		   $partner->getEnableAnalyticsTab())
		{
			$this->allow_reports = true;
		}
		if($partner->getEnableAnalyticsTab())
		{
			$this->allow_reports = true;
		}
		// if the email is empty - it is an indication that the kaltura super user is logged in
		if ( !$this->email) $this->allow_reports = true;
	/** END - set allow_reports flag **/
	
	/** set first_login and jw_license flags **/
		if ($partner)
		{
			$this->first_login = $partner->getIsFirstLogin();
			if ($this->first_login === true)
			{
				$partner->setIsFirstLogin(false);
				$partner->save();
			}
			$this->jw_license = $partner->getLicensedJWPlayer();
		}
	/** END - set first_login and jw_license flags **/
		
	/** partner-specific: change KDP version for partners working with auto-moderaion **/
		// set content kdp version according to partner id
		$moderated_partners = array( 31079, 28575, 32774 );
		$this->content_kdp_version = 'v2.7.0';
		if(in_array($this->partner_id, $moderated_partners))
		{
			$this->content_kdp_version = 'v2.1.2.29057';
		}
	/** END - partner-specific: change KDP version for partners working with auto-moderaion **/
		
	/** applications versioning **/
		$this->kmc_content_version 	= kConf::get('kmc_content_version');
		$this->kmc_account_version 	= kConf::get('kmc_account_version');
		$this->kmc_appstudio_version 	= kConf::get('kmc_appstudio_version');
		$this->kmc_rna_version 		= kConf::get('kmc_rna_version');
		$this->kmc_dashboard_version 	= kConf::get('kmc_dashboard_version');
	/** END - applications versioning **/
		
	/** uiconf listing work **/
		/** fill $this->confs with all uiconf objects for all modules **/
		$contentSystemUiConfs = kmcUtils::getAllKMCUiconfs('content',   $this->kmc_content_version, self::SYSTEM_DEFAULT_PARTNER);
		$contentTemplateUiConfs = kmcUtils::getAllKMCUiconfs('content',   $this->kmc_content_version, $this->templatePartnerId);
		//$this->confs = kmcUtils::getAllKMCUiconfs('content',   $this->kmc_content_version, $this->templatePartnerId);
		$appstudioSystemUiConfs = kmcUtils::getAllKMCUiconfs('appstudio', $this->kmc_appstudio_version, self::SYSTEM_DEFAULT_PARTNER);
		$appstudioTemplateUiConfs = kmcUtils::getAllKMCUiconfs('appstudio', $this->kmc_appstudio_version, $this->templatePartnerId);
		//$this->confs = array_merge($this->confs, kmcUtils::getAllKMCUiconfs('appstudio', $this->kmc_appstudio_version, $this->templatePartnerId));
		$reportsSystemUiConfs = kmcUtils::getAllKMCUiconfs('reports',   $this->kmc_rna_version, self::SYSTEM_DEFAULT_PARTNER);
		$reportsTemplateUiConfs = kmcUtils::getAllKMCUiconfs('reports',   $this->kmc_rna_version, $this->templatePartnerId);
		//$this->confs = array_merge($this->confs, kmcUtils::getAllKMCUiconfs('reports',   $this->kmc_rna_version, $this->templatePartnerId));
		
		/** for each module, create separated lists of its uiconf, for each need **/
		/** content players: **/
		$this->content_uiconfs_previewembed = kmcUtils::find_confs_by_usage_tag($contentTemplateUiConfs, "content_previewembed", true, $contentSystemUiConfs);
		$this->content_uiconfs_previewembed_list = kmcUtils::find_confs_by_usage_tag($contentTemplateUiConfs, "content_previewembed_list", true, $contentSystemUiConfs);
		$this->content_uiconfs_moderation = kmcUtils::find_confs_by_usage_tag($contentTemplateUiConfs, "content_moderation", false, $contentSystemUiConfs);
		$this->content_uiconfs_drilldown = kmcUtils::find_confs_by_usage_tag($contentTemplateUiConfs, "content_drilldown", false, $contentSystemUiConfs);
		$this->content_uiconfs_flavorpreview = kmcUtils::find_confs_by_usage_tag($contentTemplateUiConfs, "content_flavorpreview", false, $contentSystemUiConfs);
		$this->content_uiconfs_metadataview = kmcUtils::find_confs_by_usage_tag($contentTemplateUiConfs, "content_metadataview", false, $contentSystemUiConfs);
		/** content KCW,KSE,KAE **/
		$this->content_uiconfs_upload = kmcUtils::find_confs_by_usage_tag($contentTemplateUiConfs, "content_upload", false, $contentSystemUiConfs);
		$this->simple_editor = kmcUtils::find_confs_by_usage_tag($contentTemplateUiConfs, "content_simpleedit", false, $contentSystemUiConfs);
		$this->advanced_editor = kmcUtils::find_confs_by_usage_tag($contentTemplateUiConfs, "content_advanceedit", false, $contentSystemUiConfs);
		
		/** appStudio templates uiconf **/
		$this->appstudio_uiconfs_templates = kmcUtils::find_confs_by_usage_tag($appstudioTemplateUiConfs, "appstudio_templates", false, $appstudioSystemUiConfs);
		
		/** reports drill-down player **/
		$this->reports_uiconfs_drilldown = kmcUtils::find_confs_by_usage_tag($reportsTemplateUiConfs, "reports_drilldown", false, $reportsSystemUiConfs);
		
		/** silverlight uiconfs **/
		$this->silverLightPlayerUiConfs = array();
		$this->silverLightPlaylistUiConfs = array();
		if($partner->getKmcVersion() == 3 && $partner->getEnableSilverLight())
		{
			$this->silverLightPlayerUiConfs = kmcUtils::getSilverLightPlayerUiConfs('slp');
			$this->silverLightPlaylistUiConfs = kmcUtils::getSilverLightPlayerUiConfs('sll');
		}

		/** jw uiconfs **/
		$this->jw_uiconfs_array = kmcUtils::getJWPlayerUIConfs();
		$this->jw_uiconf_playlist = kmcUtils::getJWPlaylistUIConfs();
		
		/** 508 uicinfs **/
		if($partner->getKmcVersion() == 3 && $partner->getEnable508Players())
		{
			$this->kdp508_players = kmcUtils::getKdp508PlayerUiconfs();
		}
		
		/** partner's preview&embed uiconfs **/
		$this->content_pne_partners_player = kmcUtils::getPartnersUiconfs($this->partner_id, 'player');
		$this->content_pne_partners_playlist = kmcUtils::getPartnersUiconfs($this->partner_id, 'playlist');
		
		/** appstudio: default entry and playlists **/
		$this->appStudioExampleEntry = $partner->getAppStudioExampleEntry();
		$appStudioExampleEntry = entryPeer::retrieveByPK($this->appStudioExampleEntry);
		if (!($appStudioExampleEntry && $appStudioExampleEntry->getDisplayInSearch() == mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK && $appStudioExampleEntry->getStatus()== entry::ENTRY_STATUS_READY &&	$appStudioExampleEntry->getType() == entry::ENTRY_TYPE_MEDIACLIP ))
			$this->appStudioExampleEntry = "_KMCLOGO1";
		
		$this->appStudioExamplePlayList0 = $partner->getAppStudioExamplePlayList0();
		$appStudioExamplePlayList0 = entryPeer::retrieveByPK($this->appStudioExamplePlayList0);		
		if (!($appStudioExamplePlayList0 && $appStudioExamplePlayList0->getStatus()== entry::ENTRY_STATUS_READY && $appStudioExamplePlayList0->getType() == entry::ENTRY_TYPE_PLAYLIST ))
			$this->appStudioExamplePlayList0 = "_KMCSPL1";
		
		$this->appStudioExamplePlayList1 = $partner->getAppStudioExamplePlayList1();
		$appStudioExamplePlayList1 = entryPeer::retrieveByPK($this->appStudioExamplePlayList1);
		if (!($appStudioExamplePlayList1 && $appStudioExamplePlayList1->getStatus()== entry::ENTRY_STATUS_READY && $appStudioExamplePlayList1->getType() == entry::ENTRY_TYPE_PLAYLIST ))
			$this->appStudioExamplePlayList1 = "_KMCSPL2";
		/** END - appstudio: default entry and playlists **/
		
	/** END - uiconf listing work **/
		
		/** get templateXmlUrl for whitelabeled partners **/
		$this->appstudio_templatesXmlUrl = $this->getAppStudioTemplatePath();
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
    
	/** TODO - remove Deprecated **/
	private function DEPRECATED_getAdvancedEditorUiConf()
	{
		$c = new Criteria();
		$c->addAnd( uiConfPeer::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK , Criteria::GREATER_EQUAL );
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::OBJ_TYPE , uiConf::UI_CONF_TYPE_ADVANCED_EDITOR );
		$c->addAnd ( uiConfPeer::TAGS, 'andromeda_kae_for_kmc', Criteria::LIKE);
		$c->addAscendingOrderByColumn(uiConfPeer::ID);

		$uiConf = uiConfPeer::doSelectOne($c);
		if ($uiConf)
			return $uiConf->getId();
		else
			return -1;
	}
	
	/** TODO - remove Deprecated **/
	private function DEPRECATED_getSimpleEditorUiConf()
	{
		$c = new Criteria();
		$c->addAnd( uiConfPeer::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK , Criteria::GREATER_EQUAL );
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::OBJ_TYPE , uiConf::UI_CONF_TYPE_EDITOR );
		$c->addAnd ( uiConfPeer::TAGS, 'andromeda_kse_for_kmc', Criteria::LIKE);
		$c->addAscendingOrderByColumn(uiConfPeer::ID);

		$uiConf = uiConfPeer::doSelectOne($c);
		if ($uiConf)
			return $uiConf->getId();
		else
			return -1;
	}

	private function getCritria ( )
	{
		$c = new Criteria();
		
		// or belongs to the partner or a template  
		$criterion = $c->getNewCriterion( uiConfPeer::PARTNER_ID , $this->partner_id ) ; // or belongs to partner
		$criterion2 = $c->getNewCriterion( uiConfPeer::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK , Criteria::GREATER_EQUAL );	// or belongs to kaltura_network == templates
		
		$criterion2partnerId = $c->getNewCriterion(uiConfPeer::PARTNER_ID, $this->templatePartnerId);
		$criterion2->addAnd($criterion2partnerId);  
		
		$criterion->addOr ( $criterion2 ) ;
		$c->addAnd ( $criterion );
		
		$c->addAnd ( uiConfPeer::OBJ_TYPE , uiConf::UI_CONF_TYPE_WIDGET );	//	only ones that are of type WIDGET
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY ); 	//	display only ones that are ready - not deleted or in draft mode
		
		
		$order_by = "(" . uiConfPeer::PARTNER_ID . "={$this->partner_id})";  // first take the templates  and then the rest
		$c->addAscendingOrderByColumn ( $order_by );//, Criteria::CUSTOM );

		return $c;
	}
	
	private function getUiconfList($tag = 'player')
	{
		$template_partner_id = (isset($this->templatePartnerId))? $this->templatePartnerId: self::SYSTEM_DEFAULT_PARTNER;
		$c = new Criteria();
		$crit_partner = $c->getNewCriterion(uiConfPeer::PARTNER_ID, $this->partner_id);
		 $crit_default = $c->getNewCriterion(uiConfPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK, Criteria::GREATER_EQUAL);
		
		$crit_default_partner_id = $c->getNewCriterion(uiConfPeer::PARTNER_ID, $template_partner_id);
		$crit_default_swf_url = $c->getNewCriterion(uiConfPeer::SWF_URL, '%/kdp3/%kdp3.swf', Criteria::LIKE);
		$crit_default->addAnd($crit_default_partner_id);
		$crit_default->addAnd($crit_default_swf_url);
		
		$crit_partner->addOr($crit_default);
		$c->add($crit_partner);
		$c->addAnd(uiConfPeer::OBJ_TYPE, array(uiConf::UI_CONF_TYPE_WIDGET, uiConf::UI_CONF_TYPE_KDP3), Criteria::IN);
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::TAGS, '%'.$tag.'%', Criteria::LIKE );
		$c->addAnd ( uiConfPeer::TAGS, '%jw'.$tag.'%', Criteria::NOT_LIKE );
		
		$c->addAnd ( uiConfPeer::ID, array(48501, 48502, 48504, 48505), Criteria::NOT_IN );
		
		$order_by = "(" . uiConfPeer::PARTNER_ID . "=".$this->partner_id.")";
		$c->addAscendingOrderByColumn ( $order_by );
		$c->addDescendingOrderByColumn(uiConfPeer::CREATED_AT);
		
		$confs = uiConfPeer::doSelect($c);
		return $confs;
	}	
}
?>
