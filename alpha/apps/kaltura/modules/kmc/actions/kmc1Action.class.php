<?php
// TEST PAGE FOR ADDING JW TO KMC

require_once ( "kalturaAction.class.php" );

class kmc1Action extends kalturaAction
{
	public function execute ( ) 
	{
		
		sfView::SUCCESS;
		$this->module = $this->getP ( "module" , "dashboard" );
		$this->partner_id = $this->getP ( "partner_id" );
		$this->subp_id = $this->getP ( "subp_id" );
		$this->uid = $this->getP ( "uid" );
		$this->ks = $this->getP ( "ks" );
		$this->screen_name = $this->getP ( "screen_name" );
		$this->email = $this->getP ( "email" );
		
		$this->allow_reports = false;
		
		$this->beta = $this->getRequestParameter( "beta" );
		
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
			kmcUtils::redirectPartnerToCorrectKmc($partner, $this->ks, $this->uid, $this->screen_name, $this->email, 1);
			$this->templatePartnerId = $this->partner ? $this->partner->getTemplatePartnerId() : 0;
		}

		$this->payingPartner = 'false';
		if($partner && $partner->getPartnerPackage() != PartnerPackages::PARTNER_PACKAGE_FREE)
		{
			$this->payingPartner = 'true';
		}

		$this->visibleCT = 'false';
		if(kConf::get('kmc_content_enable_commercial_transcoding') && $partner)
		{
			// 2009-08-27 is the date we added ON2 to KMC trial account
			if ($partner->getPartnerPackage() != PartnerPackages::PARTNER_PACKAGE_FREE ||
			    ($partner->getType() == 1 && strtotime($partner->getCreatedAt()) >= strtotime('2009-08-27')) )
			{
				$this->visibleCT = 'true';
			}
		}
		
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
		
		// set content kdp version according to partner id
		$moderated_partners = array( 31079, 28575, 32774 );
		$this->content_kdp_version = 'v2.7.0';
		if(in_array($this->partner_id, $moderated_partners))
		{
			$this->content_kdp_version = 'v2.1.2.29057';
		}
		
		/*
		$c = $this->getCritria();
		$c->addAnd ( uiConfPeer::TAGS, "%playlist%" , Criteria::LIKE ); //
		$c->addAnd ( uiConfPeer::TAGS, "%jwplaylist%" , Criteria::NOT_LIKE ); //
		*/
		$this->playlist_uiconf_list = $this->getUiconfList('playlist');

		/*
		$c = $this->getCritria();
		$c->addAnd ( uiConfPeer::TAGS, "%player%" , Criteria::LIKE ); //
		$c->addAnd ( uiConfPeer::TAGS, "%jwplayer%" , Criteria::NOT_LIKE ); //
		*/
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
		$this->kmc_content_version 	= 'v1.1.11';
		$this->kmc_account_version 	= 'v1.1.7';
		$this->kmc_appstudio_version 	= 'v1.2.4';
		$this->kmc_rna_version 		= 'v1.0.5';
		$this->kmc_dashboard_version 	= 'v1.0.1';
		
		$this->jw_uiconfs_array = $this->getJWPlayerUIConfs();
		$this->jw_uiconf_playlist = $this->getJWPlaylistUIConfs();
		
		if ( ! $this->module )
		{
			$this->redirect( "kmc/kmc" );
			die();
		}
	}

	function getJWPlayerUIConfs()
	{
		$c = new Criteria();
		$c->addAnd( uiConfPeer::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK , Criteria::GREATER_EQUAL );
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::OBJ_TYPE , uiConf::UI_CONF_TYPE_WIDGET );
		$c->addAnd ( uiConfPeer::TAGS, 'jwplayer', Criteria::LIKE);
		$c->addAscendingOrderByColumn(uiConfPeer::ID);
		
		$jwPlayers = uiConfPeer::doSelect($c);
		
		$conf_players = array();
		foreach($jwPlayers as $conf)
		{
			$skin = '';
			$share = false;
			$custom_data = unserialize($conf->getCustomData());
			if($custom_data)
			{
				$skin = $custom_data['skin'];
				$share = $custom_data['share'];
			}
			$conf_players[] = array(
				'id' => $conf->getId(),
				'name' => $conf->getName(),
				'width' => (($conf->getWidth())? $conf->getWidth(): 470),
				'height' => (($conf->getHeight())? $conf->getHeight(): 600),
				'skin' => $skin,
				'share' => $share,
			);
		}
		return $conf_players;
	}

	function getJWPlaylistUIConfs()
	{
		$c = new Criteria();
		$c->addAnd( uiConfPeer::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK , Criteria::GREATER_EQUAL );
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::OBJ_TYPE , uiConf::UI_CONF_TYPE_WIDGET );
		$c->addAnd ( uiConfPeer::TAGS, 'jwplaylist', Criteria::LIKE);
		$c->addAscendingOrderByColumn(uiConfPeer::ID);

		$jwPlaylists = uiConfPeer::doSelect($c);
		
		$conf_playlist = array();
		foreach($jwPlaylists as $conf)
		{
			$skin = '';
			$share = false;
			$playlistType = 'bottom';
			$custom_data = unserialize($conf->getCustomData());
			if($custom_data)
			{
				$skin = $custom_data['skin'];
				$share = $custom_data['share'];
				$playlistType = ($custom_data['playlistType'])? $custom_data['playlistType']:$playlistType;
			}
			$conf_playlist[] = array(
				'id' => $conf->getId(),
				'name' => $conf->getName(),
				'width' => (($conf->getWidth())? $conf->getWidth(): 470),
				'height' => (($conf->getHeight())? $conf->getHeight(): 600),
				'skin' => $skin,
				'share' => $share,
				'playlistType' => $playlistType,
			);
		}
		return $conf_playlist;
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
		$crit_default_swf_url = $c->getNewCriterion(uiConfPeer::SWF_URL, '%/kdp3/%kdp3.swf', Criteria::NOT_LIKE);
		$crit_default->addAnd($crit_default_partner_id);
		$crit_default->addAnd($crit_default_swf_url);
		
		$crit_partner->addOr($crit_default);
		$c->add($crit_partner);
		$c->addAnd(uiConfPeer::OBJ_TYPE, uiConf::UI_CONF_TYPE_WIDGET);
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::TAGS, '%'.$tag.'%', Criteria::LIKE );
		$c->addAnd ( uiConfPeer::TAGS, '%jw'.$tag.'%', Criteria::NOT_LIKE );
		
		$order_by = "(" . uiConfPeer::PARTNER_ID . "=".$this->partner_id.")";
		$c->addAscendingOrderByColumn ( $order_by );
		
		$confs = uiConfPeer::doSelect($c);
		return $confs;
	}	
}
?>
