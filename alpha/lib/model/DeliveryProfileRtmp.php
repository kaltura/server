<?php

class DeliveryProfileRtmp extends DeliveryProfileVod {
	
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	protected $FLAVOR_FALLBACK = "flv";
	protected $REDUNDANT_EXTENSIONS = array('.mp4','.flv');
	
	public function setEnforceRtmpe($v)
	{
		$this->putInCustomData("enforceRtmpe", $v);
	}
	
	public function getEnforceRtmpe()
	{
		return $this->getFromCustomData("enforceRtmpe");
	}
	
	public function setPrefix($v)
	{
		$this->putInCustomData("prefix", $v);
	}
	
	public function getPrefix()
	{
		return $this->getFromCustomData("prefix");
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= '/forceproxy/true';
		$url = $this->formatByExtension($url, $this->FLAVOR_FALLBACK);
		return $url;
	}
	
	protected function formatByExtension($url, $fallback = null) {
		$extension = $this->params->getFileExtention();
		$containerFormat = $this->params->getContainerFormat();
		if( $extension && strtolower($extension) != 'flv' ||
				$containerFormat && strtolower($containerFormat) != 'flash video') {
			$url = "mp4:".ltrim($url,'/');
			if(!is_null($fallback))
				$url .= "/name/a.mp4"; 
			
		} else if($fallback) {
			$url .= "/name/a." . $fallback;
		}
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$url = parent::doGetFileSyncUrl($fileSync);
		$storageProfile = StorageProfilePeer::retrieveByPK($this->params->getStorageProfileId());
		if ($storageProfile->getRTMPPrefix())
		{
			if (strpos($url, '/') !== 0)
			{
				$url = '/'.$url;
			}
			$url = $storageProfile->getRTMPPrefix(). $url;
		}
		$url = $this->formatByExtension($url); 
		
		$url = str_replace($this->REDUNDANT_EXTENSIONS, '', $url);
		return $url;
	}
	
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		if ($this->getEnforceRtmpe())
		{
			$baseUrl = preg_replace('/^rtmp:\/\//', 'rtmpe://', $baseUrl);
			$baseUrl = preg_replace('/^rtmpt:\/\//', 'rtmpte://', $baseUrl);
		}
		parent::finalizeUrls($baseUrl, $flavorsUrls);
	}
	
	// -------------------------------------
	// -----   Serve functionality  --------
	// -------------------------------------
	
	public function serve()
	{
		$baseUrl = null;
		$flavors = $this->buildRtmpFlavorsArray($baseUrl);		
		if(!count($flavors))
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);

		$renderer = $this->getRenderer($flavors);
		$renderer->baseUrl = $baseUrl;
		return $renderer;
	}
	
	/**
	 * @param string $baseUrl
	 * @return array
	 */
	protected function buildRtmpFlavorsArray(&$baseUrl)
	{
		$flavorAssets = $this->params->getflavorAssets();
		$flavors = array();
		if($this->params->getStorageId())
		{
			$storageProfile = StorageProfilePeer::retrieveByPK($this->params->getStorageProfileId());
			$baseUrl = $storageProfile->getDeliveryRmpBaseUrl();

			// get all flavors with external urls
			foreach($flavorAssets as $flavorAsset)
			{
				$remoteFileSyncs = $this->params->getRemoteFileSyncs();
				$fileSync = $remoteFileSyncs[$flavorAsset->getId()];

				$this->initDeliveryDynamicAttribtues($fileSync, $flavorAsset);

				$url = $this->getFileSyncUrl($fileSync, false);
				$url = ltrim($url, "/");

				$flavors[] = $this->getFlavorAssetInfo($url, '', $flavorAsset);
			}
		}
		else
		{
			$baseUrl = $this->getUrl();

			// get all flavors with kaltura urls
			foreach($flavorAssets as $flavorAsset)
			{
				/* @var $flavorAsset flavorAsset */

				$this->initDeliveryDynamicAttribtues(null, $flavorAsset);

				$url = $this->getAssetUrl($flavorAsset, false);
				$url = ltrim($url, "/");

				$flavors[] = $this->getFlavorAssetInfo($url, '', $flavorAsset);
			}
		}

		if(strpos($this->params->getMediaProtocol(), "rtmp") === 0)
			$baseUrl = $this->params->getMediaProtocol() . '://' . preg_replace('/^rtmp.*?:\/\//', '', $baseUrl);
		
		$this->finalizeUrls($baseUrl, $flavors);

		return $flavors;
	}
}

