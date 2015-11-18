<?php

class DeliveryProfileGenericAppleHttp extends DeliveryProfileAppleHttp {
	
	public function setPattern($v)
	{
		$this->putInCustomData("pattern", $v);
	}
	public function getPattern()
	{
		return $this->getFromCustomData("pattern");
	}
	
	public function setRendererClass($v)
	{
		$this->putInCustomData("rendererClass", $v);
	}
	
	public function getRendererClass()
	{
		return $this->getFromCustomData("rendererClass", null, $this->DEFAULT_RENDERER_CLASS);
	}
	
	public function setManifestRedirect($v)
	{
		$this->putInCustomData("manifestRedirect", $v);
	}
	
	public function getManifestRedirect()
	{
		return $this->getFromCustomData("manifestRedirect");
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset) 
	{
		$url = $this->getBaseUrl($flavorAsset);
		if ($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		
		return kDeliveryUtils::formatGenericUrl($url, $this->getPattern(), $this->params);
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		$pattern = $this->getPattern();
		if(is_null($pattern))
			$pattern = '/hls-vod/{url}.m3u8';
		return kDeliveryUtils::formatGenericUrl($url, $pattern, $this->params);
	}
	
	public function buildServeFlavors()
	{
		if ($this->getManifestRedirect() && $this->getHostName() != $_SERVER['HTTP_HOST'])
		{
			kApiCache::setConditionalCacheExpiry(600);		// the result contains a KS so we shouldn't cache it for a long time
			$parsedUrl = parse_url($this->getUrl());
			$flavor = array(
				'urlPrefix' => $this->params->getMediaProtocol() . '://' . $parsedUrl['host'], 
				'url' => $_SERVER["REQUEST_URI"]);
			
			return array($flavor);
		}
		
		return parent::buildServeFlavors();
	}
	
	public function getRenderer($flavors)
	{
		if ($this->getManifestRedirect() && $this->getHostName() != $_SERVER['HTTP_HOST'])
		{
			return new kRedirectManifestRenderer($flavors, $this->params->getEntryId());
		}
		
		return parent::getRenderer($flavors);
	}
}

