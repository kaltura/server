<?php

class DeliveryLimeLightRtmp extends DeliveryRtmp {
	
	protected $FLAVOR_FALLBACK = null;
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url = "/s" . $url;
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$fileSync = kFileSyncUtils::resolve($fileSync);
		$url = $fileSync->getFilePath();
		return $url;
	}
}

