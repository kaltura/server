<?php
/**
 * @package plugins.exampleDistribution
 * @subpackage model
 */
class ExampleDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_ACCOUNT_ID = 'accountId';
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return ExampleDistributionPlugin::getProvider();
	}

	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getAccountId()				{return $this->getFromCustomData(self::CUSTOM_DATA_ACCOUNT_ID);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setAccountId($v)			{$this->putInCustomData(self::CUSTOM_DATA_ACCOUNT_ID, $v);}
}