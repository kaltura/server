<?php
/**
 * @package plugins.velocix
 * @subpackage storage
 */
class kVelocixUrlManager extends kUrlManager
{

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
		$data = $this->urlExists($url, kConf::get("hds_live_stream_content_type"));
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
			$streamUrl = $baseUrl.$href.'?'.$this->params['tokenParamName']."=$token";
			$streamUrl .= $token ? '?'.$this->params['tokenParamName']."=$token" : '' ;
			if(!$this->urlExists($streamUrl, array(),'0-0')){
				KalturaLog::info('not live:'.$streamUrl);
				continue;
			}
			KalturaLog::info('is live:'.$streamUrl);
			return true;
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
			return $data;
		}

		$lines = explode("#EXT-X-STREAM-INF:", trim($data));

		foreach ($lines as $line)
		{
			$line = trim($line);
			if(!endsWith($line, '.m3u8'))
				continue;
			$streamUrl = $line;
			$streamUrl = $this->checkIfValidUrl($streamUrl, $url);
			$streamUrl .= $token ? '?'.$this->params['tokenParamName']."=$token" : '' ;
			$data = $this->urlExists($streamUrl, kConf::get("hls_live_stream_content_type"));
			if (!$data)
				continue;
				
			$segments = explode("#EXTINF:", $data);
			if(!preg_match('/.+\.ts.*/', array_pop($segments), $matches))
				continue;
			
			$tsUrl = $matches[0];
			$tsUrl = $this->checkIfValidUrl($tsUrl, $url);
			$tsUrl .= $token ? '?'.$this->params['tokenParamName']."=$token" : '' ;
			if ($this->urlExists($tsUrl ,kConf::get("hls_live_stream_content_type"),'0-0'))
				return true;
		}
			
		return false;
	}
}
