<?php

class DeliveryProfileRtsp extends DeliveryProfileVod {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRtspManifestRenderer';
	}
	
	/**
	 * @return array $flavors
	 */
	public function buildServeFlavors()
	{
		$flavorAssets = $this->params->getFlavorAssets();
		$flavorAsset = reset($flavorAssets);
		$flavorInfo = $this->getFlavorHttpUrl($flavorAsset);
		
		return array($flavorInfo);
	}
}

