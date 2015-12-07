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
		return $url;
	}
	
	public function serve()
	{
		$flavors = $this->buildHttpFlavorsArray();
		
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
				$flavors, 
				$this->getUrl(), 
				'/manifest.mpd', 
				$this->params);
		
		return $this->getRenderer(array($flavor));
	}
}
