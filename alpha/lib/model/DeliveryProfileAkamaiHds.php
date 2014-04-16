<?php

class DeliveryProfileAkamaiHds extends DeliveryProfileHds {
	
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= '/forceproxy/true';

		if($this->params->getFileExtention())
			$url .= "/name/a." . $this->params->getFileExtention();
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
		$flavors = $this->buildHttpFlavorsArray();
		$this->initDeliveryDynamicAttribtues();

		$flavor = AkamaiDeliveryUtils::getManifestUrl($flavors, $this->getHostName(), '/manifest.f4m', '/z');
		if (!$flavor)
		{
			KalturaLog::debug(get_class() . ' failed to find flavor');
			return null;
		}

		if (strpos($flavor['urlPrefix'], '://') === false)
			$flavor['urlPrefix'] = $this->params->getMediaProtocol() . '://' . $flavor['urlPrefix'];

		return $flavor;
	}
	
}

