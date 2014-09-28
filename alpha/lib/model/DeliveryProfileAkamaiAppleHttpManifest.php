<?php

class DeliveryProfileAkamaiAppleHttpManifest extends DeliveryProfileAkamaiAppleHttp {
	
	function __construct() {
      	parent::__construct();
      	$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
   }
   
   public function setSupportClipping($v)
   {
   	$this->putInCustomData("supportClipping", $v);
   }
   
   public function getSupportClipping()
   {
   	return $this->getFromCustomData("supportClipping", null, true);
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
		$params = array();
		if($this->getSupportClipping()) {
			if($this->params->getSeekFromTime() != -1) {
				$params['start'] = $this->params->getSeekFromTime();
				$this->params->setSeekFromTime(-1);
			}
			if($this->params->getClipTo()) {
				$params['end'] = $this->params->getClipTo();
				$this->params->setClipTo(null);
			}
		}
		
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		$flavor = AkamaiDeliveryUtils::getHDN2ManifestUrl($flavors, $this->params->getMediaProtocol(), $this->getUrl(), '/master.m3u8', '/i', $params);
		if (!$flavor)
		{
			KalturaLog::debug(get_class() . ' failed to find flavor');
			return null;
		}
	
		return $flavor;
	}
}

