<?php

class DeliveryAkamaiAppleHttpManifest extends DeliveryAkamaiAppleHttp {
	
	protected $DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	
	public function serve()
	{
		$flavor = $this->getSecureHdUrl();
		return $this->getRenderer(array($flavor));
	}
	
	/**
	 * @return array
	 */
	protected function getSecureHdUrl()
	{
		// @_!!
		//		Check function exist - getManifestUrl
	
		$originalFormat = $this->params->getFormat();
		$this->params->setFormat(PlaybackProtocol::HTTP);
		$flavors = $this->buildHttpFlavorsArray();
		$this->params->setFormat($originalFormat);
	
		$flavors = $this->sortFlavors($flavors);
	
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

