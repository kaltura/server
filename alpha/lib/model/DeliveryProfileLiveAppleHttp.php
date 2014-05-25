<?php

class DeliveryProfileLiveAppleHttp extends DeliveryProfileLive {
	
	public function setDisableExtraAttributes($v)
	{
		$this->putInCustomData("disableExtraAttributes", $v);
	}
	
	public function getDisableExtraAttributes()
	{
		return $this->getFromCustomData("disableExtraAttributes");
	}
	
	public function isLive ($url)
	{
		$data = $this->urlExists($url, kConf::get("hls_live_stream_content_type"));
		if(!$data)
		{
			KalturaLog::Info("URL [$url] returned no valid data. Exiting.");
			return $data;
		}
	
		$lines = explode("#EXT-X-STREAM-INF:", trim($data));
	
		foreach ($lines as $line)
		{
			$explodedLine = explode("\n", $line);
			// find a line that does not start with #
			array_shift($explodedLine);	// drop the line of the EXT-X-STREAM-INF
			$streamUrl = null;
			foreach ($explodedLine as $curLine)
			{
				$curLine = trim($curLine);
				if (!$curLine || $curLine[0] == '#')
					continue;
				$streamUrl = $curLine;
				break;
			}
			if (!$streamUrl || strpos($streamUrl, '.m3u8') === false)
				continue;
			$streamUrl = $this->checkIfValidUrl($streamUrl, $url);
	
			$data = $this->urlExists($streamUrl, kConf::get("hls_live_stream_content_type"));
			if (!$data)
				continue;
	
			$segments = explode("#EXTINF:", $data);
			if(!preg_match('/.+\.ts.*/', array_pop($segments), $matches))
				continue;
	
			$tsUrl = $matches[0];
			$tsUrl = $this->checkIfValidUrl($tsUrl, $url);
			if ($this->urlExists($tsUrl ,kConf::get("hls_live_stream_content_type"),'0-1') !== false)
				return true;
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

	/* (non-PHPdoc)
	 * @see DeliveryProfileLive::serve()
	 */
	public function serve($baseUrl, $backupUrl) 
	{
		if(!$backupUrl)
		{
			return parent::serve($baseUrl, $backupUrl);
		}
		
		
		$flavors = array();
		$this->buildM3u8Flavors($baseUrl, $flavors);
		$this->buildM3u8Flavors($backupUrl, $flavors);
		
		$this->params->setResponseFormat('m3u8');
		$renderer = $this->getRenderer($flavors);
		return $renderer;
	}
}

