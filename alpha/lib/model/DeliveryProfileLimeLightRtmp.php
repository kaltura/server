<?php

class DeliveryProfileLimeLightRtmp extends DeliveryProfileRtmp {
	
	function __construct() {
		parent::__construct();
		$this->FLV_FILE_EXTENSION = null;
		$this->NON_FLV_FILE_EXTENSION = null;
		$this->REDUNDANT_EXTENSIONS = array();
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url = "/s" . $url;
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$url = $fileSync->getFilePath();
		return $url;
	}
}

