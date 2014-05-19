<?php
/**
 * @package plugins.velocix
 * @subpackage storage
 */
class DeliveryProfileVelocixLiveHds extends DeliveryProfileLiveHds
{
	
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
		// For configuration purposes. 
		if(is_null($this->params->getEntryId())) 
			return parent::getTokenizer();
				
		$liveEntry = entryPeer::retrieveByPK($this->params->getEntryId());
		//if stream name doesn't start with 'auth' than the url stream is not tokenized
		if ($liveEntry && substr($liveEntry->getStreamName(), 0, 4) == 'auth') {
			$token = parent::getTokenizer();
			$token->setStreamName($liveEntry->getStreamName());
			$token->setProtocol('hds');
			return $token;
		}
		
		return null;
	}
	
	protected function getParamName() {
		$tokenizer = $this->getTokenizer();
		if($tokenizer && ($tokenizer instanceof kVelocixUrlTokenizer)) 
			return $tokenizer->getParamName();
		return '';
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
