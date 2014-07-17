<?php

class DeliveryProfileLocalPathRtmp extends DeliveryProfileRtmp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
		$url = $this->doGetFileSyncUrl($fileSync);
		$url = $this->formatByExtension($url);
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$url = $fileSync->getFilePath();
		$url = preg_replace('/\.[\w]+$/', '', $url);
		return $url;
	}
	
	protected function formatByExtension($url) {
		$extension = $this->params->getFileExtension();
		$containerFormat = $this->params->getContainerFormat();
		if( $extension && strtolower($extension) != 'flv' ||
				$containerFormat && strtolower($containerFormat) != 'flash video') {
			$url = "mp4:".ltrim($url,'/');
			if($this->NON_FLV_FILE_EXTENSION)
				$url .= "." .  $this->NON_FLV_FILE_EXTENSION; 
			
		} else if($this->FLV_FILE_EXTENSION) {
			$url .= "." . $this->FLV_FILE_EXTENSION;
		}
		return $url;
	}
}

