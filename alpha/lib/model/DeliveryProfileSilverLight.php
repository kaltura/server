<?php

class DeliveryProfileSilverLight extends DeliveryProfileVod {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kSilverLightManifestRenderer';
	}
	
	public function buildServeFlavors()
	{
		$manifestInfo = $this->getSmoothStreamUrl();
		
		return array($manifestInfo);
	}
	
	/**
	 * @return array
	 */
	protected function getSmoothStreamUrl()
	{
		$urlPrefix = $this->getUrl();
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $urlPrefix, $matches))
		{
			$urlPrefix = $matches[1];
		}
		$urlPrefix .= '/';
	
		$this->initDeliveryDynamicAttributes($this->params->getManifestFileSync());
		$url = $this->getFileSyncUrl($this->params->getManifestFileSync(), false);
		return $this->getFlavorAssetInfo($url, $urlPrefix);
	
	}
	
}

