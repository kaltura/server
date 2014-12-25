<?php

class DeliveryProfileVodPackagerHds extends DeliveryProfileHds {
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= '/forceproxy/true';

		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
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
}
