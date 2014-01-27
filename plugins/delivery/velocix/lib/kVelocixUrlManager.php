<?php
/**
 * @package plugins.velocix
 * @subpackage storage
 */
class kVelocixUrlManager extends kUrlManager
{
	const MAX_SEGMENTS_TO_CHECK = 3;

	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		$liveEntry = entryPeer::retrieveByPK($this->entryId);
		//if stream name doesn't start with 'auth' than the url stream is not tokenized
		if (substr($liveEntry->getStreamName(), 0, 4) == 'auth'){
			$secret = $this->params['shared_secret'];
			$window = $this->params['access_window_seconds'];
			$hdsPaths = array();
			$hdsPaths[] = $this->params['hdsBitratesManifestPath'];
			$hdsPaths[] = $this->params['hdsSegmentsPath'];
			$tokenParamName = $this->params['tokenParamName'];
			$protocol = $this->protocol;
			return new kVelocixUrlTokenizer($window, $secret, $protocol, $liveEntry->getStreamName(), $hdsPaths, $tokenParamName);
		}
		
		return null;
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
		for($i=0 ; $i < self::MAX_SEGMENTS_TO_CHECK ; ++$i)
		{
			$segment = trim($segments[$i]);
			if (!$segment || $segment[0]=='#')
			{
				continue;
			}
			$segmentUrl = $this->checkIfValidUrl($segment, $manifestUrl);
			$segmentUrl .= $token ? '?'.$this->params['tokenParamName']."=$token" : '' ;
			if ($this->urlExists($segmentUrl, kConf::get("hls_live_stream_content_type"),'0-0'))
			{
				KalturaLog::info("is live:[$segmentUrl]");
				return true;
			}
		}
		return false;
	}
}
