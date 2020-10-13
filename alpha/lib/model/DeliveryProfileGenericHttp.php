<?php

class DeliveryProfileGenericHttp extends DeliveryProfileHttp {
	
	public function setPattern($v)
	{
		$this->putInCustomData("pattern", $v);
	}
	public function getPattern()
	{
		return $this->getFromCustomData("pattern");
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
		return kDeliveryUtils::formatGenericUrl($url, $this->getPattern(), $this->params);
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		$pattern = $this->getPattern();
		if(is_null($pattern))
			$pattern = '{url}';
		return kDeliveryUtils::formatGenericUrl($url, $pattern, $this->params);
	}

	/**
	 * returns whether the delivery profile supports the passed deliveryAttributes in this case seekFrom
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes
	 */
	public function supportsDeliveryDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes) {
		$result = parent::supportsDeliveryDynamicAttributes($deliveryAttributes);
		
		if ($result == self::DYNAMIC_ATTRIBUTES_NO_SUPPORT)
			return $result;
	
		// the profile supports seek if it has the {seekFromSec} placeholder in its pattern
		if ($deliveryAttributes->getSeekFromTime() > 0 && strpos($this->getPattern(), "{seekFromSec}") === false)
			return self::DYNAMIC_ATTRIBUTES_PARTIAL_SUPPORT;

		if($this->getMaxSize())
		{
			foreach($deliveryAttributes->getFlavorAssets() as $flavorAsset)
			{
				$flavorSize = $flavorAsset->getSize() * 1024; // size in MB
				if($flavorSize > $this->getMaxSize())
				{
					return self::DYNAMIC_ATTRIBUTES_PARTIAL_SUPPORT;
				}
			}
		}
				
		return $result;
	}
}

