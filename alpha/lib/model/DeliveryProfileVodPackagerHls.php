<?php

class DeliveryProfileVodPackagerHls extends DeliveryProfileAppleHttp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset) 
	{
		$url = $this->getBaseUrl($flavorAsset);
		$url .= VodPackagerDeliveryUtils::getExtraParams($this->params);
		if ($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		return $url . '/index.m3u8';
	}
}
