<?php

class DeliveryProfileAkamaiHdNetworkSmil extends DeliveryProfileVod {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kSmilManifestRenderer';
	}
	
	/**
	 * @return array $flavors
	 */
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
	
		// When playing HDS with Akamai HD the bitrates in the manifest must be unique
		$this->ensureUniqueBitrates($flavors);
		
		return $flavors;
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
		$host = preg_replace("(https?://)", "", $this->getUrl() );
		
		$url = "http://". $host . $url . '/forceproxy/true';
		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$path = parent::doGetFileSyncUrl($fileSync);
		$host = preg_replace("(https?://)", "", $this->getUrl() );
		$urlSuffix = str_replace('\\', '/', $path);
		return "http://" . $host. '/' . ltrim($urlSuffix, '/');
	}
}

