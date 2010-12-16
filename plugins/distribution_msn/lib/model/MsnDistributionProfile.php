<?php
class MsnDistributionProfile extends DistributionProfile
{
	const THUMBNAIL_WIDTH = 1280;
	const THUMBNAIL_HEIGHT = 720;
	
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_DOMAIN = 'domain';

	public function __construct() {
		parent::__construct();
		
		// Add required thumbnail dimensions
		$requiredThumbDimensionsObjects = array();
		
		$requiredThumbDimensions = new kDistributionThumbDimensions();
		$requiredThumbDimensions->setWidth(self::THUMBNAIL_WIDTH);
		$requiredThumbDimensions->setHeight(self::THUMBNAIL_HEIGHT);
		$requiredThumbDimensionsObjects[] = $requiredThumbDimensions;
		
		$this->setRequiredThumbDimensionsObjects($requiredThumbDimensionsObjects);
		
		// TODO - add the required flavor params, maybe from local config, maybe from kConf
	}

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return MsnDistributionPlugin::getProvider();
	}

	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getDomain()					{return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setDomain($v)				{$this->putInCustomData(self::CUSTOM_DATA_DOMAIN, $v);}
	
}