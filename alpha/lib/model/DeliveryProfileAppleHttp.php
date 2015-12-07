<?php

class DeliveryProfileAppleHttp extends DeliveryProfileVod {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kM3U8ManifestRenderer';
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= "/file/playlist.m3u8";
		return $url;
	}
	
	// doGetFileSyncUrl - Inherit from parent
	
	/**
	 * @return kManifestRenderer
	 */
	public function serve()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		return $this->getRenderer($flavors);
	}
	
}

