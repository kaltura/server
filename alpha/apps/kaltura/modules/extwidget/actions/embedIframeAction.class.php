<?php
class embedIframeAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		$entry_id = $this->getRequestParameter("entry_id");
		
		$entry = null;
		$widget_id = null;
		$partner_id = null;
		if($entry_id)
		{
			$entry = entryPeer::retrieveByPK($entry_id);
			if(!$entry)
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
				
			$partner_id = $entry->getPartnerId();
			$widget_id = '_' . $partner_id;
		}
			
			
		$widget_id = $this->getRequestParameter("widget_id", $widget_id);
		$widget = widgetPeer::retrieveByPK($widget_id);
		if(!$widget)
			KExternalErrors::dieError(KExternalErrors::WIDGET_NOT_FOUND);

		$subp_id = $widget->getSubpId();
		if (!$subp_id)
			$subp_id = 0;
		
		if(!$entry_id)
		{
			$entry_id = $widget->getEntryId();

			if(!$entry_id)
				KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'entry_id');
				
			$entry = entryPeer::retrieveByPK($entry_id);
			if(!$entry)
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		}
			
		$allowCache = true;
		$securityType = $widget->getSecurityType();
		switch($securityType) 
		{
			case widget::WIDGET_SECURITY_TYPE_TIMEHASH:
				// TODO - I don't know what should be validated here
				break;
			
			case widget::WIDGET_SECURITY_TYPE_MATCH_IP:
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
						KalturaLog::log("Attempting to access widget [$widget_id] and entry [$entry_id] from country [$current_country]. Retrning entry_id: [$fallback_entry_id] kshow_id [$fallback_kshow_id]" );
						$entry_id = $fallback_entry_id;
					}
				}
				break;
			
			case widget::WIDGET_SECURITY_TYPE_FORCE_KS:
				$ks_str = $this->getRequestParameter('ks');
				try
				{
					$ks = kSessionUtils::crackKs($ks_str);
				}
				catch (Exception $e)
				{
					KExternalErrors::dieError(KExternalErrors::INVALID_KS);
				}
				$res = kSessionUtils::validateKSession2(1, $partner_id, 0, $ks_str, $ks);
				if ($res <= 0)
					KExternalErrors::dieError(KExternalErrors::INVALID_KS);
					
				break;
			
			default:
				break;
		}
	
		$requestKey = $_SERVER["REQUEST_URI"];
		
		// check if we cached the redirect url
		$cache = new myCache("embedIframe", 10 * 60); // 10 minutes
		$cachedResponse  = $cache->get($requestKey);
		if ($allowCache && $cachedResponse) // dont use cache if we want to force no caching
		{
			header("X-Kaltura: cached-action");
			header("Expires: Thu, 19 Nov 2000 08:52:00 GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
			header("Location:$cachedResponse");
				
			die;
		}

		$uiconf_id = $this->getRequestParameter('uiconf_id');
		if(!$uiconf_id)
			$uiconf_id = $widget->getUiConfId();
		if(!$uiconf_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'uiconf_id');
			
		$partner_host = myPartnerUtils::getHost($partner_id);
		$partner_cdnHost = myPartnerUtils::getCdnHost($partner_id);
		$host = $partner_host;

		$uiConf = uiConfPeer::retrieveByPK($uiconf_id);
		if(!$uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);
			
		$url = null;
		$ui_conf_swf_url = $uiConf->getSwfUrl();
		if(kString::beginsWith($ui_conf_swf_url, "http"))
		{
			$url = 	$ui_conf_swf_url; // absolute URL
		}
		else
		{
			$use_cdn = $uiConf->getUseCdn();
			$host = $use_cdn ?  $partner_cdnHost : $partner_host;
			$url =  $host;
			$url .=  myPartnerUtils::getUrlForPartner($partner_id, $subp_id);
			$url .=  $ui_conf_swf_url;
			$url .= "/entry_id/$entry_id/widget_id/$widget_id/uiconf_id/$uiconf_id";
		}
		
		if ($allowCache)
			$cache->put($requestKey, $url);

		$this->redirect($url);
	}
}

