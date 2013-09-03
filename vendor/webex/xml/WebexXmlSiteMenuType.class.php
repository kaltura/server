<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteMenuType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlSiteUrlType
	 */
	protected $userGuides;
	
	/**
	 *
	 * @var WebexXmlSiteUrlType
	 */
	protected $downloads;
	
	/**
	 *
	 * @var WebexXmlSiteUrlType
	 */
	protected $training;
	
	/**
	 *
	 * @var WebexXmlSiteUrlType
	 */
	protected $contactUs;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportMyResources;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'userGuides':
				return 'WebexXmlSiteUrlType';
	
			case 'downloads':
				return 'WebexXmlSiteUrlType';
	
			case 'training':
				return 'WebexXmlSiteUrlType';
	
			case 'contactUs':
				return 'WebexXmlSiteUrlType';
	
			case 'supportMyResources':
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
			'userGuides',
			'downloads',
			'training',
			'contactUs',
			'supportMyResources',
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
		return 'menuType';
	}
	
	/**
	 * @param WebexXmlSiteUrlType $userGuides
	 */
	public function setUserGuides(WebexXmlSiteUrlType $userGuides)
	{
		$this->userGuides = $userGuides;
	}
	
	/**
	 * @return WebexXmlSiteUrlType $userGuides
	 */
	public function getUserGuides()
	{
		return $this->userGuides;
	}
	
	/**
	 * @param WebexXmlSiteUrlType $downloads
	 */
	public function setDownloads(WebexXmlSiteUrlType $downloads)
	{
		$this->downloads = $downloads;
	}
	
	/**
	 * @return WebexXmlSiteUrlType $downloads
	 */
	public function getDownloads()
	{
		return $this->downloads;
	}
	
	/**
	 * @param WebexXmlSiteUrlType $training
	 */
	public function setTraining(WebexXmlSiteUrlType $training)
	{
		$this->training = $training;
	}
	
	/**
	 * @return WebexXmlSiteUrlType $training
	 */
	public function getTraining()
	{
		return $this->training;
	}
	
	/**
	 * @param WebexXmlSiteUrlType $contactUs
	 */
	public function setContactUs(WebexXmlSiteUrlType $contactUs)
	{
		$this->contactUs = $contactUs;
	}
	
	/**
	 * @return WebexXmlSiteUrlType $contactUs
	 */
	public function getContactUs()
	{
		return $this->contactUs;
	}
	
	/**
	 * @param boolean $supportMyResources
	 */
	public function setSupportMyResources($supportMyResources)
	{
		$this->supportMyResources = $supportMyResources;
	}
	
	/**
	 * @return boolean $supportMyResources
	 */
	public function getSupportMyResources()
	{
		return $this->supportMyResources;
	}
	
}
		
