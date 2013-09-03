<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteMeetingPlaceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $mpProfileURL;
	
	/**
	 *
	 * @var string
	 */
	protected $mpLogoutURL;
	
	/**
	 *
	 * @var string
	 */
	protected $mpInternalMeetingLink;
	
	/**
	 *
	 * @var string
	 */
	protected $nbrProfileNumber;
	
	/**
	 *
	 * @var string
	 */
	protected $nbrProfilePassword;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'mpProfileURL':
				return 'string';
	
			case 'mpLogoutURL':
				return 'string';
	
			case 'mpInternalMeetingLink':
				return 'string';
	
			case 'nbrProfileNumber':
				return 'string';
	
			case 'nbrProfilePassword':
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
			'mpProfileURL',
			'mpLogoutURL',
			'mpInternalMeetingLink',
			'nbrProfileNumber',
			'nbrProfilePassword',
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
		return 'meetingPlaceType';
	}
	
	/**
	 * @param string $mpProfileURL
	 */
	public function setMpProfileURL($mpProfileURL)
	{
		$this->mpProfileURL = $mpProfileURL;
	}
	
	/**
	 * @return string $mpProfileURL
	 */
	public function getMpProfileURL()
	{
		return $this->mpProfileURL;
	}
	
	/**
	 * @param string $mpLogoutURL
	 */
	public function setMpLogoutURL($mpLogoutURL)
	{
		$this->mpLogoutURL = $mpLogoutURL;
	}
	
	/**
	 * @return string $mpLogoutURL
	 */
	public function getMpLogoutURL()
	{
		return $this->mpLogoutURL;
	}
	
	/**
	 * @param string $mpInternalMeetingLink
	 */
	public function setMpInternalMeetingLink($mpInternalMeetingLink)
	{
		$this->mpInternalMeetingLink = $mpInternalMeetingLink;
	}
	
	/**
	 * @return string $mpInternalMeetingLink
	 */
	public function getMpInternalMeetingLink()
	{
		return $this->mpInternalMeetingLink;
	}
	
	/**
	 * @param string $nbrProfileNumber
	 */
	public function setNbrProfileNumber($nbrProfileNumber)
	{
		$this->nbrProfileNumber = $nbrProfileNumber;
	}
	
	/**
	 * @return string $nbrProfileNumber
	 */
	public function getNbrProfileNumber()
	{
		return $this->nbrProfileNumber;
	}
	
	/**
	 * @param string $nbrProfilePassword
	 */
	public function setNbrProfilePassword($nbrProfilePassword)
	{
		$this->nbrProfilePassword = $nbrProfilePassword;
	}
	
	/**
	 * @return string $nbrProfilePassword
	 */
	public function getNbrProfilePassword()
	{
		return $this->nbrProfilePassword;
	}
	
}
		
