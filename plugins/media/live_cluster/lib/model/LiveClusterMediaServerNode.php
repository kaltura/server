<?php


class LiveClusterMediaServerNode extends MediaServerNode
{
    const ENVIRONMENT = 'env';
    const SESSION_TYPE = 'st';
    const TIMELINE_URL_PARAM = 'tl';
    const EXPLICIT_LIVE_VIEWER_TYPE_URL = 'tl';
    const USER_TYPE_ADMIN = 'main';
    const USER_TYPE_USER = 'viewer';

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        parent::applyDefaultValues();

        $this->setType(LiveClusterPlugin::getLiveClusterMediaServerTypeCoreValue(LiveClusterMediaServerNodeType::LIVE_CLUSTER_MEDIA_SERVER));
    }

    /**
     * @param string $serviceName
     * @return KalturaMediaServerClient
     */
    public function getWebService($serviceName)
    {
        return null;
    }

    public function getLiveWebServiceName()
    {
        return 'NOT_IMPLEMENTED';
    }

    public function getPlaybackHost($protocol = 'http', $format = null, $baseUrl = null, $deliveryType = null)
    {
        $domain = rtrim(kString::removeHttp($baseUrl), '/'); // extract only the domain from the base url
        $domain = str_replace('{hostName}', $this->getHostname(), $domain); // if the domain contain place-holder replace it with the server-node host name

        return "$protocol://$domain/";
    }

    public function getEnvDc()
    {
        return self::ENVIRONMENT . '/' . $this->getEnvironment();
    }

    public static function getSessionType($entryServerNode)
    {
        return self::SESSION_TYPE . '/' . $entryServerNode->getServerType() . '/';
    }

	public function getEntryIdUrl(DeliveryProfileDynamicAttributes $da)
	{
		if ($da->getServeVodFromLive())
		{
			$recordingEntryId = $da->getServeLiveAsVodEntryId();
			$entryId =  $da->getEntryId();
			return '/' . self::ENTRY_ID_URL_PARAM . "/$entryId/" . self::TIMELINE_URL_PARAM . "/$recordingEntryId/";
//			returns "/e/$entryId/tl/$recordingEntryId/"
		}

		return parent::getEntryIdUrl($da);
	}

//	TODO: replace getEntryIdUrl with this in getLivePackagerUrl
	protected function getEntryIdUrlFromEntry($entry)
	{
		$sourceId = $entry->getRootEntryId();
		return self::ENTRY_ID_URL_PARAM . "/$sourceId/";
	}

	protected function getTimelineUrl($entry)
	{
		$timelineId = self::USER_TYPE_ADMIN;

		if ($entry->getExplicitLive() && !$entry->canViewExplicitLive())
		{
			$timelineId = $this->getUserType(false);
		}
		elseif (myEntryUtils::shouldServeVodFromLive($entry))
		{
			$timelineId = $entry->getRootEntryId();
		}

		return self::TIMELINE_URL_PARAM . "/$timelineId/";
	}

	public function tokenizeUrl($url, $signingDomain = '')
	{
		$livePackagerToken = kConf::get("live_packager_secure_token");

		if(!empty($signingDomain))
		{
			$domain = parse_url($url, PHP_URL_HOST);
			if($domain && $domain != '')
			{
				$url = str_replace($domain, $signingDomain, $url);
			}
			else
			{
				KalturaLog::debug("Failed to parse domain from original url, signed domain will not be modified");
			}
		}

		$strippedUrl = preg_replace('#^https?://#', '', $url);

		$token = md5("$livePackagerToken $url", true);
		$token = rtrim(strtr(base64_encode($token), '+/', '-_'), '=');
		return $url . "t/$token/";
	}

	public function createThumbUrl($baseUrl, $entry)
	{
		$serverNodeUrl = str_replace('{dc}', $this->getEnvDc(), $baseUrl);
		$serverNodeUrl .= $this->getEntryIdUrlFromEntry($entry);
		$serverNodeUrl .= $this->getTimelineUrl($entry);

		return $this->tokenizeUrl($serverNodeUrl);
	}

	protected function getUserType($isAdmin)
	{
		return $isAdmin ? self::USER_TYPE_ADMIN : self::USER_TYPE_USER;
	}

	protected function getUrlType()
	{
		return self::EXPLICIT_LIVE_VIEWER_TYPE_URL;
	}


	public function getExplicitLiveUrl($liveUrl, LiveStreamEntry $entry)
	{
		$tlUrlParam = '/' . self::TIMELINE_URL_PARAM . '/';
		if (strpos($liveUrl, $tlUrlParam) !== false)
		{
			return '';
		}
		return parent::getExplicitLiveUrl($liveUrl, $entry);
	}
}