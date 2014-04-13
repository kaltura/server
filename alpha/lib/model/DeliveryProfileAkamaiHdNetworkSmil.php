<?php

class DeliveryProfileAkamaiHdNetworkSmil extends DeliveryProfileVod {
	
	protected $DEFAULT_RENDERER_CLASS = 'kSmilManifestRenderer';
	
	/**
	 * @return kManifestRenderer
	 */
	public function serve()
	{
		$flavors = $this->buildHttpFlavorsArray();
	
		// When playing HDS with Akamai HD the bitrates in the manifest must be unique
		$this->ensureUniqueBitrates($flavors);
	
		return $this->getRenderer($flavors);
	}
	
	protected function ensureUniqueBitrates(array &$flavors)
	{
		$seenBitrates = array();
		foreach ($flavors as &$flavor)
		{
			while (in_array($flavor['bitrate'], $seenBitrates))
			{
				$flavor['bitrate']++;
			}
			$seenBitrates[] = $flavor['bitrate'];
		}
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url = "http://".$this->getHostName().$url.'/forceproxy/true';
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$path = parent::doGetFileSyncUrl($fileSync);
	
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return $path;
	
		if($this->getHostName()) {
			$urlSuffix = str_replace('\\', '/', $path);
			$urlPrefix = "http://" . $this->getHostName();
			return $urlPrefix. '/' . ltrim($urlSuffix, '/');
		}
	
		return $path;
	}
}

