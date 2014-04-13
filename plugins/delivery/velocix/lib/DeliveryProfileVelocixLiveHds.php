<?php
/**
 * @package plugins.velocix
 * @subpackage storage
 */
class DeliveryProfileVelocixLiveHds extends DeliveryProfileLiveHds
{
	// TODO Once we found someone who uses this tokenizer parameters, 
	// These should be externalized.
	public function setParamName($v)
	{
		$this->putInCustomData("paramName", $v);
	}
	
	public function getParamName()
	{
		return $this->getFromCustomData("paramName");
	}
	
	public function setHdsManifestContentType($v)
	{
		$this->putInCustomData("hdsManifestContentType", $v);
	}
	
	public function getHdsManifestContentType()
	{
		return $this->getFromCustomData("hdsManifestContentType");
	}
	
	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		$liveEntry = entryPeer::retrieveByPK($this->params->getEntryId());
		//if stream name doesn't start with 'auth' than the url stream is not tokenized
		if (substr($liveEntry->getStreamName(), 0, 4) == 'auth')
			return parent::getTokenizer;
		
		return null;
	}
	
	public function isLive($url){
		KalturaLog::info('url to check:'.$url);
		$parts = parse_url($url);
		parse_str($parts['query'], $query);
		$token = $query[$this->getParamName()];
		$data = $this->urlExists($url, array($this->getHdsManifestContentType()));
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
			$streamUrl .= $token ? '?'.$this->getParamName()."=$token" : '' ;
			if($this->urlExists($streamUrl, array(),'0-0')  !== false){
				KalturaLog::info('is live:'.$streamUrl);
				return true;
			}
		}
		return false;
	}
	
}
