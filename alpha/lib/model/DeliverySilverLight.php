<?php

class DeliverySilverLight extends DeliveryVod {
	
	protected $DEFAULT_RENDERER_CLASS = 'kSilverLightManifestRenderer';
	
	public function serve()
	{
		$manifestInfo = $this->getSmoothStreamUrl();
		return $this->getRenderer(array($manifestInfo));
	}
	
	/**
	 * @return array
	 */
	protected function getSmoothStreamUrl()
	{
		$urlPrefix = myPartnerUtils::getIisHost($this->getPartnerId(), $this->getStreamerType());
	
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $urlPrefix, $matches))
		{
			$urlPrefix = $matches[1];
		}
		$urlPrefix .= '/';
	
		$this->initDeliveryDynamicAttribtues($this->params->getManifestFileSync());
		$url = $this->getFileSyncUrl($this->params->getManifestFileSync(), false);
		return $this->getFlavorAssetInfo($url, $urlPrefix);
		
	}
}

