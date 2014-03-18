<?php

abstract class DeliveryProfileAkamaiAppleHttp extends DeliveryProfileAppleHttp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		if (strpos($flavorAsset->getTags(), flavorParams::TAG_APPLEMBR) === FALSE)
		{
			$url = $this->getBaseUrl($flavorAsset);
			if($this->params->getClipTo())
				$url .= "/clipTo/" . $this->params->getClipTo();
			// we use index_0_av.m3u8 instead of master.m3u8 as temporary solution to overcome
			// an extra "redirection" done on the part of akamai.
			// the auto created master.m3u8 file contains a single item playlist to the index_0_av.m3u8 file
			// this extra "redirection" fails
			return $this->formatHdIos($url);				
		}
		else
			return parent::doGetFlavorAssetUrl($flavorAsset);
	}
	
	protected function formatHdIos($path) {
		$urlPrefix = "http://".$this->getHostName().'/i/';
		$urlSuffix = str_replace('\\', '/', $path)."/index_0_av.m3u8";
		
		return $urlPrefix.ltrim($urlSuffix, '/');
	}
}

