<?php

class DeliveryProfileLiveAppleHttp extends DeliveryProfileLive {
	
	const HLS_LIVE_STREAM_CONTENT_TYPE = "hls_live_stream_content_type";
	const M3U8_MASTER_PLAYLIST_IDENTIFIER = "EXT-X-STREAM-INF";
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
	 * Extract all non-empty / non-comment lines from a .m3u/.m3u8 content
	 * @param $content array|string Full file content as a single string or as a lines-array
	 * @return array Valid lines
	 */
	protected function getM3U8Urls( $content )
	{
		$outLines = array();

		if ( strpos($content, "#EXTM3U") !== 0 )
		{
			return $outLines;
		}

		if ( !is_array($content) )
		{
			$lines = explode("\n", trim($content));
		}

		foreach ( $lines as $line )
		{
			$line = trim($line);
			if (!$line || $line[0] == '#')
			{
				continue;
			}

			$outLines[] = $line;
		}

		return $outLines;
	}
	
	/**
	 * Check if the given URL contains live entries (typically live .m3u8 URLs)
	 * @param string $url
	 * @param string|array $urlContent The URL's parsed content
	 * @return boolean
	 */
	protected function checkIsLiveMasterPlaylist( $url, $urlContent )
	{
		$lines = $this->getM3U8Urls( $urlContent );

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
		$lines = $this->getM3U8Urls( $urlContent );

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
	
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		if($this->getDisableExtraAttributes()) {
			$baseUrl = kDeliveryUtils::addQueryParameter($baseUrl, "attributes=off");
		}
	}
	
	/**
	 * Fetch the manifest and build all flavors array
	 * @param string $url
	 */
	private function buildM3u8Flavors($url, array &$flavors)
	{
		$this->finalizeUrls($url, $flavors);
		
		$manifest = KCurlWrapper::getContent($url);
		if(!$manifest)
			return;
	
		$manifestLines = explode("\n", $manifest);
		$manifestLine = reset($manifestLines);
		while($manifestLine)
		{
			$lineParts = explode(':', $manifestLine, 2);
			if($lineParts[0] === '#EXT-X-STREAM-INF')
			{
				// passing the url as urlPrefix so that only the path will be tokenized
				$flavor = array(
					'url' => '',
					'urlPrefix' => requestUtils::resolve(next($manifestLines), $url),
					'ext' => 'm3u8',
				);
				
				$attributes = explode(',', $lineParts[1]);
				foreach($attributes as $attribute)
				{
					$attributeParts = explode('=', $attribute, 2);
					switch($attributeParts[0])
					{
						case 'BANDWIDTH':
							$flavor['bitrate'] = $attributeParts[1] / 1024;
							break;
							
						case 'RESOLUTION':
							list($flavor['width'], $flavor['height']) = explode('x', $attributeParts[1], 2);
							break;
					}
				}
				$flavors[] = $flavor;
			}
			
			$manifestLine = next($manifestLines);
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
		return "$url?url=$manifestUrl";
	}
	
	public function compareFlavors($a, $b) 
	{
	    if ($a['bitrate'] == $b['bitrate']) {
	        return 0;
	    }
	    return ($a['bitrate'] < $b['bitrate']) ? -1 : 1;
	}

	/* (non-PHPdoc)
	 * @see DeliveryProfileLive::serve()
	 */
	public final function serve($baseUrl, $backupUrl) 
	{
		if($this->params->getUsePlayServer())
		{
			$baseUrl = $this->getPlayServerUrl($baseUrl);
			$backupUrl = null;
		}
		
		return $this->doServe($baseUrl, $backupUrl);
	}

	protected function doServe($baseUrl, $backupUrl) 
	{
		if((!$backupUrl && !$this->getForceProxy()) || $this->params->getUsePlayServer())
		{
			return parent::serve($baseUrl, $backupUrl);
		}
		
		$entry = entryPeer::retrieveByPK($this->params->getEntryId());
		/* @var $entry LiveEntry */
		if($entry && $entry->getSyncDCs())
		{
			$baseUrl = str_replace('_all.smil', '_publish.smil', $baseUrl);
			if($backupUrl)
				$backupUrl = str_replace('_all.smil', '_publish.smil', $backupUrl);
		}
		
		$flavors = array();
		$this->buildM3u8Flavors($baseUrl, $flavors);
		if($backupUrl)
			$this->buildM3u8Flavors($backupUrl, $flavors);
		
		usort($flavors, array($this, 'compareFlavors'));
		
		$this->DEFAULT_RENDERER_CLASS = 'kM3U8ManifestRenderer';
		$renderer = $this->getRenderer($flavors);
		return $renderer;
	}
}

