<?php

class DeliveryProfileLocalPathAppleHttp extends DeliveryProfileAppleHttp {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	public function setRendererClass($v)
	{
		$this->putInCustomData("rendererClass", $v);
	}
	
	public function getRendererClass()
	{
		return $this->getFromCustomData("rendererClass", null, $this->DEFAULT_RENDERER_CLASS);
	}
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		//In this instance, since we require the local path of the flavor asset, it's the same thing as returning its filesync path.
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
		return $this->getFileSyncUrl($fileSync);
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$url = $fileSync->getFilePath();
		return $url . "/playlist.m3u8";
	}
}

