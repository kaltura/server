<?php

class DeliveryProfileAkamaiHdNetworkSmil extends DeliveryProfileHd {
	
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	
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
	
		if(!$this->getHostName()) {
			$urlSuffix = str_replace('\\', '/', $path);
			$urlPrefix = "http://" . $this->getHostName();
			return $urlPrefix. '/' . ltrim($urlSuffix, '/');
		}
	
		return $path;
	}
	
	/**
	 * @return kManifestRenderer
	 */
	public function serve()
	{
		kApiCache::setConditionalCacheExpiry(600);		// the result contains a KS so we shouldn't cache it for a long time
		
		$mediaUrl = requestUtils::getHost().str_replace("f4m", "smil", str_replace("hdnetwork", "hdnetworksmil", $_SERVER["REQUEST_URI"])); 

		$renderer = $this->getRenderer(array());
		$renderer->mediaUrl = $mediaUrl;
		return $renderer;
	}
}

