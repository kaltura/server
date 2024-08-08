<?php

require_once dirname(__FILE__)."/../../../lib/v2ToV7Utils.class.php";

/**
 * @package Core
 * @subpackage externalWidgets
 */
class embedIframeJsAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id
	 */
	public function execute()
	{
		// prevent indexing of direct player urls
		header('X-Robots-Tag: noindex');

		$partner_id = $this->getRequestParameter('partner_id');
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";

		$optimizedPlayback = kConf::getMap("optimized_playback");
		if ($partner_id && array_key_exists($partner_id, $optimizedPlayback))
		{
			$params = $optimizedPlayback[$partner_id];
			if (array_key_exists('redirect_host', $params))
			{
				$redirectUrl = "$protocol://" . $params['redirect_host'] . $_SERVER["REQUEST_URI"];
				kFile::cacheRedirect($redirectUrl);
				header("Location:$redirectUrl");
				KExternalErrors::dieGracefully();
			}
		}

		$uiconf_id = $this->getRequestParameter('uiconf_id');
		if(!$uiconf_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'uiconf_id');

		$uiConf = uiConfPeer::retrieveByPK($uiconf_id);
		if(!$uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);

		$partner_id = $this->getRequestParameter('partner_id', $uiConf->getPartnerId());
		if(!$partner_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'partner_id');

		$widget_id = $this->getRequestParameter("widget_id", '_' . $partner_id);

		$host = myPartnerUtils::getCdnHost($partner_id, $protocol, 'api');

		$ui_conf_html5_url = $uiConf->getHtml5Url();

		if (array_key_exists($partner_id, $optimizedPlayback))
		{
			// force a specific kdp for the partner
			$params = $optimizedPlayback[$partner_id];
			if (array_key_exists('html5_url', $params))
			{
				$ui_conf_html5_url = $params['html5_url'];
			}
		}

		$autoEmbed = $this->getRequestParameter('autoembed');

		$iframeEmbed = $this->getRequestParameter('iframeembed');

		//redirect the call to V7
		if($uiConf->getV2tov7id() && ($this->getRequestParameter(v2Tov7Utils::V2TOV7_PARAM_NAME) || $uiConf->getV2tov7Approved()) )
		{
			$this->redirectToV7($uiConf->getV2tov7id(), $uiconf_id, $partner_id, $uiConf->getShouldTranslatePluginsToV7() );
		}


		$scriptName = ($iframeEmbed) ? 'mwEmbedFrame.php' : 'mwEmbedLoader.php';
		if($ui_conf_html5_url && $iframeEmbed) {
			$ui_conf_html5_url = str_replace('mwEmbedLoader.php', 'mwEmbedFrame.php', $ui_conf_html5_url);
		}

		$relativeUrl = true; // true if ui_conf html5_url is relative (doesnt start with an http prefix)

		if( kString::beginsWith( $ui_conf_html5_url , "http") )
		{
			$relativeUrl = false;
			$url = $ui_conf_html5_url; // absolute URL
		}
		else
		{
			if (!$iframeEmbed)
				$host = "$protocol://". kConf::get('html5lib_host') ."/";
			$html5_version = kConf::getArrayValue('html5_version', 'playerApps', kConfMapNames::APP_VERSIONS, null);
			if(!$html5_version)
			    KExternalErrors::dieError('The html player version was not found');

			if ($ui_conf_html5_url)
			{
				$url =  $host . $ui_conf_html5_url;
				$url = str_replace("{latest}", $html5_version, $url);
			}
			else
			{
				$url =  "$host/html5/html5lib/{$html5_version}/" . $scriptName;
			}
		}

		// append uiconf_id and partner id for optimizing loading of html5 library. append them only for "standard" urls by looking for the mwEmbedLoader.php/mwEmbedFrame.php suffix
		if (kString::endsWith($url, $scriptName))
		{
			$url .= "/p/$partner_id/uiconf_id/$uiconf_id";

			if (!$autoEmbed) // auto embed will include any query string parameter anyway
			{
				$entry_id = $this->getRequestParameter('entry_id');
				if ($entry_id)
					$url .= "/entry_id/$entry_id";
			}
		}

		header("pragma:");
		if($iframeEmbed) {
			$url .= ((strpos($url, "?") === false) ? "?" : "&") . 'wid=' . $widget_id . '&' . $_SERVER["QUERY_STRING"];
		}
		else
		{
			$params = "protocol=$protocol&".$_SERVER["QUERY_STRING"];

			$url .= ((strpos($url, "?") === false) ? "?" : "&") . $params;

			if ($relativeUrl)
			{
				header('Content-Type: application/javascript');
				$headers = array("X-Forwarded-For" =>  requestUtils::getRemoteAddress());
				if(isset($_SERVER["HTTP_REFERER"]))
					$headers["referer"] = $_SERVER["HTTP_REFERER"];

				$partner = PartnerPeer::retrieveByPK( $partner_id );
				if ( $partner )
				{
					$whiteListHost = myPartnerUtils::getWhiteListHost($partner);
					if ($whiteListHost) {
						$headers["X-Forwarded-Host"] = $whiteListHost;
					} else {
						unset($_SERVER['HTTP_X_FORWARDED_HOST']);
					}
				}

				kFileUtils::dumpUrl($url, true, false, $headers);
			}
		}

		requestUtils::sendCachingHeaders(60, true, time());

		kFile::cacheRedirect($url);
		header("Location:$url");
		KExternalErrors::dieGracefully();
	}

	/*
	 * v2 - https://cdnapisec.kaltura.com/p/1915851/sp/191585100/embedIframeJs/uiconf_id/32880931/partner_id/1915851?iframeembed=true&playerId=kaltura_player_1719900446&entry_id=1_aeg07vpv
	 * v7 - https://cdnapisec.kaltura.com/p/1915851/embedPlaykitJs/uiconf_id/54813242?iframeembed=true&entry_id=1_aeg07vpv
	 * Test - http://www.kaltura.local/p/102/sp/10200/embedIframeJs/uiconf_id/23448128/partner_id/102?iframeembed=true&playerId=kaltura_player_1719900446&entry_id=0_3eil6eqs
	 */
	private function redirectToV7($v7Id, $v2UiConfId, $partnerId, $shouldTranslatePlugins) : void
	{
        //validate all the params are handled
        try
        {
            $config = array();
            $config["bundleConfig"] = null;
            $config["playerConfig"] = new stdClass();
            $config = v2Tov7Utils::addV2toV7config($config,$this->getRequestParameter(v2Tov7Utils::FLASHVARS_PARAM_NAME),$v7Id);
            if($shouldTranslatePlugins)
            {
                v2Tov7Utils::addV2toV7plugins(
                    $this->getRequestParameter(v2Tov7Utils::FLASHVARS_PARAM_NAME),
                    $config["bundleConfig"],
                    $config["playerConfig"]);
            }
        }
        catch(Exception $e)
        {
            //if we failed, then should not redirect!
            KalturaLog::log("V2toV7 was rejected because: " . $e->getMessage() . " v2 id:" .  $v2UiConfId.  " v7 dd:" . $v2UiConfId);
            return;
        }

		$host = myPartnerUtils::getCdnHost($partnerId, null , 'api');
		$url = $host . "/p/" . $partnerId  . '/embedPlaykitJs/uiconf_id/' . $v7Id . "?" . $_SERVER['QUERY_STRING'] . "&"
                . v2Tov7Utils::FLASHVARS_PARAM_NAME . "=true&v2tov7translatePlugins="
                . v2Tov7Utils::SHOULD_TRANSLATE_PLUGINS . "=true"  ;;
		header("Location:$url");
		KExternalErrors::dieGracefully();
	}
}
