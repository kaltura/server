<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesAccessControlType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlSalesListingType
	 */
	protected $listing;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $meetingPassword;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enforcePassword;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'listing':
				return 'WebexXmlSalesListingType';
	
			case 'meetingPassword':
				return 'WebexXml';
	
			case 'enforcePassword':
				return 'boolean';
	
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
			'meetingPassword',
			'enforcePassword',
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
	 * @param WebexXmlSalesListingType $listing
	 */
	public function setListing(WebexXmlSalesListingType $listing)
	{
		$this->listing = $listing;
	}
	
	/**
	 * @return WebexXmlSalesListingType $listing
	 */
	public function getListing()
	{
		return $this->listing;
	}
	
	/**
	 * @param WebexXml $meetingPassword
	 */
	public function setMeetingPassword(WebexXml $meetingPassword)
	{
		$this->meetingPassword = $meetingPassword;
	}
	
	/**
	 * @return WebexXml $meetingPassword
	 */
	public function getMeetingPassword()
	{
		return $this->meetingPassword;
	}
	
	/**
	 * @param boolean $enforcePassword
	 */
	public function setEnforcePassword($enforcePassword)
	{
		$this->enforcePassword = $enforcePassword;
	}
	
	/**
	 * @return boolean $enforcePassword
	 */
	public function getEnforcePassword()
	{
		return $this->enforcePassword;
	}
	
}
		
