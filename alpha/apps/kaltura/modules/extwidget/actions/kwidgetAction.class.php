<?php
require_once ( MODULES . "/partnerservices2/actions/getwidgetAction.class.php" );

function klog ( $str )
{
	KalturaLog::log( "kwidgetAction: " . $str );
}

class kwidgetAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		$uv_cookie = @$_COOKIE['uv'];
		if (strlen($uv_cookie) != 35)
		{
			$uv_cookie = "uv_".md5(uniqid(rand(), true));
		}
		setrawcookie( 'uv', $uv_cookie, time() + 3600 * 24 * 365, '/' );

		// check if this is a request for the kdp without a wrapper
		// in case of an application loading the kdp (e.g. kmc)
		$nowrapper = $this->getRequestParameter( "nowrapper", false);
		
		// allow caching if either the cache start time (cache_st) parameter
		// wasn't specified or if it is past the specified time
		$cache_st = $this->getRequestParameter( "cache_st" );
		$allowCache = !$cache_st || $cache_st < time();

		$referer = @$_SERVER['HTTP_REFERER'];

		$externalInterfaceDisabled = (
		strstr($referer, "bebo.com") === false &&
		strstr($referer, "myspace.com") === false &&
		strstr($referer, "current.com") === false &&
		strstr($referer, "myyearbook.com") === false &&
		strstr($referer, "facebook.com") === false &&
		strstr($referer, "friendster.com") === false) ? "" : "&externalInterfaceDisabled=1";
		
		// if there is no wrapper the loader is responsible for setting extra params to the kdp
		$noncached_params = "";
		if (!$nowrapper)
			$noncached_params =	$externalInterfaceDisabled."&referer=".urlencode($referer);

		$requestKey = $_SERVER["REQUEST_URI"];
		
		// check if we cached the redirect url
		$cache = new myCache("kwidget", 10 * 60); // 10 minutes
		$cachedResponse  = $cache->get($requestKey);
		if ($allowCache && $cachedResponse) // dont use cache if we want to force no caching
		{
			header("X-Kaltura:cached-action");

			header("Expires: Thu, 19 Nov 2000 08:52:00 GMT");
			header( "Cache-Control" , "no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
			header( "Pragma" , "no-cache" );
			
			header("Location:$cachedResponse".$noncached_params);
				
			die;
		}
		
		// check if we cached the patched swf with flashvars
		$cache_swfdata = new myCache("kwidgetswf", 10 * 60); // 10 minutes
		$cachedResponse  = $cache_swfdata->get($requestKey);
		if ($allowCache && $cachedResponse) // dont use cache if we want to force no caching
		{
			header("X-Kaltura:cached-action");
			requestUtils::sendCdnHeaders("swf", strlen($cachedResponse), 60 * 10);
			echo $cachedResponse;
			die;
		}
		
		$widget_id = $this->getRequestParameter( "wid" );
		$show_version = $this->getRequestParameter( "v" );
		$debug_kdp = $this->getRequestParameter( "debug_kdp" , false );

		$widget = widgetPeer::retrieveByPK( $widget_id );

		if ( !$widget )
		{
			die();
		}

		// because of the routing rule - the entry_id & kmedia_type WILL exist. be sure to ignore them if smaller than 0
		$kshow_id= $widget->getKshowId();
		$entry_id= $widget->getEntryId();
		$gallery_widget = !$kshow_id && !$entry_id;

		if( !$entry_id  ) $entry_id = -1;

		if ( $widget->getSecurityType () != widget::WIDGET_SECURITY_TYPE_TIMEHASH  )
		{
			// try eid - if failed entry_id
			$eid = $this->getRequestParameter( "eid" , $this->getRequestParameter( "entry_id" ) );
			// try kid - if failed kshow_id
			$kid = $this->getRequestParameter( "kid" , $this->getRequestParameter( "kshow_id" ) );
			if ( $eid != null )
			$entry_id =  $eid ;
			// allow kshow to be overriden by dynamic one
			elseif ( $kid != null )
			$kshow_id = $kid ;
		}

		if ( $widget->getSecurityType () == widget::WIDGET_SECURITY_TYPE_MATCH_IP  )
		{
			$allowCache = false;

			// here we'll attemp to match the ip of the request with that from the customData of the widget
			$custom_data = $widget->getCustomData();
			$valid_country  = false;

			if ( $custom_data )
			{
				// in this case the custom_data should be of format:
				//  valid_county_1,valid_country_2,...,valid_country_n;falback_entry_id
				$arr = explode ( ";" , $custom_data );
				$countries_str = $arr[0]; 
				$fallback_entry_id = (isset($arr[1]) ? $arr[1] : null);
				$fallback_kshow_id = (isset($arr[2]) ? $arr[2] : null);
				$current_country = "";

				$valid_country = requestUtils::matchIpCountry( $countries_str , $current_country );
				if ( ! $valid_country )
				{
					KalturaLog::log ( "kwidgetAction: Attempting to access widget [$widget_id] and entry [$entry_id] from country [$current_country]. Retrning entry_id: [$fallback_entry_id] kshow_id [$fallback_kshow_id]" );
					$entry_id= $fallback_entry_id;
					$kshow_id = $fallback_kshow_id;
				}
			}
		}
		elseif ( $widget->getSecurityType () == widget::WIDGET_SECURITY_TYPE_FORCE_KS )
		{

		}


		$kmedia_type= -1;

		// support either uiconf_id or ui_conf_id
		$uiconf_id =  $this->getRequestParameter( "uiconf_id" );
		if ( !$uiconf_id ) $uiconf_id =  $this->getRequestParameter( "ui_conf_id" );

		if ( $uiconf_id )
		{
			$widget_type = $uiconf_id;
			$uiconf_id_str = "&uiconf_id=$uiconf_id";
		}
		else
		{
			$widget_type = $widget->getUiConfId() ;
			$uiconf_id_str = "";
		}


		if ( empty ( $widget_type ) )
		$widget_type = 3;
		$kdata = $widget->getCustomData();

		$partner_host = myPartnerUtils::getHost($widget->getPartnerId());
		$partner_cdnHost = myPartnerUtils::getCdnHost($widget->getPartnerId());

		$host = $partner_host;

		if ( $widget_type == 10)
		$swf_url = $host . "/swf/weplay.swf";
		else
		$swf_url = $host . "/swf/kplayer.swf";

		$partner_id = $widget->getPartnerId();
		$subp_id = $widget->getSubpId();
		if (!$subp_id)
		$subp_id = 0;

		$uiConf = uiConfPeer::retrieveByPK($widget_type);
		// new ui_confs which are deleted should stop the script
		// the check for >100000 is for supporting very old mediawiki and such players
		if (!$uiConf && $widget_type>100000)
	        die;
	        
		if ($uiConf)
		{
			$ui_conf_swf_url = $uiConf->getSwfUrl();
			if( kString::beginsWith( $ui_conf_swf_url , "http") )
			{
				$swf_url = 	$ui_conf_swf_url; // absolute URL
			}
			else
			{
				$use_cdn = $uiConf->getUseCdn();
				$host = $use_cdn ?  $partner_cdnHost : $partner_host;
				$swf_url =  $host . myPartnerUtils::getUrlForPartner ( $partner_id , $subp_id ) . $ui_conf_swf_url;
			}

			if ( $debug_kdp )
			{
				$swf_url = str_replace( "/kdp/" , "/kdp_debug/" , $swf_url );
			}
		}

		if ( $show_version < 0 )
		$show_version = null;


		$ip = requestUtils::getRemoteAddress();// to convert back, use long2ip

		// the widget log should change to reflect the new data, but for now - i used $widget_id instead of the widgget_type
		//		WidgetLog::createWidgetLog( $referer , $ip , $kshow_id , $entry_id , $kmedia_type , $widget_id );

		if ( $entry_id == -1 ) $entry_id = null;

		$kdp3 = false;
		$base_wrapper_swf = myContentStorage::getFSFlashRootPath ()."/kdpwrapper/".kConf::get('kdp_wrapper_version')."/kdpwrapper.swf";
		$widgetIdStr = "widget_id=$widget_id";
		$partnerIdStr = "partner_id=$partner_id&subp_id=$subp_id";
		
		if ($uiConf)
		{
			$ks_flashvars = "";
			$conf_vars = $uiConf->getConfVars();
			if ($conf_vars)
			$conf_vars = "&".$conf_vars;

			$wrapper_swf = $base_wrapper_swf;

			$partner = PartnerPeer::retrieveByPK($partner_id);

			if( $partner )
			{
				$partner_type = $partner->getType();
			}

			if (version_compare($uiConf->getSwfUrlVersion(), "3.0", ">="))
			{
				$kdp3 = true;
				// further in the code, $wrapper_swf is being used and not $base_wrapper_swf
				$wrapper_swf = $base_wrapper_swf = myContentStorage::getFSFlashRootPath ()."/kdp3wrapper/v32.0/kdp3wrapper.swf";
				$widgetIdStr = "widgetId=$widget_id";
				$uiconf_id_str = "&uiConfId=$uiconf_id";
				$partnerIdStr = "partnerId=$partner_id&subpId=$subp_id";

			}
			
			// if we are loaded without a wrapper (directly in flex)
			// 1. dont create the ks - keep url the same for caching
			// 2. dont patch the uiconf - patching is done only to wrapper anyway
			if ($nowrapper)
			{
				$dynamic_date = 
					$widgetIdStr.
					"&host=" . str_replace("http://", "", str_replace("https://", "", $partner_host)).
					"&cdnHost=" . str_replace("http://", "", str_replace("https://", "", $partner_cdnHost)).
					$uiconf_id_str  . // will be empty if nothing to add
					$conf_vars;

				$url = "$swf_url?$dynamic_date";
			}
			else
			{
				// if kdp version >= 2.5
				if (version_compare($uiConf->getSwfUrlVersion(), "2.5", ">="))
				{
					// create an anonymous session
					$ks = "";
					$result = kSessionUtils::createKSessionNoValidations ( $partner_id , 0 , $ks , 86400 , false , "" , "view:*" );
					$ks_flashvars = "&$partnerIdStr&uid=0&ts=".microtime(true);
					if($widget->getSecurityType () != widget::WIDGET_SECURITY_TYPE_FORCE_KS)
					{
						$ks_flashvars = "&ks=$ks".$ks_flashvars;
					}
					
		
					// patch kdpwrapper with getwidget and getuiconf

					$root = myContentStorage::getFSContentRootPath();
					$confFile_mtime = $uiConf->getUpdatedAt(null);
					$new_swf_path = "widget_{$widget_id}_{$widget_type}_{$confFile_mtime}_".md5($base_wrapper_swf.$swf_url).".swf";
					$md5 = md5($new_swf_path);
					$new_swf_path = "content/cacheswf/".substr($md5, 0, 2)."/".substr($md5, 2, 2)."/".$new_swf_path;
					
					$cached_swf = "$root/$new_swf_path";

					if (!file_exists($cached_swf) || filemtime($cached_swf) < $confFile_mtime)
					{
						kFile::fullMkdir($cached_swf);
						require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "api_v3" . DIRECTORY_SEPARATOR . "bootstrap.php");
						$dispatcher = KalturaDispatcher::getInstance();
						try
						{
							$widget_result = $dispatcher->dispatch("widget", "get", array("ks"=> $ks, "id" => $widget_id));
							$ui_conf_result = $dispatcher->dispatch("uiConf", "get", array("ks"=> $ks, "id" => $widget_type));
						}
						catch(Exception $ex)
						{
							die;
						}
							
						$serializer = new KalturaXmlSerializer(false);
						$serializer->serialize($widget_result);
						$widget_xml = $serializer->getSerializedData();

						$serializer = new KalturaXmlSerializer(false);
						$serializer->serialize($ui_conf_result);
						$ui_conf_xml = $serializer->getSerializedData();
						$patcher = new kPatchSwf( $root . $base_wrapper_swf);
						$result = "<xml><result>$widget_xml</result><result>$ui_conf_xml</result></xml>";
						$patcher->patch($result, $cached_swf);
					}

				if (file_exists($cached_swf))
				{
					$wrapper_swf = $new_swf_path;
				}
			}
			

			$kdp_version_2 = strpos($swf_url, "kdp/v2." ) > 0;
			if ($partner_host == "http://www.kaltura.com" && !$kdp_version_2 && !$kdp3)
			{
				$partner_host = 1; // otherwise the kdp will try going to cdnwww.kaltura.com
			}
			
			$track_wrapper = '';
			if (kConf::get('track_kdpwrapper') && kConf::get('kdpwrapper_track_url')) {
				$track_wrapper = "&wrapper_tracker_url=".urlencode(kConf::get('kdpwrapper_track_url')."?activation_key=".kConf::get('kaltura_activation_key')."&package_version=".kConf::get('kaltura_version'));
			}
			
			$dynamic_date = $widgetIdStr .
			$track_wrapper.
				"&kdpUrl=".urlencode($swf_url).
				"&host=" . str_replace("http://", "", str_replace("https://", "", $partner_host)).
				"&cdnHost=" . str_replace("http://", "", str_replace("https://", "", $partner_cdnHost)).
				( $show_version ? "&entryVersion=$show_version" : "" ) .
				( $kshow_id ? "&kshowId=$kshow_id" : "" ).
				( $entry_id ? "&entryId=$entry_id" : "" ) .
				$uiconf_id_str  . // will be empty if nothing to add
				$ks_flashvars.
				($cache_st ? "&clientTag=cache_st:$cache_st" : "").
				$conf_vars;

				// for now changed back to $host since kdp version prior to 1.0.15 didnt support loading by external domain kdpwrapper
				$url =  $host . myPartnerUtils::getUrlForPartner( $partner_id , $subp_id ) . "/$wrapper_swf?$dynamic_date";
				
				// patch wrapper with flashvars and dump to browser
				if (version_compare($uiConf->getSwfUrlVersion(), "2.6.6", ">="))
				{
					$patcher = new kPatchSwf( $root . $wrapper_swf, "KALTURA_FLASHVARS_DATA");
					ob_start();
					$patcher->patch($dynamic_date."&referer=".urlencode($referer));
					$wrapper_data = ob_get_contents();
					ob_end_clean();
	
					requestUtils::sendCdnHeaders("swf", strlen($wrapper_data), $allowCache ? 60 * 10 : 0);
					echo $wrapper_data;
					
					if ($allowCache)
					{
						$cache_swfdata->put($requestKey, $wrapper_data);
					}
					die;
				}
			}
		}
		else
		{
			$dynamic_date = "kshowId=$kshow_id" .
			"&host=" . requestUtils::getRequestHostId() .
			( $show_version ? "&entryVersion=$show_version" : "" ) .
			( $entry_id ? "&entryId=$entry_id" : "" ) .
			( $entry_id ? "&KmediaType=$kmedia_type" : "");
			$dynamic_date .= "&isWidget=$widget_type&referer=".urlencode($referer);
			$dynamic_date .= "&kdata=$kdata";
			$url = "$swf_url?$dynamic_date";
		}

		// if referer has a query string an IE bug will prevent out flashvars to propagate
		// when nowrapper is true we cant use /swfparams either as there isnt a kdpwrapper
		if (!$nowrapper && $uiConf && version_compare($uiConf->getSwfUrlVersion(), "2.6.6", ">="))
		{
			// apart from the /swfparam/ format, add .swf suffix to the end of the stream in case
			// a corporate firewall looks at the file suffix
			$pos = strpos($url, "?");
			$url = substr($url, 0, $pos)."/swfparams/".urlencode(substr($url, $pos + 1)).".swf";			
		}

		if ($allowCache)
			$cache->put($requestKey, $url);

		if (strpos($url, "/swfparams/") > 0)
			$url = substr($url, 0, -4).urlencode($noncached_params).".swf";
		else
			$url .= $noncached_params;

		$this->redirect( $url );
	}
}

