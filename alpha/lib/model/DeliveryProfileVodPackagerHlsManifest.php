<?php

class DeliveryProfileVodPackagerHlsManifest extends DeliveryProfileVodPackagerHls {

	const MASTER_MANIFEST_STR = '/master';
	const M3U8_SUFFIX = '.m3u8';
	
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
		$urlSuffix = $this->getManifestUrlSuffix();
		
		$flavor = VodPackagerDeliveryUtils::getVodPackagerUrl(
			$flavors,
			$this->getUrl(),
			$urlSuffix,
			$this->params);
		
		return array($flavor);
	}

	/**
	 * @param array $flavors
	 * @return array
	 */
	protected function sortFlavors($flavors)
	{
		$sortedFlavors = parent::sortFlavors($flavors);
		if($this->params->getMuxedAudioLanguage())
		{
			//Order audio flavors after video flavors
			$audioflavors = array();
			$videoflavors = array();
			foreach ($sortedFlavors as $flavor)
			{
				if (!isset($flavor['audioCodec']) && !isset($flavor['audioLanguageName']))
				{
					$videoflavors[] = $flavor;
				}
				else
				{
					$audioflavors[] = $flavor;
				}
			}
			$sortedFlavors = array_merge($videoflavors, $audioflavors);
		}
		return $sortedFlavors;
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

	protected function getManifestUrlSuffix()
	{
		if($this->params->getMuxedAudioLanguage())
		{
			$urlSuffix = self::MASTER_MANIFEST_STR . $this->addMuxedAudioLanguageToManifestUrl() . self::M3U8_SUFFIX;
		}
		else
		{
			$urlSuffix = self::MASTER_MANIFEST_STR . self::M3U8_SUFFIX;
		}

		return $urlSuffix;
	}

	protected function addMuxedAudioLanguageToManifestUrl()
	{
		$muxedAudioLanguagePart = '';
		if(languageCodeManager::getObjectFromThreeCode(strtolower($this->params->getMuxedAudioLanguage())))
		{
			$muxedAudioLanguagePart = '-l' . $this->params->getMuxedAudioLanguage();
		}

		$this->params->setMuxedAudioLanguage(null);
		return $muxedAudioLanguagePart;
	}
}
