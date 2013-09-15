<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOneClickMetaDataType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $confName;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionPassword;
	
	/**
	 *
	 * @var WebexXmlEpListingType
	 */
	protected $listing;
	
	/**
	 *
	 * @var string
	 */
	protected $CUVCMeetingID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isInternal;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'confName':
				return 'string';
	
			case 'sessionPassword':
				return 'string';
	
			case 'listing':
				return 'WebexXmlEpListingType';
	
			case 'CUVCMeetingID':
				return 'string';
	
			case 'isInternal':
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
			'confName',
			'sessionPassword',
			'listing',
			'CUVCMeetingID',
			'isInternal',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'confName',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'oneClickMetaDataType';
	}
	
	/**
	 * @param string $confName
	 */
	public function setConfName($confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @return string $confName
	 */
	public function getConfName()
	{
		return $this->confName;
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
	
	/**
	 * @param WebexXmlEpListingType $listing
	 */
	public function setListing(WebexXmlEpListingType $listing)
	{
		$this->listing = $listing;
	}
	
	/**
	 * @return WebexXmlEpListingType $listing
	 */
	public function getListing()
	{
		return $this->listing;
	}
	
	/**
	 * @param string $CUVCMeetingID
	 */
	public function setCUVCMeetingID($CUVCMeetingID)
	{
		$this->CUVCMeetingID = $CUVCMeetingID;
	}
	
	/**
	 * @return string $CUVCMeetingID
	 */
	public function getCUVCMeetingID()
	{
		return $this->CUVCMeetingID;
	}
	
	/**
	 * @param boolean $isInternal
	 */
	public function setIsInternal($isInternal)
	{
		$this->isInternal = $isInternal;
	}
	
	/**
	 * @return boolean $isInternal
	 */
	public function getIsInternal()
	{
		return $this->isInternal;
	}
	
}
		
