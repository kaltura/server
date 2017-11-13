<?php

class DeliveryProfileLiveAppleHttp extends DeliveryProfileLive {
	
	const HLS_LIVE_STREAM_CONTENT_TYPE = "hls_live_stream_content_type";
	const M3U8_MASTER_PLAYLIST_IDENTIFIER = "EXT-X-STREAM-INF";
	const M3U8_PLAYLIST_END_LIST_IDENTIFIER = "#EXT-X-ENDLIST";
	const MAX_IS_LIVE_ATTEMPTS = 3;

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
	protected function buildM3u8Flavors($url, array &$flavors, array $kLiveStreamParamsArray, $flavorBitrateInfo = array())
	{
		$domainPrefix = $this->getDeliveryServerNodeUrl(true);
		
		foreach ($kLiveStreamParamsArray as $kLiveStreamParams)
		{
			if(!$this->isFlavorAllowed($kLiveStreamParams->getFlavorId()))
				continue;
			
			/* @var $kLiveStreamParams kLiveStreamParams */
			/* @var $stream kLiveStreamParams */
			$flavor = array(
					'url' => '',
					'urlPrefix' => $this->getUrlPrefix($url, $kLiveStreamParams),
					'domainPrefix' => $domainPrefix,
					'ext' => 'm3u8',
			);
		
			$flavor['bitrate'] = isset($flavorBitrateInfo[$kLiveStreamParams->getFlavorId()]) ? $flavorBitrateInfo[$kLiveStreamParams->getFlavorId()] : $kLiveStreamParams->getBitrate();
			$flavor['bitrate'] = $flavor['bitrate'] / 1024;
			$flavor['width'] = $kLiveStreamParams->getWidth();
			$flavor['height'] = $kLiveStreamParams->getHeight();
			
			$this->addLanguageInfo($flavor, $kLiveStreamParams);
				
			$flavors[] = $flavor;
		}
	}
	
	protected function getUrlPrefix($url, $kLiveStreamParams)
	{
		return requestUtils::resolve($kLiveStreamParams->getFlavorId() . "/chunklist.m3u8" , $url);
	}
	
	protected function addLanguageInfo(&$flavor, $kLiveStreamParams)
	{
		$audioLanguageData = $this->getLanguageInfo($kLiveStreamParams);
		if($audioLanguageData)
		{
			list($audioLanguage, $audioLanguageName) = $audioLanguageData;
			$flavor['audioLanguage'] = $audioLanguage;
			$flavor['audioLanguageName'] =  $audioLanguageName;
			$flavor['defaultAudio'] = $this->isDefaultAudio($audioLanguage, $audioLanguageName);
		}
	}
	
	protected function getLanguageInfo($kLiveStreamParams)
	{
		$language = $kLiveStreamParams->getLanguage();
		if(!$language)
			return null;
		
		$languageObj = languageCodeManager::getObjectFromThreeCode(strtolower($language));
		$audioLanguageName = $this->getAudioLanguageName($languageObj, $language);
			
		return array($language, $audioLanguageName);
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

		if(count($this->getDynamicAttributes()->getPlayerConfig()))
			$url .= '/playerConfig/' . $this->params->getPlayerConfig();
			
		// TODO encrypt the manifest URL
		return "$url/a.m3u8?url=$manifestUrl";
	}
	
	public function compareFlavors($a, $b) 
	{
		$isAudio1 = isset($a['audioLanguage']) && isset($a['audioLanguageName']);
		$isAudio2 = isset($b['audioLanguage']) && isset($b['audioLanguageName']);
		
		if ($isAudio1 != $isAudio2)
		{
			return $isAudio1 ? -1 : 1;
		}
		
		//Move all Dolby audio flavors to the beginning of the audio flavors list
		if($isAudio1 == true)
		{
			if(isset($flavor1['defaultAudio']) && $flavor1['defaultAudio'] == true)
				return -1;
			
			if(isset($flavor2['defaultAudio']) && $flavor2['defaultAudio'] == true)
				return 1;
		}
		
	    if ($a['bitrate'] == $b['bitrate']) 
	    {
	        return ($a['index'] < $b['index']) ? -1 : 1;
	    }
	    
	    return ($a['bitrate'] < $b['bitrate']) ? -1 : 1;
	}

	protected function getHttpUrl($serverNode)
	{
		$httpUrl = $this->getBaseUrl($serverNode, PlaybackProtocol::HLS);
		$httpUrl = rtrim($httpUrl, "/") . "/" . $this->getStreamName() . "/playlist.m3u8" . $this->getQueryAttributes();

		return $httpUrl;
	}

	protected function buildHttpFlavorsArray()
	{
		$flavors = array();
		
		$primaryManifestUrl = $this->liveStreamConfig->getUrl();
		$backupManifestUrl = $this->liveStreamConfig->getBackupUrl();
		$primaryStreamInfo = $this->liveStreamConfig->getPrimaryStreamInfo();
		$backupStreamInfo = $this->liveStreamConfig->getBackupStreamInfo();
		
		if($this->getDynamicAttributes()->getUsePlayServer())
		{
			$playServerManifestUrl = $this->getPlayServerUrl($primaryManifestUrl);
			$this->liveStreamConfig->setUrl($playServerManifestUrl);
		}
		
		if($this->getDynamicAttributes()->getUsePlayServer() || (!count($primaryStreamInfo) && !count($backupStreamInfo)))
			$this->shouldRedirect = true;
		
		if($this->shouldRedirect)
		{
			return parent::buildHttpFlavorsArray();
		}
		
		$this->buildM3u8Flavors($primaryManifestUrl, $flavors, $primaryStreamInfo);
		if($backupManifestUrl && ($this->getForceProxy() || count($flavors) == 0))
		{
			//Until a full solution will be made on the liveServer side we need to manually sync bitrates Between primary and backup streams
			$primaryFlavorBitrateInfo = $this->buildFlavorBitrateInfoArray($primaryStreamInfo);
			$this->buildM3u8Flavors($backupManifestUrl, $flavors, $backupStreamInfo, $primaryFlavorBitrateInfo);
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
	
	protected function isFlavorAllowed($flavorParamId)
	{
		$aclFlavorParamsIds = $this->getDynamicAttributes()->getAclFlavorParamsIds();
		if(!$aclFlavorParamsIds)
			return true;
		
		$isBlockedList = $this->getDynamicAttributes()->getIsAclFlavorParamsIdsBlockedList();
		$exists = in_array($flavorParamId, $aclFlavorParamsIds);
		
		return $isBlockedList ? !$exists : $exists;
	}
}

