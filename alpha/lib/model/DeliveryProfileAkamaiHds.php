<?php

class DeliveryProfileAkamaiHds extends DeliveryProfileHds {
	
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= '/forceproxy/true';

		if($this->params->getExtention())
			$url .= "/name/a." . $this->params->getExtention();
		return $url;
	}
	
	public function serve()
	{
		$flavor = $this->getSecureHdUrl();
		if (!$flavor)
		{
			KalturaLog::debug('No flavor found');
			return null;
		}
		
		return $this->getRenderer(array($flavor));
	}
	
	/**
	 * @return array
	 */
	protected function getSecureHdUrl()
	{
		// Similar to AppleHttp
		// @_!!
		//		Check function exist - getManifestUrl
	
		$flavors = $this->buildHttpFlavorsArray();
		$this->initDeliveryDynamicAttribtues();

		$flavor = $this->getManifestUrl($flavors);
		if (!$flavor)
		{
			KalturaLog::debug(get_class() . ' failed to find flavor');
			return null;
		}

		if (strpos($flavor['urlPrefix'], '://') === false)
			$flavor['urlPrefix'] = $this->getStreamerType() . '://' . $flavor['urlPrefix'];

		return $flavor;
	}
}

