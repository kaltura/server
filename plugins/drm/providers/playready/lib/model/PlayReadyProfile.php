<?php

/**
* @package plugins.playReady
* @subpackage model
*/
class PlayReadyProfile extends DrmProfile
{	
	// ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
	
	const CUSTOM_DATA_PLAY_READY_KEY_SEED = 'play_ready_key_seed';
	
	/**
	 * @return string
	 */
	public function getKeySeed()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PLAY_READY_KEY_SEED);
	}
	
	public function setKeySeed($keySeed)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PLAY_READY_KEY_SEED, $keySeed);
	}
	
	public function getLicenseServerUrl()
	{
		if(!parent::getLicenseServerUrl())
		{
			return PlayReadyPlugin::getPlayReadyConfigParam('license_server_url');
		}
		return parent::getLicenseServerUrl();
	}
}
