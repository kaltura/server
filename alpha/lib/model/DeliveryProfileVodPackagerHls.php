<?php

class DeliveryProfileVodPackagerHls extends DeliveryProfileAppleHttp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset) 
	{
		$url = $this->getBaseUrl($flavorAsset);
		if ($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		
		$url = $this->addSeekParams($url);
		return $url . '/index.m3u8';
	}
	
	protected function addSeekParams($url) {
		
		$seekStart = $this->params->getSeekFromTime();
		$seekEnd = $this->params->getClipTo();
		
		if($seekStart != -1) {
			$url .= '/clipFrom/'. $this->params->getSeekFromTime();
		} else if($seekEnd) {
			$url .= '/clipFrom/0';
		}
			
		if($seekEnd) {
			$url .= '/clipTo/'. $this->params->getClipTo();
		}
		
		return $url;
	}
}
