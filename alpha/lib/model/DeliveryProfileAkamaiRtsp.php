<?php

class DeliveryProfileAkamaiRtsp extends DeliveryProfileRtsp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		return $this->getBaseUrl($flavorAsset);
	}
	
	// doGetFileSyncUrl - Inherited from parent
}

