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

		$urlSuffix = '/master' . $this->addMuxedAudioLanguageToManifestUrl() . '.m3u8';
		
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
			$flavors,
			$this->getUrl(),
			$urlSuffix,
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
			
			$codecAndLang = $flavor['audioCodec'] . "_" . $flavor['audioLanguageName'];
			$audioFlavorsMap[$codecAndLang] = true;
			
			if(count($audioFlavorsMap) > 1)
			{
				$this->isMultiAudio = true;
				break;
			}
			
		}
	}

	protected function addMuxedAudioLanguageToManifestUrl()
	{
		$muxedAudioLanguagePart = '';
		if(languageCodeManager::getObjectFromThreeCode(strtolower($this->params->getMuxedAudioLanguage())))
		{
			$muxedAudioLanguagePart = '-l' . $this->params->getMuxedAudioLanguage();
			$this->params->setMuxedAudioLanguage(null);
		}

		return $muxedAudioLanguagePart;
	}
}
