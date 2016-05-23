<?php

class DeliveryProfileLiveAppleHttp extends DeliveryProfileLive {
	
	const HLS_LIVE_STREAM_CONTENT_TYPE = "hls_live_stream_content_type";
	const M3U8_MASTER_PLAYLIST_IDENTIFIER = "EXT-X-STREAM-INF";
	const M3U8_PLAYLIST_END_LIST_IDENTIFIER = "#EXT-X-ENDLIST";
	const MAX_IS_LIVE_ATTEMPTS = 3;
	
	/**
	 * @var bool
	 */
	private $shouldRedirect = false;

	public function setDisableExtraAttributes($v)
	{
		$this->putInCustomData("disableExtraAttributes", $v);
	}
	
	public function getDisableExtraAttributes()
	{
		return $this->getFromCustomData("disableExtraAttributes");
	}
	
	public function setForceProxy($v)
	{
		$this->putInCustomData("forceProxy", $v);
	}
	
	public function getForceProxy()
	{
		return $this->getFromCustomData("forceProxy", null, false);
	}
	
	public function checkIsLive( $url )
	{
		$urlContent = $this->urlExists($url, kConf::get(self::HLS_LIVE_STREAM_CONTENT_TYPE));
		if( ! $urlContent )
		{
			return false;
		}

		if ( strpos( $urlContent, self::M3U8_MASTER_PLAYLIST_IDENTIFIER ) !== false )
		{
			$isLive = $this->checkIsLiveMasterPlaylist( $url, $urlContent );
		}
		else
		{
			$isLive = $this->checkIsLiveMediaPlaylist( $url, $urlContent );
		}

		return $isLive;
	}
		
	/**
	 * Check if the given URL contains live entries (typically live .m3u8 URLs)
	 * @param string $url
	 * @param string|array $urlContent The URL's parsed content
	 * @return boolean
	 */
	protected function checkIsLiveMasterPlaylist( $url, $urlContent )
	{
		$lines = kDeliveryUtils::getM3U8Urls( $urlContent );

		foreach ($lines as $urlLine)
		{
			$mediaUrl = $this->checkIfValidUrl($urlLine, $url);
	
			$urlContent = $this->urlExists($mediaUrl, kConf::get(self::HLS_LIVE_STREAM_CONTENT_TYPE));

			if (!$urlContent)
			{
				continue;
			}

			$isLive = $this->checkIsLiveMediaPlaylist($mediaUrl, $urlContent);
			if ( $isLive )
			{
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Check if the given URL contains live entries (typically containing .ts URLs)
	 * @param string $url
	 * @param string|array $urlContent The URL's parsed content
	 * @return boolean
	 */
	protected function checkIsLiveMediaPlaylist( $url, $urlContent )
	{
		if($this->isDvrContent($urlContent))
			return false;
		
		$lines = kDeliveryUtils::getM3U8Urls( $urlContent );

		$lines = array_slice($lines, -self::MAX_IS_LIVE_ATTEMPTS, self::MAX_IS_LIVE_ATTEMPTS, true);
		foreach ($lines as $urlLine)
		{
			$tsUrl = $this->checkIfValidUrl($urlLine, $url);
			if ($this->urlExists($tsUrl ,kConf::get(self::HLS_LIVE_STREAM_CONTENT_TYPE),'0-1') !== false)
			{
				KalturaLog::log("Live ts url: $tsUrl");
				return true;
			}
		}
	
		return false;
	}
	
	/**
	 * Check the the manifest content is actually a DVR.
	 * We identify the DVR content by having the END-LIST identifier within the playlist.
	 * @param String $content
	 */
	protected function isDvrContent($content) {
		return in_array(self::M3U8_PLAYLIST_END_LIST_IDENTIFIER, array_map('trim', explode("\n", $content)));
	}
	
	/**
	 * Build all streaming flavors array
	 * @param string $url
	 */
	private function buildM3u8Flavors($url, array &$flavors, array $kLiveStreamParamsArray, $flavorBitrateInfo = array())
	{
		$domainPrefix = $this->getDeliveryServerNodeUrl(true);
		
		foreach ($kLiveStreamParamsArray as $kLiveStreamParams)
		{			
			/* @var $kLiveStreamParams kLiveStreamParams */
			/* @var $stream kLiveStreamParams */
			$flavor = array(
					'url' => '',
					'urlPrefix' => requestUtils::resolve($kLiveStreamParams->getFlavorId() . "/chunklist.m3u8" , $url),
					'domainPrefix' => $domainPrefix,
					'ext' => 'm3u8',
			);
			
			$flavor['bitrate'] = isset($flavorBitrateInfo[$kLiveStreamParams->getFlavorId()]) ? $flavorBitrateInfo[$kLiveStreamParams->getFlavorId()] : $kLiveStreamParams->getBitrate();
			$flavor['width'] = $kLiveStreamParams->getWidth();
			$flavor['height'] = $kLiveStreamParams->getHeight();
			
			$flavors[] = $flavor;
		}
	}

	protected function getPlayServerUrl($manifestUrl)
	{
		$entryId = $this->params->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			KalturaLog::err("Entry [$entryId] not found");
			return $manifestUrl;
		}
		
		$partnerId = $entry->getPartnerId();
		$uiConfId = $this->params->getUiConfId();
		$playServerHost = myPartnerUtils::getPlayServerHost($partnerId, $this->params->getMediaProtocol());		
		
		$url = "$playServerHost/p/$partnerId/manifest/master/entryId/$entryId";
		if($uiConfId)
			$url .= '/uiConfId/' . $uiConfId;

		if(count($this->params->getPlayerConfig()))
			$url .= '/playerConfig/' . $this->params->getPlayerConfig();
			
		// TODO encrypt the manifest URL
		return "$url/a.m3u8?url=$manifestUrl";
	}
	
	public function compareFlavors($a, $b) 
	{
	    if ($a['bitrate'] == $b['bitrate']) {
	        return ($a['index'] < $b['index']) ? -1 : 1;
	    }
	    return ($a['bitrate'] < $b['bitrate']) ? -1 : 1;
	}

	public function buildServeFlavors() 
	{	
		$baseUrl = $this->liveStreamConfig->getUrl();
		$backupUrl = $this->liveStreamConfig->getBackupUrl();
		
		$primaryServerStreams = $this->liveStreamConfig->getPrimaryLiveEntryServerNode() ? $this->liveStreamConfig->getPrimaryLiveEntryServerNode()->getStreams() : array();
		$backupServerStreams = $this->liveStreamConfig->getBackupLiveEntryServerNode() ? $this->liveStreamConfig->getBackupLiveEntryServerNode()->getStreams() : array();
		
		if(!$this->getForceProxy() || $this->params->getUsePlayServer() || (!count($primaryServerStreams) && !count($backupServerStreams)))
		{
			$this->shouldRedirect = true;
		}
		
		$entry = entryPeer::retrieveByPK($this->params->getEntryId());
		/* @var $entry LiveEntry */
		if($entry && $entry->getSyncDCs())
		{
			$baseUrl = str_replace('_all.smil', '_publish.smil', $baseUrl);
			if($backupUrl)
				$backupUrl = str_replace('_all.smil', '_publish.smil', $backupUrl);
		}
		
		if($this->params->getUsePlayServer()) {
			
			$this->liveStreamConfig->setUrl($this->getPlayServerUrl($baseUrl));
			$this->liveStreamConfig->setBackupUrl(null);
		}
				
		if($this->shouldRedirect) {
			return parent::buildServeFlavors();
		}
				
		$flavors = array();
		$this->buildM3u8Flavors($baseUrl, $flavors, $primaryServerStreams);
		if($backupUrl)
		{
			//Until a full solution will be made on the mediaServer side we need to manually sync bitrates Between primay and backup streams
			$priamryFlavorBitrateInfo = $this->buildFlavorBitrateInfoArray($primaryServerStreams);
			$this->buildM3u8Flavors($backupUrl, $flavors, $backupServerStreams, $priamryFlavorBitrateInfo);
		}
		
		foreach ($flavors as $index => $flavor)
		{
			$flavors[$index]['index'] = $index;
		}
			
		usort($flavors, array($this, 'compareFlavors'));
		
		foreach ($flavors as $index => $flavor)
		{
			unset($flavors[$index]['index']);
		}
		
		return $flavors;
	}
	
	public function getRenderer($flavors)
	{
		$this->DEFAULT_RENDERER_CLASS = 'kM3U8ManifestRenderer';
		if($this->shouldRedirect) {
			$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
		}
		$renderer = parent::getRenderer($flavors);
		return $renderer;
	}
	
	private function buildFlavorBitrateInfoArray($primaryServerStreams)
	{
		$flavorBitrateInfo = array();
		foreach ($primaryServerStreams as $stream)
		{
			$flavorBitrateInfo[$stream->getFlavorId()] = $stream->getBitrate();
		}
		return $flavorBitrateInfo;
	}
}

