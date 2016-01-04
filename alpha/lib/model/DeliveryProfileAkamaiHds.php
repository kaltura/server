<?php

class DeliveryProfileAkamaiHds extends DeliveryProfileHds {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
	public function setSupportClipping($v)
	{
		$this->putInCustomData("supportClipping", $v);
	}
	 
	public function getSupportClipping()
	{
		return $this->getFromCustomData("supportClipping", null, true);
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= '/forceproxy/true';

		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		return $url;
	}
	
	public function buildServeFlavors()
	{
		$flavor = $this->getSecureHdUrl();
		if (!$flavor)
		{
			KalturaLog::log('No flavor found');
			return null;
		}
		
		return array($flavor);
	}
	
	/**
	 * @return array
	 */
	protected function getSecureHdUrl()
	{
		$params = array();
		if($this->getSupportClipping()) {
			$seekStart = $this->params->getSeekFromTime();
			$seekEnd = $this->params->getClipTo();
			
			if($seekStart != -1) {
				$params['start'] = floor($this->params->getSeekFromTime() / 1000);
				$this->params->setSeekFromTime(-1);
			} else if($seekEnd) {
					$params['start'] = 0;
			}
				
			if($seekEnd) {
				$params['end'] = ceil($this->params->getClipTo() / 1000);
				$this->params->setClipTo(null);
			}
		}
			
		$flavors = $this->buildHttpFlavorsArray();
		$flavor = AkamaiDeliveryUtils::getHDN2ManifestUrl($flavors, $this->params->getMediaProtocol(), $this->getUrl(), '/manifest.f4m', '/z', $params);
		if (!$flavor)
		{
			KalturaLog::info(get_class() . ' failed to find flavor');
			return null;
		}
		
		if ($this->getExtraParams())
		{
			$flavor['url'] = kDeliveryUtils::addQueryParameter($flavor['url'], $this->getExtraParams());
		}	 
		
		return $flavor;
	}
	
}

