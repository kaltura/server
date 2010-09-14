<?php
class kmcUtils
{
	public static function getJWPlayerUIConfs()
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
				//'swfUrlVersion' => $conf->getSwfUrlVersion(),
			);
		}
		return $conf_players;
	}

	public static function getJWPlaylistUIConfs()
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
				//'swfUrlVersion' => $conf->getSwfUrlVersion(),
			);
		}
		return $conf_playlist;
	}

	public static function redirectPartnerToCorrectKmc(Partner $partner, $ks, $uid, $screenName, $email, $validatingKmc)
	{
		if($validatingKmc == $partner->getKmcVersion())
		{
			return true;
		}
		$subpId = $partner->getId()*100;
		switch($partner->getKmcVersion())
		{
		    case 1: 
			$kmc1 = "/index.php/kmc/kmc1?partner_id=".$partner->getId()."&subp_id=".$subpId."&ks=".$ks."&uid=".$uid."&screen_name=".$screenName."&email=".$email ;
			header("Location: ".$kmc1);
			die;
			break;
		    case 2:
			$kmc2 = "/index.php/kmc/kmc2";
			header("Location: ".$kmc2);
			die;
			break;
		    case 3:
			$kmc3 = "/index.php/kmc/kmc3";
			header("Location: ".$kmc3);
			die;
			break;
		}
	}
	
	public static function getPartnersUiconfs($partner_id, $tag)
	{
		$c = new Criteria();
		$c->addAnd(uiConfPeer::PARTNER_ID, $partner_id);
		$c->addAnd(uiConfPeer::OBJ_TYPE, array(uiConf::UI_CONF_TYPE_WIDGET, uiConf::UI_CONF_TYPE_KDP3), Criteria::IN);
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::TAGS, '%'.$tag.'%', Criteria::LIKE );
		$c->addAnd ( uiConfPeer::TAGS, '%jw'.$tag.'%', Criteria::NOT_LIKE );
		$c->addDescendingOrderByColumn(uiConfPeer::CREATED_AT);
		
		$confs = uiConfPeer::doSelect($c);
		return $confs;
	}
	
	public static function getAllKMCUiconfs($module_tag, $module_version, $template_partner_id)
	{
		$c = new Criteria();
		$c->addAnd(uiConfPeer::PARTNER_ID, $template_partner_id);
		$c->addAnd(uiConfPeer::TAGS, "%".$module_tag."\_".$module_version."%", Criteria::LIKE);
		$c->addAnd(uiConfPeer::TAGS, "%autodeploy%", Criteria::LIKE);
		return uiConfPeer::doSelect($c);
	}
	
	public static function find_confs_by_usage_tag($confs, $tag, $allow_array = false, $alternateConfs = array())
	{
	  $uiconfs = array();
	  foreach($confs as $uiconf)
	  {
	    $tags = explode(",", $uiconf->getTags());
	    $trimmed_tags = kmcUtils::TrimArray($tags);
	    if(in_array($tag, $trimmed_tags))
	    {
		if($allow_array)
		{
			$uiconfs[] = $uiconf;
		}
		else
		{
			return $uiconf;
		}
	    }
	  }
	  
	  if($allow_array)
	  {
		// if we didnt find uiconfs and we have alternate uiconf list -
		// 	call myself with the alternate uiconfs, return whatever was returned.
		if(!count($uiconfs) && count($alternateConfs))
		{
			return self::find_confs_by_usage_tag($alternateConfs, $tag, $allow_array);
		}
		// we either found uiconfs from the template or we didn't find but we don't have alternate
		return $uiconfs;
	  }
	  
	  // requested single and not array, and no valid uiconf found. try calling myself with alternate
	  if(!count($alternateConfs))
	  {
		return new uiConf();
		
	  }
	  else
	  {
		return self::find_confs_by_usage_tag($alternateConfs, $tag, $allow_array);
	  }
	}
	
	public static function getKdp508PlayerUiconfs()
	{
		$confs = array();
		// implement query to get uiconfs from DB
		$c = new Criteria();
		$c->addAnd( uiConfPeer::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK , Criteria::GREATER_EQUAL );
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::OBJ_TYPE , uiConf::UI_CONF_TYPE_KDP3);
		$c->addAnd ( uiConfPeer::TAGS, 'kdp508', Criteria::LIKE);
		$c->addAscendingOrderByColumn(uiConfPeer::ID);

		$k508Players = uiConfPeer::doSelect($c);

		$conf_players = array();
		foreach($k508Players as $conf)
		{
			$conf_players[] = array(
				'id' => $conf->getId(),
				'name' => $conf->getName(),
				'width' => (($conf->getWidth())? $conf->getWidth(): 470),
				'height' => (($conf->getHeight())? $conf->getHeight(): 600),
				'swfUrlVersion' => $conf->getSwfUrlVersion(),
			);
		}
		return $conf_players;
	}

	public static function getSilverLightPlayerUiConfs($tag)
	{
		$confs = array();
		// implement query to get uiconfs from DB
		$c = new Criteria();
		$c->addAnd( uiConfPeer::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK , Criteria::GREATER_EQUAL );
		$c->addAnd ( uiConfPeer::STATUS , uiConf::UI_CONF_STATUS_READY );
		$c->addAnd ( uiConfPeer::OBJ_TYPE , uiConf::UI_CONF_TYPE_SLP );
		$c->addAnd ( uiConfPeer::TAGS, $tag, Criteria::LIKE);
		$c->addAscendingOrderByColumn(uiConfPeer::ID);

		$slPlayers = uiConfPeer::doSelect($c);

		$conf_players = array();
		foreach($slPlayers as $conf)
		{
			$minRuntimeVersion = '';
			$custom_data = unserialize($conf->getCustomData());
			if($custom_data)
			{
				$minRuntimeVersion = $custom_data['minRuntimeVersion'];
			}
			$conf_players[] = array(
				'id' => $conf->getId(),
				'name' => $conf->getName(),
				'width' => (($conf->getWidth())? $conf->getWidth(): 470),
				'height' => (($conf->getHeight())? $conf->getHeight(): 600),
				'minRuntimeVersion' => $minRuntimeVersion,
				'swfUrlVersion' => self::getSlpUrlVersion($conf),
			);
		}
		return $conf_players;
	}

	private static function getSlpUrlVersion($conf)
	{
		$swf_url = $conf->getSwfUrl();
		$flash_url = myContentStorage::getFSFlashRootPath ();
		$match = preg_match ( "|$flash_url/slp[\d]*/v([\w\d\.]*)/|" , $swf_url , $version );
		if ( $match )
		{
			return $version[1];
		}
		return null;
	}
	
	public static function TrimArray($arr){
	    if (!is_array($arr)){ return $arr; }
	
	    while (list($key, $value) = each($arr)){
		if (is_array($value)){
		    $arr[$key] = kmcUtils::TrimArray($value);
		}
		else {
		    $arr[$key] = trim($value);
		}
	    }
	    return $arr;
	}

}