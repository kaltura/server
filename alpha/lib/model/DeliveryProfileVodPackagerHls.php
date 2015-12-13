<?php

class DeliveryProfileVodPackagerHls extends DeliveryProfileAppleHttp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset) 
	{
		$url = $this->getBaseUrl($flavorAsset);
		if ($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		$url .= VodPackagerDeliveryUtils::getExtraParams($this->params);
		return $url . '/index.m3u8';
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		
		$url .= VodPackagerDeliveryUtils::getExtraParams($this->params);
		return $url . '/index.m3u8';
	}
}
