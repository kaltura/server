<?php

class DeliveryProfileAkamaiRtsp extends DeliveryProfileRtsp {
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$flavorAssetId = $flavorAsset->getId();
		$versionString = $this->getFlavorVersionString($flavorAsset);
		
		return "/p/$partnerId/serveFlavor{$versionString}/flavorId/$flavorAssetId";
	}
	
	// doGetFileSyncUrl - Inherited from parent
}

