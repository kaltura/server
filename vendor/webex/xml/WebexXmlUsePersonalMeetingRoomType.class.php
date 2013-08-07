<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUsePersonalMeetingRoomType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $welcomeMessage;
	
	/**
	 *
	 * @var string
	 */
	protected $photoURL;
	
	/**
	 *
	 * @var boolean
	 */
	protected $headerImageBranding;
	
	/**
	 *
	 * @var string
	 */
	protected $headerImageURL;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'welcomeMessage':
				return 'string';
	
			case 'photoURL':
				return 'string';
	
			case 'headerImageBranding':
				return 'boolean';
	
			case 'headerImageURL':
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
			'welcomeMessage',
			'photoURL',
			'headerImageBranding',
			'headerImageURL',
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
		return 'personalMeetingRoomType';
	}
	
	/**
	 * @param string $welcomeMessage
	 */
	public function setWelcomeMessage($welcomeMessage)
	{
		$this->welcomeMessage = $welcomeMessage;
	}
	
	/**
	 * @return string $welcomeMessage
	 */
	public function getWelcomeMessage()
	{
		return $this->welcomeMessage;
	}
	
	/**
	 * @param string $photoURL
	 */
	public function setPhotoURL($photoURL)
	{
		$this->photoURL = $photoURL;
	}
	
	/**
	 * @return string $photoURL
	 */
	public function getPhotoURL()
	{
		return $this->photoURL;
	}
	
	/**
	 * @param boolean $headerImageBranding
	 */
	public function setHeaderImageBranding($headerImageBranding)
	{
		$this->headerImageBranding = $headerImageBranding;
	}
	
	/**
	 * @return boolean $headerImageBranding
	 */
	public function getHeaderImageBranding()
	{
		return $this->headerImageBranding;
	}
	
	/**
	 * @param string $headerImageURL
	 */
	public function setHeaderImageURL($headerImageURL)
	{
		$this->headerImageURL = $headerImageURL;
	}
	
	/**
	 * @return string $headerImageURL
	 */
	public function getHeaderImageURL()
	{
		return $this->headerImageURL;
	}
	
}
		
