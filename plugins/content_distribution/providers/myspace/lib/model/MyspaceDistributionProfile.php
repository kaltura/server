<?php
class MyspaceDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_DOMAIN = 'domain';
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return MyspaceDistributionPlugin::getProvider();
	}

	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getDomain()				{return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN);}
	public function getMetadataProfileId()			{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setDomain($v)				{$this->putInCustomData(self::CUSTOM_DATA_DOMAIN, $v);}
	public function setMetadataProfileId($v)		{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}	
}