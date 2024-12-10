<?php

class DeliveryProfileVodPackagerHls extends DeliveryProfileAppleHttp {
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset) 
	{
		$url = $this->getBaseUrl($flavorAsset);
		if ($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		$url = VodPackagerDeliveryUtils::addExtraParams($url, $this->params);
		return $url . '/index.m3u8';
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		$url = VodPackagerDeliveryUtils::addExtraParams($url, $this->params);
		return $url . '/index.m3u8';
	}

	protected function getPlayServerUrl()
	{
		return $this->generatePlayServerUrl();
	}

	public function setAllowFairplayOffline($v)
	{
		$this->putInCustomData("allowFairplayOffline", $v);
	}

	public function getAllowFairplayOffline()
	{
		return $this->getFromCustomData("allowFairplayOffline", null, false);
	}
	
	public function setSupportFmp4($v)
	{
		$this->putInCustomData("support_fmp4", $v);
	}
	
	public function getSupportFmp4()
	{
		return $this->getFromCustomData("support_fmp4", null, false);
	}

	/**
	 * @return array
	 */
	protected function buildHttpFlavorsArray()
	{
		$flavors = array();
		if ($this->params->getEdgeServerFallback() && $this->params->getEdgeServerIds() && count($this->params->getEdgeServerIds()))
		{
			foreach ($this->params->getEdgeServerIds() as $currEdgeServerId)
			{
				$domainPrefix = $this->getDeliveryServerNodeUrl(true);
				foreach($this->params->getflavorAssets() as $flavorAsset)
				{
					$httpUrl = $this->getFlavorHttpUrl($flavorAsset);
					if ($httpUrl)
					{
						$httpUrl['domainPrefix'] = $domainPrefix;
						$flavors[] = $httpUrl;
					}
				}
			}
		}

		$parentFlavors = parent::buildHttpFlavorsArray();
		
		$dpSupportFmp4Playback = $this->getSupportFmp4();
		$assetsRequireFMP4layback = VodPackagerDeliveryUtils::doAssetsRequireFMP4Playback($this->params->getflavorAssets());
		if($dpSupportFmp4Playback && $assetsRequireFMP4layback)
		{
			foreach ($parentFlavors as &$parentFlavor)
			{
				$parentFlavor['url'] = "container/fmp4/" . $parentFlavor['url'];
			}
		}
		
		$mergedFlavors = array_merge($flavors, $parentFlavors);
		return $mergedFlavors;

	}


}
