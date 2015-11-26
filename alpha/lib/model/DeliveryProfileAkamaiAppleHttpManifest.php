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
	
	public function buildServeFlavors()
	{
		$flavor = $this->getSecureHdUrl();
		
		return array($flavor);
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
		$flavors = $this->sortFlavors($flavors);
		$flavor = AkamaiDeliveryUtils::getHDN2ManifestUrl($flavors, $this->params->getMediaProtocol(), $this->getUrl(), '/master.m3u8', '/i', $params);
		if (!$flavor)
		{
			KalturaLog::info(get_class() . ' failed to find flavor');
			return null;
		}
	
		return $flavor;
	}
}

