<?php
/**
 * @package plugins.velocix
 * @subpackage storage
 */
class kVelocixUrlManager extends kUrlManager
{
	//limit the number of urls we check as there could be a gazillion of them
	const MAX_SEGMENTS_TO_CHECK = 3;
	const MAX_FLAVORS_TO_CHECK = 3;

	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		$liveEntry = entryPeer::retrieveByPK($this->entryId);
		$secret = $this->params['shared_secret'];
		$window = $this->params['access_window_seconds'];
		$hdsPaths = array();
		$hdsPaths[] = $this->params['hdsBitratesManifestPath'];
		$hdsPaths[] = $this->params['hdsSegmentsPath'];
		$tokenParamName = $this->params['tokenParamName'];
		$protocol = $this->protocol;
		return new kVelocixUrlTokenizer($window, $secret, $protocol, $liveEntry->getStreamName(), $hdsPaths, $tokenParamName, 'auth_');
	}
	
	
	
	public function isHdsLive($url){
		KalturaLog::info('url to check:'.$url);
		$parts = parse_url($url);
		parse_str($parts['query'], $query);
		$token = $query[$this->params['tokenParamName']];
		$data = $this->urlExists($url, array($this->params['hdsManifestContentType']));
		if(!$data)
		{
			KalturaLog::Info("URL [$url] returned no valid data. Exiting.");
			return false;
		}
		KalturaLog::info('Velocix HDS manifest data:'.$data);
		$dom = new KDOMDocument();
		$dom->loadXML($data);
		$element = $dom->getElementsByTagName('baseURL')->item(0);
		if(!$element){
			KalturaLog::Info("No base url was given");
			return false;
		}
		$baseUrl = $element->nodeValue;
		foreach ($dom->getElementsByTagName('media') as $media){
			$href = $media->getAttribute('href');
			$streamUrl = $baseUrl.$href;
			$streamUrl .= $token ? '?'.$this->params['tokenParamName']."=$token" : '' ;
			if($this->urlExists($streamUrl, array(),'0-0')  !== false){
				KalturaLog::info('is live:'.$streamUrl);
				return true;
			}
		}
		return false;
	}
	
	public function isHlsLive ($url)
	{
		$parts = parse_url($url);
		parse_str($parts['query'], $query);
		$token = $query[$this->params['tokenParamName']];
		$data = $this->urlExists($url, kConf::get("hls_live_stream_content_type"));
		if(!$data)
		{
			KalturaLog::Info("URL [$url] returned no valid data. Exiting.");
			return false;
		}
		KalturaLog::debug("url return data:[$data]");
		$explodedLine = explode("\n", $data);
		$flavorsChecked = 0;
		if (strpos($data,'#EXT-X-STREAM-INF') !== false)
		{
			//handle master manifest
			foreach ($explodedLine as $streamUrl)
			{
				$streamUrl = trim($streamUrl);
				if (!$streamUrl || $streamUrl[0]=='#')
				{
					continue;
				}
				if ($flavorsChecked == self::MAX_FLAVORS_TO_CHECK)
				{
					break;
				}
				$manifestUrl = $this->checkIfValidUrl($streamUrl, $url);
				$manifestUrl .= $token ? '?'.$this->params['tokenParamName']."=$token" : '' ;
				$data = $this->urlExists($manifestUrl, kConf::get("hls_live_stream_content_type"));
				if (!$data)
				{
					continue;
				}
				//handle flavor manifest
				if ($this->checkSegments($data, $token, $manifestUrl))
				{
					return true;
				}
				++$flavorsChecked;
			}
		}
		else if (strpos($data,'#EXTINF') !== false)
		{
			//handle flavor manifest
			return $this->checkSegments($data, $token, $url);
		}
		return false;
	}
	
	private function checkSegments($data, $token, $manifestUrl)
	{
		$segments = explode("\n", $data);
		$segmentsChecked = 0;
		foreach ($segments as $segment){
			if (!$segment || $segment[0]=='#')
			{
				continue;
			}
			if ($segmentsChecked == self::MAX_SEGMENTS_TO_CHECK)
			{
				break;
			}
			$segmentUrl = $this->checkIfValidUrl($segment, $manifestUrl);
			$segmentUrl .= $token ? '?'.$this->params['tokenParamName']."=$token" : '' ;
			if ($this->urlExists($segmentUrl, kConf::get("hls_live_stream_content_type"),'0-0'))
			{
				KalturaLog::info("is live:[$segmentUrl]");
				return true;
			}
			++$segmentsChecked;
		}
		return false;
	}
}
