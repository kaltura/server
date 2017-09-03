<?php

class DeliveryProfileVodPackagerHlsDirect extends DeliveryProfileVodPackagerHls {
	
	function __construct() 
	{
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = $this->getBaseUrl($flavorAsset);
		
		if($this->params->getClipTo())
			$url .= "/clipTo/" . $this->params->getClipTo();
		
		$url .= '/forceproxy/true';
		// using mp4 hardcoded, to prevent ugly urls when there are captions, 
		//	the vod packager does not care anyway...
		$url .= "/name/a.mp4";
		$url .= VodPackagerDeliveryUtils::getExtraParams($this->params);
		return $url;
	}
	
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
			$flavors,
			$this->getUrl(),
			'/master.m3u8',
			$this->params);
		
		return array($flavor);
	}
	
	protected function flavorCmpFunction ($flavor1, $flavor2)
	{
		$isAudio1 = $flavor1['height'] == 0 && $flavor1['width'] == 0;
		$isAudio2 = $flavor2['height'] == 0 && $flavor2['width'] == 0;
		
		//Move all Dolby audio flavors to the beginning of the audio flavors list
		if($isAudio1 == true && $isAudio1 == $isAudio2)
		{
			if($this->isDolbyAudioCodec($flavor2['audioCodec']))
				return 1;
			
			if($this->isDolbyAudioCodec($flavor1['audioCodec']))
				return -1;
		}
		
		return parent::flavorCmpFunction($flavor1, $flavor2);
	}
	
	private function isDolbyAudioCodec($audioCodec)
	{
		return in_array($audioCodec, array('eac3','e-ac-3','ec-3','ec3','ac3','ac-3','a-c-3'));
	}
}
