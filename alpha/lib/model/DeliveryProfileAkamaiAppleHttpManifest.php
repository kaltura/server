<?php

class DeliveryProfileAkamaiAppleHttpManifest extends DeliveryProfileAkamaiAppleHttp {
	
	function __construct() {
      	parent::__construct();
      	$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
   }
	
	public function serve()
	{
		$flavor = $this->getSecureHdUrl();
		return $this->getRenderer(array($flavor));
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset) {
		$url = $this->getBaseUrl($flavorAsset);
		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = $fileSync->getFilePath();
		return $url;
	}
	
	/**
	 * @return array
	 */
	protected function getSecureHdUrl()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		$flavor = AkamaiDeliveryUtils::getHDN2ManifestUrl($flavors, $this->params->getMediaProtocol(), $this->getUrl(), '/master.m3u8', '/i');
		if (!$flavor)
		{
			KalturaLog::debug(get_class() . ' failed to find flavor');
			return null;
		}
	
		return $flavor;
	}
}

