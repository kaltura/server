<?php
class GenericDistributionProfile extends DistributionProfile
{
	// TODO - force provider and provider action editable and required fields
	
	const CUSTOM_DATA_GENERIC_PROVIDER_ID = 'genericProviderId';	
	const CUSTOM_DATA_PROTOCOL = 'protocol';
	const CUSTOM_DATA_SERVERURL = 'serverUrl';
	const CUSTOM_DATA_SERVERPATH = 'serverPath';
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_FTP_PASSIVE_MODE = 'ftpPassiveMode';
	const CUSTOM_DATA_HTTP_FIELD_NAME = 'httpFieldName';
	const CUSTOM_DATA_HTTP_FILE_NAME = 'httpFileName';
	

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
	

	public function getGenericProviderId()			{return $this->getFromCustomData(self::CUSTOM_DATA_GENERIC_PROVIDER_ID);}
		
	public function getProtocol($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_PROTOCOL, $action);}
	public function getServerUrl($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_SERVERURL, $action);}
	public function getServerPath($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_SERVERPATH, $action);}
	public function getUsername($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME, $action);}
	public function getPassword($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD, $action);}
	public function getFtpPassiveMode($action)		{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PASSIVE_MODE, $action);}
	public function getHttpFieldName($action)		{return $this->getFromCustomData(self::CUSTOM_DATA_HTTP_FIELD_NAME, $action);}
	public function getHttpFileName($action)		{return $this->getFromCustomData(self::CUSTOM_DATA_HTTP_FILE_NAME, $action);}
	
	public function setGenericProviderId($v)		{$this->putInCustomData(self::CUSTOM_DATA_GENERIC_PROVIDER_ID, $v);}
		
	public function setProtocol($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_PROTOCOL, $v, $action);}
	public function setServerUrl($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_SERVERURL, $v, $action);}
	public function setServerPath($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_SERVERPATH, $v, $action);}
	public function setUsername($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v, $action);}
	public function setPassword($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v, $action);}
	public function setFtpPassiveMode($v, $action)	{$this->putInCustomData(self::CUSTOM_DATA_FTP_PASSIVE_MODE, $v, $action);}
	public function setHttpFieldName($v, $action)	{$this->putInCustomData(self::CUSTOM_DATA_HTTP_FIELD_NAME, $v, $action);}
	public function setHttpFileName($v, $action)	{$this->putInCustomData(self::CUSTOM_DATA_HTTP_FILE_NAME, $v, $action);}
}