<?php

class DeliveryProfileVodPackagerHlsManifest extends DeliveryProfileVodPackagerHls {
	
	function __construct() 
	{
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
			$flavors,
			$this->getUrl(),
			'/master.m3u8',
			$this->params);
		
		return array($flavor);
	}
}
