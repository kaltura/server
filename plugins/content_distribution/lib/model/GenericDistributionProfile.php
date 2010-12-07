<?php
class GenericDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_GENERIC_PROVIDER_ID = 'genericProviderId';	
	const CUSTOM_DATA_PROTOCOL = 'protocol';
	const CUSTOM_DATA_SERVERURL = 'serverUrl';
	const CUSTOM_DATA_SERVERPATH = 'serverPath';
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_FTPPASSIVEMODE = 'ftpPassiveMode';
	

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		$genericProviderId = $this->getGenericProviderId();
		if(!$genericProviderId)
			return null;
			
		return GenericDistributionProviderPeer::retrieveByPK($genericProviderId);
	}
	

	public function getGenericProviderId()		{return $this->getFromCustomData(self::CUSTOM_DATA_GENERIC_PROVIDER_ID);}	
	public function getProtocol()				{return $this->getFromCustomData(self::CUSTOM_DATA_PROTOCOL);}
	public function getServerUrl()				{return $this->getFromCustomData(self::CUSTOM_DATA_SERVERURL);}
	public function getServerPath()				{return $this->getFromCustomData(self::CUSTOM_DATA_SERVERPATH);}
	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getFtpPassiveMode()			{return $this->getFromCustomData(self::CUSTOM_DATA_FTPPASSIVEMODE);}
	
	public function setGenericProviderId($v)	{$this->putInCustomData(self::CUSTOM_DATA_GENERIC_PROVIDER_ID, $v);}	
	public function setProtocol($v)				{$this->putInCustomData(self::CUSTOM_DATA_PROTOCOL, $v);}
	public function setServerUrl($v)			{$this->putInCustomData(self::CUSTOM_DATA_SERVERURL, $v);}
	public function setServerPath($v)			{$this->putInCustomData(self::CUSTOM_DATA_SERVERPATH, $v);}
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setFtpPassiveMode($v)		{$this->putInCustomData(self::CUSTOM_DATA_FTPPASSIVEMODE, $v);}
	
}