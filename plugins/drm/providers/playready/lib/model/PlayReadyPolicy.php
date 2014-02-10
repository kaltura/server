<?php

/**
* @package plugins.playReady
* @subpackage model
*/
class PlayReadyPolicy extends DrmPolicy
{	
	// ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
	
	const CUSTOM_DATA_PLAY_READY_GRACE_PERIOD = 'play_ready_grace_period';
	const CUSTOM_DATA_PLAY_READY_LICENSE_REMOVAL_POLICY = 'play_ready_license_removal_policy';
	const CUSTOM_DATA_PLAY_READY_LICENSE_REMOVAL_DURATION = 'play_ready_license_removal_duration';
	const CUSTOM_DATA_PLAY_READY_MIN_SECURITY_LEVEL = 'play_ready_min_security_level';
	const CUSTOM_DATA_PLAY_READY_RIGHTS = 'play_ready_rights';
	
	/**
	 * @return int
	 */
	public function getGracePeriod()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PLAY_READY_GRACE_PERIOD);
	}
	
	public function setGracePeriod($period)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PLAY_READY_GRACE_PERIOD, $period);
	}
	
	/**
	 * @return int
	 */
	public function getLicenseRemovalPolicy()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PLAY_READY_LICENSE_REMOVAL_POLICY);
	}
	
	public function setLicenseRemovalPolicy($policy)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PLAY_READY_LICENSE_REMOVAL_POLICY, $policy);
	}
	
	/**
	 * @return int
	 */
	public function getLicenseRemovalDuration()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PLAY_READY_LICENSE_REMOVAL_DURATION);
	}
	
	public function setLicenseRemovalDuration($duration)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PLAY_READY_LICENSE_REMOVAL_DURATION, $duration);
	}
	
	/**
	 * @return int
	 */
	public function getMinSecurityLevel()
	{
		$level = $this->getFromCustomData(self::CUSTOM_DATA_PLAY_READY_MIN_SECURITY_LEVEL);
		if(!$level)
			return PlayReadyPlugin::getPlayReadyConfigParam('min_security_level');
		return $level;
	}
	
	public function setMinSecurityLevel($level)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PLAY_READY_MIN_SECURITY_LEVEL, $level);
	}
	
	/**
	 * @return array of PlayReadyRight
	 */
	public function getRights()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PLAY_READY_RIGHTS);
	}
	
	public function setRights($rights)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PLAY_READY_RIGHTS, $rights);
	}
}
