<?php

class DeliveryProfileVodPackagerHds extends DeliveryProfileHds {
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= '/forceproxy/true';
		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		$url = VodPackagerDeliveryUtils::addExtraParams($url, $this->params);
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		$url = VodPackagerDeliveryUtils::addExtraParams($url, $this->params);
		return $url;
	}
	
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
				$flavors, 
				$this->getUrl(), 
				'/manifest.f4m', 
				$this->params);
		
		return array($flavor);
	}

	/**
	 * returns whether the delivery profile supports the passed deliveryAttributes such as mediaProtocol, flv support, etc..
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes
	 */
	public function supportsDeliveryDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes) {
		$result = parent::supportsDeliveryDynamicAttributes($deliveryAttributes);
		
		if ($result == self::DYNAMIC_ATTRIBUTES_NO_SUPPORT)
			return $result;
	
		foreach($deliveryAttributes->getFlavorAssets() as $flavorAsset) {
			if (strtolower($flavorAsset->getFileExt()) == 'flv' || strtolower($flavorAsset->getContainerFormat()) == 'flash video')
				return self::DYNAMIC_ATTRIBUTES_NO_SUPPORT;
		}
				
		return $result;
	}

	protected function getPlayServerUrl()
	{
		return $this->generatePlayServerUrl();
	}
}
