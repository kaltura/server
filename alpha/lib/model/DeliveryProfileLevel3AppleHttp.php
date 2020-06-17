<?php

class DeliveryProfileLevel3AppleHttp extends DeliveryProfileAppleHttp {
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = $this->getBaseUrl($flavorAsset);
		if($this->params->getClipTo())
		{
			$url = self::insertClipTo($url, $this->params->getClipTo());
		}

		if($this->params->getExtension())
		{
			$url .= "/name/a." . $this->params->getExtension();
		}

//		Trying to remove this code, based on the compat check. @_!!		
// 		$entry = $flavorAsset->getentry();
// 		if ($entry->getSecurityPolicy())
// 		{
// 			$url = "/s$url";
// 		}
		$url .= '?novar=0';
		return $this->addSeekFromBytes($flavorAsset, $url, 'start');
	}
	
	// doGetFileSyncUrl - Inherit from parent
}

