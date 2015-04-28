<?php

class DeliveryProfileVodPackagerHds extends DeliveryProfileHds {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= '/forceproxy/true';
		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		$url .= VodPackagerDeliveryUtils::getExtraParams($this->params);
		return $url;
	}
	
	public function serve()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
				$flavors, 
				$this->getUrl(), 
				'/manifest.f4m', 
				$this->params);
		
		return $this->getRenderer(array($flavor));
	}

	/**
	 * returns whether the delivery profile supports the passed deliveryAttributes such as mediaProtocol, flv support, etc..
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes
	 */
	public function supportsDeliveryDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes) {
		if (!parent::supportsDeliveryDynamicAttributes($deliveryAttributes))
			return false;
	
		foreach($deliveryAttributes->getFlavorAssets() as $flavorAsset) {
			if (strtolower($flavorAsset->getFileExt()) == 'flv' || strtolower($flavorAsset->getContainerFormat()) == 'flash video')
				return false;
		}
				
		return true;
	}
}
