<?php
class embedIframeJsAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		$uiconf_id = $this->getRequestParameter('uiconf_id');
		if(!$uiconf_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'uiconf_id');
			
		$uiConf = uiConfPeer::retrieveByPK($uiconf_id);
		if(!$uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);
			
		$partner_id = $this->getRequestParameter('partner_id', $uiConf->getPartnerId());
		if(!$partner_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'partner_id');
		
		$partner_host = myPartnerUtils::getHost($partner_id);
		$partner_cdnHost = myPartnerUtils::getCdnHost($partner_id);
		
		$use_cdn = $uiConf->getUseCdn();
		$host = $use_cdn ?  $partner_cdnHost : $partner_host;

		$ui_conf_html5_url = $uiConf->getHtml5Url();

		if (kConf::hasParam("optimized_playback"))
		{
			$optimizedPlayback = kConf::get("optimized_playback");
			if (array_key_exists($partner_id, $optimizedPlayback))
			{
				// force a specific kdp for the partner
				$params = null;
				parse_str($optimizedPlayback[$partner_id], $params);
				if (array_key_exists('html5_url', $params))
				{
					$ui_conf_html5_url = $params['html5_url'];
				}
			}
		}

		if( kString::beginsWith( $ui_conf_html5_url , "http") )
		{
			$url = $ui_conf_html5_url; // absolute URL
		}
		else if ($ui_conf_html5_url)
		{
			$url =  $host . $ui_conf_html5_url;
		}
		else
		{
			$html5_version = kConf::get('html5_version');
			$url =  "$host/html5/html5lib/{$html5_version}/mwEmbedLoader.php";
		}

		requestUtils::sendCachingHeaders(60);
		header("Pragma:");
		header("Location:$url");
		die;
	}
}

