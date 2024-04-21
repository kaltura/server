<?php


class LiveClusterMediaServerNode extends MediaServerNode
{
    const ENVIRONMENT = 'env';
    const SESSION_TYPE = 'st';
    const TIMELINE_URL_PARAM = 'tl';
    const LOW_LATENCY_URL_PARAM = 'll';
    const CONTAINER_URL_PARAM = 'container';
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
        return self::ENVIRONMENT . '/' . $this->getName();
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

	protected function getThumbTimeline($entry)
	{
		if ($entry->getType() != entryType::LIVE_STREAM)
		{
			return $entry->getId();
		}

		return $this->getUserType(!$entry->getExplicitLive() || $entry->canViewExplicitLive());
	}

	public function createThumbUrl($baseUrl, $entry, $entryServerNode)
	{
		$serverNodeUrl = str_replace('{dc}', $this->getEnvDc(), $baseUrl);
		$serverNodeUrl .= self::ENTRY_ID_URL_PARAM . "/{$entry->getRootEntryId()}/";
		$serverNodeUrl .= self::TIMELINE_URL_PARAM . "/{$this->getThumbTimeline($entry)}/";
		$serverNodeUrl .= self::SESSION_TYPE . "/{$entryServerNode->getServerType()}/";

		$token = myPackagerUtils::generateLivePackagerToken($serverNodeUrl);
		$serverNodeUrl .= "t/$token/";

		return $serverNodeUrl;
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

	/**
	 * @return string
	 */
	public function getAdditionalUrlParam(LiveStreamEntry $entry, LiveEntryServerNode $liveEntryServerNode)
	{
		$res = '';
		if ($entry->isLowLatencyEntry())
		{
			$res .= self::LOW_LATENCY_URL_PARAM . '/1/';
		}

		$streams = $liveEntryServerNode->getStreams();
		$this->sanitizeAndFilterStreamIdsByBitrate($streams);

		foreach($streams as $stream)
		{
			if ($stream->getCodec() == flavorParams::VIDEO_CODEC_H265) {
				KalturaLog::debug("Stream has h265 video codec - force fmp4 container");
				$res .= self::CONTAINER_URL_PARAM . '/fmp4/';
				break;
			}
		}

		return $res;
	}
}
