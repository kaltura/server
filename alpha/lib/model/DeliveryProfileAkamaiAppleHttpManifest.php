<?php

class DeliveryProfileAkamaiAppleHttpManifest extends DeliveryProfileAkamaiAppleHttp {
	
	protected $DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	
	public function serve()
	{
		$flavor = $this->getSecureHdUrl();
		return $this->getRenderer(array($flavor));
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		$url = $fileSync->getFilePath();
		return $url;
	}
	
	/**
	 * @return array
	 */
	protected function getSecureHdUrl()
	{
		$originalFormat = $this->params->getFormat();
		$this->params->setFormat(PlaybackProtocol::HTTP);
		$flavors = $this->buildHttpFlavorsArray();
		$this->params->setFormat($originalFormat);
	
		$flavors = $this->sortFlavors($flavors);
	
		$this->initDeliveryDynamicAttribtues();
	
		$flavor = AkamaiDeliveryUtils::getManifestUrl($flavors, $this->getUrl(), '/master.m3u8', '/i');
		if (!$flavor)
		{
			KalturaLog::debug(get_class() . ' failed to find flavor');
			return null;
		}
	
		if (strpos($flavor['urlPrefix'], '://') === false)
			$flavor['urlPrefix'] = $this->deliveryAttributes->getMediaProtocol() . '://' . $flavor['urlPrefix'];
	
		return $flavor;
	}
}

