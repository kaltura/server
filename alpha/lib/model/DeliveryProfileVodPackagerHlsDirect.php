<?php

class DeliveryProfileVodPackagerHlsDirect extends DeliveryProfileVodPackagerHls {
	
	function __construct() 
	{
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = $this->getBaseUrl($flavorAsset);
		
		if($this->params->getClipTo())
			$url .= "/clipTo/" . $this->params->getClipTo();
		
		$url .= '/forceproxy/true';
		// using mp4 hardcoded, to prevent ugly urls when there are captions, 
		//	the vod packager does not care anyway...
		$url .= "/name/a.mp4";
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
			'/master.m3u8',
			$this->params);
		
		return array($flavor);
	}
}
