<?php
class DeliveryProfileLocalPathHds extends DeliveryProfileHds {
	
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4Mv2ManifestRenderer';
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
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
		return $this->getFileSyncUrl($fileSync);
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$url = $fileSync->getFilePath();
		return $url . "/manifest.f4m";
	}
	
}