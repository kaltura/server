<?php

class DeliveryProfileLocalPathHls extends DeliveryProfileGenericAppleHttp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
		$url = $this->doGetFileSyncUrl($fileSync);
		$url .= "/playlist.m3u8";
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$url = kFileSyncUtils::getLocalFilePathForKey(kFileSyncUtils::getKeyForFileSync($fileSync));
		$url = preg_replace('/\.[\w]+$/', '', $url);
		return $url;
	}
	
}