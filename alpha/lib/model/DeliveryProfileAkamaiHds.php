<?php

class DeliveryProfileAkamaiHds extends DeliveryProfileHds {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
	public function setUseTimingParameters($v)
	{
		$this->putInCustomData("useTimingParameters", $v);
	}
	 
	public function getUseTimingParameters()
	{
		return $this->getFromCustomData("useTimingParameters", null, true);
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
		$params = array();
		if($this->getUseTimingParameters()) {
			if($this->params->getSeekFromTime()) {
				$params['start'] = $this->params->getSeekFromTime();
				$this->params->setSeekFromTime(null);
			}
			if($this->params->getClipTo()) {
				$params['end'] = $this->params->getClipTo();
				$this->params->setClipTo(null);
			}
		}
			
		$flavors = $this->buildHttpFlavorsArray();
		$flavor = AkamaiDeliveryUtils::getHDN2ManifestUrl($flavors, $this->params->getMediaProtocol(), $this->getUrl(), '/manifest.f4m', '/z', $params);
		if (!$flavor)
		{
			KalturaLog::debug(get_class() . ' failed to find flavor');
			return null;
		}
		
		return $flavor;
	}
	
}

