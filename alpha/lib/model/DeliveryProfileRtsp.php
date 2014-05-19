<?php

class DeliveryProfileRtsp extends DeliveryProfileVod {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRtspManifestRenderer';
	}
	
	/**
	 * @return kManifestRenderer
	 */
	public function serve()
	{
		$flavorAssets = $this->params->getFlavorAssets();
		$flavorAsset = reset($flavorAssets);
		$flavorInfo = $this->getFlavorHttpUrl($flavorAsset);
		return $this->getRenderer(array($flavorInfo));
	}
}

