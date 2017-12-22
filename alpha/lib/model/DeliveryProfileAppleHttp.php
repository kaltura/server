<?php

class DeliveryProfileAppleHttp extends DeliveryProfileVod {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kM3U8ManifestRenderer';
	}
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= "/file/playlist.m3u8";
		return $url;
	}
	
	// doGetFileSyncUrl - Inherit from parent
	
	/**
	 * @return array $flavors
	 */
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		
		return $flavors;
	}

}

