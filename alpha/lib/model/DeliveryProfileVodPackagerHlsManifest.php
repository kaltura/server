<?php

class DeliveryProfileVodPackagerHlsManifest extends DeliveryProfileVodPackagerHls {
	
	function __construct() 
	{
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$this->checkIsMultiAudioFlavorSet($flavors);
		$flavors = $this->sortFlavors($flavors);
		
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
			$flavors,
			$this->getUrl(),
			'/master.m3u8',
			$this->params);
		
		return array($flavor);
	}
	
	private function checkIsMultiAudioFlavorSet($flavors)
	{
		$audioFlavorsMap = array();
		foreach ($flavors as $flavor)
		{
			if(!isset($flavor['audioCodec']) && !isset($flavor['audioLanguageName'])) 
				continue;
			
			$codec = $flavor['audioCodec'];
			$audioLanguageName = $flavor['audioLanguageName'];
			$codecAndLang = $codec . "_" . $audioLanguageName;
			$audioFlavorsMap[$codecAndLang] = true;
			
			if(count($audioFlavorsMap) > 1)
			{
				$this->isMultiAudio = true;
				break;
			}
			
		}
	}
}
