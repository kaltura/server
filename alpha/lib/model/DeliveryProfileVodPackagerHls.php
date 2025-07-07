<?php

class DeliveryProfileVodPackagerHls extends DeliveryProfileAppleHttp
{
	protected $serveAsFmp4 = false;
	
	/**
	 * @return array $flavors
	 */
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		
		$hasAudioOnlyFlavor = $this->hasAudioOnlyFlavor($flavors);
		if($hasAudioOnlyFlavor && $this->serveAsFmp4)
		{
			//If audio flavors are present and fmp4 is supported, force unmuxed segments
			$flavors = $this->forceUnmuxedSegments($flavors, "url");
		}
		
		return $flavors;
	}

	protected function updateFlavorUrl(&$flavor)
	{
		$isVideo = !isset($flavor[self::AUDIO_CODEC]) && !isset($flavor[self::AUDIO_LANGUAGE_NAME]);
		$flavor['url'] .= "/index-" . ($isVideo ? "v" : "a") . "1.m3u8";
	}
	
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
		$this->serveAsFmp4 = VodPackagerDeliveryUtils::doAssetsRequireFMP4Playback($this->params->getflavorAssets());
		if($this->params->getSimuliveEventId())
		{
			$simuliveEvent = ScheduleEventPeer::retrieveByPK($this->params->getSimuliveEventId());
			if($simuliveEvent)
			{
				$res = kSimuliveUtils::getEventDetailsByEvent($simuliveEvent);
				$assets = array_values(array_map((function($assets) { return $assets[0]; }), $res[1] ));
				$this->serveAsFmp4 = VodPackagerDeliveryUtils::doAssetsRequireFMP4Playback(array_filter($assets));
			}
		}
		
		//TO-DO: Once we verify codecs are well calculated, we can remove this check, until than include only when serveAsFmp4 is true
		if(!$this->serveAsFmp4)
		{
			$this->removeCodecsString($parentFlavors);
		}
		
		if($this->serveAsFmp4)
		{
			foreach ($parentFlavors as &$parentFlavor)
			{
				$parentFlavor['url'] = "container/fmp4/" . $parentFlavor['url'];
			}
		}
		
		$mergedFlavors = array_merge($flavors, $parentFlavors);
		return $mergedFlavors;
	}
	
	private function removeCodecsString($flavors)
	{
		foreach ($flavors as &$flavor)
		{
			$flavor['codecs'] = '';
		}
		
		return $flavors;
	}

}
