<?php

class DeliveryProfileAkamaiHds extends DeliveryProfileHds {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
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
		$flavor = AkamaiDeliveryUtils::getHDN2ManifestUrl($flavors, $this->params->getMediaProtocol(), $this->getUrl(), '/manifest.f4m', '/z');
		if (!$flavor)
		{
			KalturaLog::debug(get_class() . ' failed to find flavor');
			return null;
		}

		return $flavor;
	}
	
}

