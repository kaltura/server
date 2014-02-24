<?php

class DeliveryLevel3AppleHttp extends DeliveryAppleHttp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = $this->getBaseUrl($flavorAsset);
		if($this->params->getClipTo())
			$url .= "/clipTo/" . $this->params->getClipTo();
		if($this->params->getExtention())
			$url .= "/name/a." . $this->params->getExtention();
		
		$entry = $flavorAsset->getentry();
		if ($entry->getSecurityPolicy())
		{
			$url = "/s$url";
		}
		$url .= '?novar=0';
		return $this->addSeekFromBytes($flavorAsset, $url, 'start');
	}
	
	// doGetFileSyncUrl - Inherit from parent
}

