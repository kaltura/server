<?php

class DeliveryProfileVodPackagerDash extends DeliveryProfileDash {
		
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= '/forceproxy/true';
		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		$url .= VodPackagerDeliveryUtils::getExtraParams($this->params);
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		
		$url .= VodPackagerDeliveryUtils::getExtraParams($this->params);
		return $url;
	}
	
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
				$flavors, 
				$this->getUrl(), 
				'/manifest.mpd', 
				$this->params);
		
		return array($flavor);
	}
}
