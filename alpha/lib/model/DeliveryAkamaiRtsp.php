<?php

class DeliveryAkamaiRtsp extends DeliveryRtsp {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		return $this->getBaseUrl($flavorAsset);
	}
	
	// doGetFileSyncUrl - Inherited from parent
}

