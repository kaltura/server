<?php

class DeliveryProfileAppleHttp extends DeliveryProfileVod {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kM3U8ManifestRenderer';
	}
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= "/file/playlist.m3u8";
		return $url;
	}
	
	// doGetFileSyncUrl - Inherit from parent
	
	/**
	 * @return array $flavors
	 */
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->updateAudioLanguages($flavors);
		$flavors = $this->sortFlavors($flavors);
		
		return $flavors;
	}

	protected function updateAudioLanguages($flavors)
	{
		foreach($flavors as &$flavor)
		{
			if (isset($flavor['audioLanguage']) && strcmp($flavor['audioLanguage'],'und') != 0)
			{
				if (kConf::hasParam('three_code_language_partners') &&
					in_array($this->getPartnerId(), kConf::get('three_code_language_partners')))
					continue;
				else
				{
					$twoCodeLanguage = languageCodeManager::getTwoCodeLowerFromThreeCode($flavor['audioLanguage']);
					if ($twoCodeLanguage)
						$flavor['audioLanguage'] = $twoCodeLanguage;
				}
			}
		}
	}

}

