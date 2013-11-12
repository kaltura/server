<?php

/**
* @package plugins.widevine
* @subpackage model
*/
class WidevineProfile extends DrmProfile
{	
	// ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
	
	const CUSTOM_DATA_WIDEVINE_KEY = 'widevine_key';
	const CUSTOM_DATA_WIDEVINE_IV = 'widevine_iv';
	const CUSTOM_DATA_WIDEVINE_OWNER = 'widevine_owner';
	const CUSTOM_DATA_WIDEVINE_PORTAL = 'widevine_portal';
	const CUSTOM_DATA_WIDEVINE_MAX_GOP = 'widevine_max_gop';
	const CUSTOM_DATA_WIDEVINE_REG_SERVER_HOST = 'widevine_reg_server_host';
	
	/**
	 * @return string
	 */
	public function getKey()
	{
		$key = $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_KEY);
		if(!$key)
			$key = WidevinePlugin::getWidevineConfigParam('key');
		return $key;
	}
	
	public function setKey($key)
	{
		$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_KEY, $key);
	}
	
	/**
	 * @return string
	 */
	public function getIv()
	{
		$iv = $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_IV);
		if(!$iv)
			$iv = WidevinePlugin::getWidevineConfigParam('iv');
		return $iv;
	}
	
	public function setIv($iv)
	{
		$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_IV, $iv);
	}
	
	/**
	 * @return string
	 */
	public function getOwner()
	{
		$owner = $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_OWNER);
		if(!$owner)
			return WidevinePlugin::getWidevineConfigParam('portal');
		return $owner;
	}
	
	public function setOwner($owner)
	{
		$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_OWNER, $owner);
	}
	
	/**
	 * @return string
	 */
	public function getPortal()
	{
		$portal = $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_PORTAL);
		if(!$portal)
			return WidevinePlugin::getWidevineConfigParam('portal');
		return $portal;		
	}
	
	public function setPortal($portal)
	{
		$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_PORTAL, $portal);
	}
	
	public function getLicenseServerUrl()
	{
		if(!parent::getLicenseServerUrl())
		{
			return WidevinePlugin::getWidevineConfigParam('license_server_url');
		}
		return parent::getLicenseServerUrl();
	}
	
	public function getDefaultPolicy()
	{
		if(!parent::getDefaultPolicy())
		{
			return WidevinePlugin::DEFAULT_POLICY;
		}
		return parent::getDefaultPolicy();
	}
	
	/**
	 * @return int
	 */
	public function getMaxGop()
	{
		$gop = $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_MAX_GOP);
		if(!$gop)
			$gop = WidevinePlugin::getWidevineConfigParam('max_gop');
		return $gop;
	}
	
	public function setMaxGop($gop)
	{
		$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_MAX_GOP, $gop);
	}
	
	/**
	 * @return int
	 */
	public function getRegServerHost()
	{
		$host = $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_REG_SERVER_HOST);
		if(!$host)
			$host = WidevinePlugin::getWidevineConfigParam('reg_server_host');
		return $host;
	}
	
	public function setRegServerHost($host)
	{
		$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_REG_SERVER_HOST, $host);
	}
}
