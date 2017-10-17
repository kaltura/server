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
		$this->checkIsisMultiAudioFlavorSet($flavors);
		$flavors = $this->sortFlavors($flavors);
		
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
			$flavors,
			$this->getUrl(),
			'/master.m3u8',
			$this->params);
		
		return array($flavor);
	}
	
	private function checkIsisMultiAudioFlavorSet($flavors)
	{
		$audoFalvorMap = array();
		foreach ($flavors as $flavor)
		{
			if(!isset($flavor['audioCodec']) && !isset($flavor['audioLanguageName'])) 
				continue;
			
			$codec = $flavor['audioCodec'];
			$audioLanguageName = $flavor['audioLanguageName'];
			
			if(!isset($audoFalvorMap[$codec]))
				$audoFalvorMap[$codec] = array();
			
			if(!isset($audoFalvorMap[$codec][$audioLanguageName]))
				$audoFalvorMap[$codec][$audioLanguageName] = $flavor;
			
			if(count($audoFalvorMap) > 1)
			{
				$this->isMultiAudio = true;
				break;
			}
			
		}
	}
}
