<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTeleconferenceonlyAccessControlType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlAuoListingType
	 */
	protected $listing;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionPassword;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'listing':
				return 'WebexXmlAuoListingType';
	
			case 'sessionPassword':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'listing',
			'sessionPassword',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'accessControlType';
	}
	
	/**
	 * @param WebexXmlAuoListingType $listing
	 */
	public function setListing(WebexXmlAuoListingType $listing)
	{
		$this->listing = $listing;
	}
	
	/**
	 * @return WebexXmlAuoListingType $listing
	 */
	public function getListing()
	{
		return $this->listing;
	}
	
	/**
	 * @param string $sessionPassword
	 */
	public function setSessionPassword($sessionPassword)
	{
		$this->sessionPassword = $sessionPassword;
	}
	
	/**
	 * @return string $sessionPassword
	 */
	public function getSessionPassword()
	{
		return $this->sessionPassword;
	}
	
}
		
