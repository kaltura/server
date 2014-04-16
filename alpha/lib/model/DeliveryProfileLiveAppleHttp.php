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
		if($this->getDisableExtraAttributes() == 1) {
			$parsedUrl = parse_url($baseUrl);
			if (isset($parsedUrl['query']) && strlen($parsedUrl['query']) > 0)
				$baseUrl .= '&';
			else
				$baseUrl .= '?';
			$baseUrl .= "attributes=off";
		}
	}
	
}

