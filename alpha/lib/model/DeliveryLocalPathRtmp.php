<?php

class DeliveryLocalPathRtmp extends DeliveryRtmp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
		$url = $this->doGetFileSyncUrl($fileSync);
		$url = $this->formatByExtension($url);
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$fileSync = kFileSyncUtils::resolve($fileSync);
		$url = $fileSync->getFilePath();
		$url = preg_replace('/\.[\w]+$/', '', $url);
		return $url;
	}
}

