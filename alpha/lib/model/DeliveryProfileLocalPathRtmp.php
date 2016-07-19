<?php

class DeliveryProfileLocalPathRtmp extends DeliveryProfileRtmp {
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
		$url = $this->getFileSyncUrl($fileSync);
		$url = $this->formatByExtension($url, false);
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$url = $fileSync->getFilePath();
		$url = preg_replace('/\.[\w]+$/', '', $url);
		return $url;
	}
	
}

