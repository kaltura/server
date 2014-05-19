<?php

class DeliveryProfileLevel3Http extends DeliveryProfileHttp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		
		$entry = $flavorAsset->getentry();
		if ($entry->getSecurityPolicy())
			$url = "/s$url";
		
		$url .= '?novar=0';
		return $this->addSeekFromBytes($flavorAsset, $url, 'start');
	}
	
	// doGetFileSyncUrl - Inherit from parent
}

