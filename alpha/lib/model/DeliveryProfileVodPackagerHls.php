<?php

class DeliveryProfileVodPackagerHls extends DeliveryProfileAppleHttp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset) 
	{
		$url = $this->getBaseUrl($flavorAsset);
		if ($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		
		$url = $this->addSeekParams($url);
		return $url . '/index.m3u8';
	}
	
	protected function addSeekParams($url) {
		
		$seekStart = $this->params->getSeekFromTime();
		$seekEnd = $this->params->getClipTo();
		
		if($seekStart != -1) {
			$url .= '/start/'. floor($this->params->getSeekFromTime() / 1000);
			$this->params->setSeekFromTime(-1);
		} else if($seekEnd) {
			$url .= '/start/0';
		}
			
		if($seekEnd) {
			$url .= '/end/'. ceil($this->params->getClipTo() / 1000);
			$this->params->setClipTo(null);
		}
		
		return $url;
	}
	
	public function serve()
	{
		if ($this->getHostName() != $_SERVER['HTTP_HOST'])
		{
			kApiCache::setConditionalCacheExpiry(600);		// the result contains a KS so we shouldn't cache it for a long time
			$parsedUrl = parse_url($this->getUrl());
			$flavor = array(
				'urlPrefix' => $this->params->getMediaProtocol() . '://' . $parsedUrl['host'], 
				'url' => $_SERVER["REQUEST_URI"]);
			return new kRedirectManifestRenderer(array($flavor), $this->params->getEntryId());
		}
		
		return parent::serve();
	}
}
