<?php

class DeliveryProfileRtmp extends DeliveryProfileVod {
	
	protected $FLV_FILE_EXTENSION = "flv";
	protected $NON_FLV_FILE_EXTENSION = "mp4";
	protected $REDUNDANT_EXTENSIONS = array('.mp4','.flv');
	private $baseUrl = '';
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
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
		$url = $this->formatByExtension($url);
		return $url;
	}
	
	protected function formatByExtension($url, $addPostfix = true) {
		$extension = $this->params->getFileExtension();
		$containerFormat = $this->params->getContainerFormat();
		if( $extension && strtolower($extension) != 'flv' ||
				$containerFormat && strtolower($containerFormat) != 'flash video') {
			$url = "mp4:".ltrim($url,'/');
			if($this->NON_FLV_FILE_EXTENSION)
				$url .= ($addPostfix ? "/name/a." : ".") . $this->NON_FLV_FILE_EXTENSION; 
			
		} else if($this->FLV_FILE_EXTENSION) {
			$url .= ($addPostfix ? "/name/a." : ".") . $this->FLV_FILE_EXTENSION;
		}
		return $url;
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync) {
		$url = parent::doGetFileSyncUrl($fileSync);
		if ($this->getPrefix())
		{
			if (strpos($url, '/') !== 0)
			{
				$url = '/'.$url;
			}
			$url = $this->getPrefix() . $url;
		}
		$url = $this->formatByExtension($url, false); 
		
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
	
	public function buildServeFlavors()
	{
		$baseUrl = null;
		$flavors = $this->buildRtmpFlavorsArray($baseUrl);		
		if(!count($flavors))
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);

		$this->baseUrl = $baseUrl;
		return $flavors;
	}
	
	public function getRenderer($flavors)
	{
		$renderer = parent::getRenderer($flavors);
		$renderer->baseUrl = $this->baseUrl;
		return $renderer;
	}
	
	/**
	 * @param string $baseUrl
	 * @return array
	 */
	protected function buildRtmpFlavorsArray(&$baseUrl)
	{
		$baseUrl = $this->getUrl();
		$flavorAssets = $this->params->getflavorAssets();
		$flavors = array();
		if($this->params->getStorageId())
		{
			// get all flavors with external urls
			foreach($flavorAssets as $flavorAsset)
			{
				$remoteFileSyncs = $this->params->getRemoteFileSyncs();
				$fileSync = $remoteFileSyncs[$flavorAsset->getId()];

				$this->initDeliveryDynamicAttributes($fileSync, $flavorAsset);

				$url = $this->getFileSyncUrl($fileSync, false);
				$url = ltrim($url, "/");

				$flavors[] = $this->getFlavorAssetInfo($url, '', $flavorAsset);
			}
		}
		else
		{
			// get all flavors with kaltura urls
			foreach($flavorAssets as $flavorAsset)
			{
				/* @var $flavorAsset flavorAsset */

				$this->initDeliveryDynamicAttributes(null, $flavorAsset);

				$url = $this->getAssetUrl($flavorAsset, false);
				$url = ltrim($url, "/");

				$flavors[] = $this->getFlavorAssetInfo($url, '', $flavorAsset);
			}
		}

		$baseUrl = $this->params->getMediaProtocol() . '://' . preg_replace('/^rtmp.*?:\/\//', '', $baseUrl);
		$this->finalizeUrls($baseUrl, $flavors);

		return $flavors;
	}
}

