<?php

require_once ( "kalturaAction.class.php" );

class kmc2Action extends kalturaAction
{
	public function execute ( ) 
	{
		
		sfView::SUCCESS;

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

		$this->allow_reports = false;

		if (!$this->ks)
		{
			$this->redirect( "kmc/kmc" );
			die();
		}

//		$this->beta = $this->getRequestParameter( "beta" );
		
		$this->embed_code  = "";
		$this->ui_conf_width = "";
		$this->ui_conf_height = "";
		if ( $this->partner_id !== null )
		{
			$widget = widgetPeer::retrieveByPK( "_" . $this->partner_id );
			if ( $widget )
			{
				$this->embed_code = $widget->getWidgetHtml( "kaltura_player" );
				
				$ui_conf = $widget->getuiConf();
//				$this->ui_conf_width = 0; // $ui_conf->getWidth();
//				$this->ui_conf_height = 0 ; // $ui_conf->getHeight();
			}
		}
		
		$this->partner = $partner = null;
		$this->templatePartnerId = 0;
		if ($this->partner_id !== NULL)
		{
			$this->partner = $partner = PartnerPeer::retrieveByPK($this->partner_id);
			kmcUtils::redirectPartnerToCorrectKmc($partner, $this->ks, $this->uid, $this->screen_name, $this->email, 2);
			$this->templatePartnerId = $this->partner ? $this->partner->getTemplatePartnerId() : 0;
		}
		
		$this->payingPartner = 'false';
		if($partner && $partner->getPartnerPackage() != PartnerPackages::PARTNER_PACKAGE_FREE)
		{
			$this->payingPartner = 'true';
		}
		
		$this->enable_live_streaming = 'false';
		if(kConf::get('kmc_content_enable_live_streaming') && $partner)
		{
			if ($partner->getLiveStreamEnabled() && $partner->getKmcVersion() == 3)
			{
				$this->enable_live_streaming = 'true';
			}
		}

		// this is Andromeda kmc2Action - following are irrelevant so we set them to false & empty
		// just to make sure they don't get a black-eye value
		$this->enable_live_streaming = 'false';
		$this->silverLightPlayerUiConfs = array();
		$this->silverLightPlaylistUiConfs = array();
/*
		// remarked - no silverlight players in Andromeda
		if($partner->getKmcVersion() == 3)
		{
			$this->silverLightPlayerUiConfs = kmcUtils::getSilverLightPlayerUiConfs('slp');
			$this->silverLightPlaylistUiConfs = kmcUtils::getSilverLightPlayerUiConfs('sll');
		}
*/
		
		// 2009-08-27 is the date we added ON2 to KMC trial account
		// TODO - should be depracated
		if($partner && strtotime($partner->getCreatedAt()) >= strtotime('2009-08-27') ||
		   $partner->getEnableAnalyticsTab())
		{
			$this->allow_reports = true;
		}
		if($partner->getEnableAnalyticsTab())
		{
			$this->allow_reports = true;
		}
		
		// set content kdp version according to partner id
		$moderated_partners = array( 31079, 28575, 32774 );
		$this->content_kdp_version = 'v2.7.0';
		if(in_array($this->partner_id, $moderated_partners))
		{
			$this->content_kdp_version = 'v2.1.2.29057';
		}
		
		$this->playlist_uiconf_list = $this->getUiconfList('playlist');

		$this->player_uiconf_list = $this->getUiconfList('player');


		$this->first_login = false;
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

		// if the email is empty - it is an indication that the kaltura super user is logged in
		if ( !$this->email) $this->allow_reports = true;   
		
		/* applications versioning */
		$this->kmc_content_version 	= 'v2.1.6.1';
		$this->kmc_account_version 	= 'v2.1.2.3';
		$this->kmc_appstudio_version 	= 'v2.0.4';
		$this->kmc_rna_version 		= 'v1.1.3';
		$this->kmc_dashboard_version 	= 'v1.0.10';
		
		$this->jw_uiconfs_array = kmcUtils::getJWPlayerUIConfs();
		$this->jw_uiconf_playlist = kmcUtils::getJWPlaylistUIConfs();
		$this->advanced_editor = $this->getAdvancedEditorUiConf();
		$this->simple_editor = $this->getSimpleEditorUiConf();
	}

	private function getAdvancedEditorUiConf()
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
	
	private function getSimpleEditorUiConf()
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
		$template_partner_id = (isset($this->templatePartnerId))? $this->templatePartnerId: 0;
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
		
		$c->addAnd ( uiConfPeer::ID, array(48120, 48121, 48130, 48131, 48132, 48133, 48134, 48135), Criteria::NOT_IN);
		
		$order_by = "(" . uiConfPeer::PARTNER_ID . "=".$this->partner_id.")";
		$c->addAscendingOrderByColumn ( $order_by );
		$c->addDescendingOrderByColumn(uiConfPeer::CREATED_AT);
		
		$confs = uiConfPeer::doSelect($c);
		return $confs;
	}
}
?>
