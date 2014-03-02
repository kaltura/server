<?php

class DeliveryProfileHttp extends DeliveryProfileVod {
	
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		if($this->params->getFileExtention())
			$url .= "/name/a.".$this->params->getFileExtention();
		if($this->params->getSeekFromTime() > 0)
			$url .= "/seekFrom/" . $this->params->getSeekFromTime();
		return $url;
	}
	
	// doGetFileSyncUrl - Inherit from parent
	protected function getRendererClass() 
	{
		if($this->params->getFormat() == 'url') {
			return 'kRedirectManifestRenderer';
		} 
		return 'kF4MManifestRenderer';
	}
	
	/**
	 * @return kManifestRenderer
	 */
	public function serve()
	{
		// Work with response format instead.
		if($this->params->getFormat() == 'url') {
			$flavorAssets = $this->params->getFlavorAssets();
			$flavorAsset = reset($flavorAssets);
			$flavorInfo = $this->getFlavorHttpUrl($flavorAsset);
			return $this->getRenderer(array($flavorInfo));
		}
	
		$flavors = $this->buildHttpFlavorsArray();
		return $this->getRenderer($flavors);
	}
	
}

