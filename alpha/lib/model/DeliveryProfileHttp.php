<?php

class DeliveryProfileHttp extends DeliveryProfileVod {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}

	public function setMaxSize($v)
	{
		$this->putInCustomData("maxSize", $v);
	}
	public function getMaxSize()
	{
		return $this->getFromCustomData("maxSize");
	}

	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		if($this->params->getFileExtension())
			$url .= "/name/a.".$this->params->getFileExtension();
		if($this->params->getSeekFromTime() > 0)
			$url .= "/seekFrom/" . $this->params->getSeekFromTime();
		return $url;
	}
	
	// doGetFileSyncUrl - Inherit from parent
	
	/**
	 * @return array $flavors
	 */
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		
		return $flavors;
	}

	/**
	 * returns whether the delivery profile supports the passed deliveryAttributes in this case seekFrom
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes
	 * @return int
	 */
	public function supportsDeliveryDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes) {
		$result = parent::supportsDeliveryDynamicAttributes($deliveryAttributes);

		if ($result == self::DYNAMIC_ATTRIBUTES_NO_SUPPORT)
		{
			return $result;
		}

		if($this->getMaxSize())
		{
			foreach($deliveryAttributes->getFlavorAssets() as $flavorAsset)
			{
				$flavorSizeInBytes = $flavorAsset->getSize() * 1024;
				if($flavorSizeInBytes > $this->getMaxSize())
				{
					return self::DYNAMIC_ATTRIBUTES_NO_SUPPORT;
				}
			}
		}

		return $result;
	}
	
}

