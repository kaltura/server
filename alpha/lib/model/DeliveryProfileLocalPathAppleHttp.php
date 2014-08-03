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
	
	public function setPattern($v)
	{
		$this->putInCustomData("pattern", $v);
	}
	public function getPattern()
	{
		return $this->getFromCustomData("pattern");
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		//In this instance, since we require the local path of the flavor asset, it's the same thing as returning its filesync path.
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
		return $this->doGetFileSyncUrl($fileSync);
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$url = parent::doGetFileSyncUrl($fileSync);
		return $url . "/playlist.m3u8";
	}
}

