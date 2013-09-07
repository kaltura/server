<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventExtOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $enrollmentNumber;
	
	/**
	 *
	 * @var string
	 */
	protected $destinationURL;
	
	/**
	 *
	 * @var boolean
	 */
	protected $allowInviteFriend;
	
	/**
	 *
	 * @var WebexXmlComAttendeeListViewType
	 */
	protected $viewAttendeeList;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $participantLimit;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayQuickStartHost;
	
	/**
	 *
	 * @var boolean
	 */
	protected $voip;
	
	/**
	 *
	 * @var boolean
	 */
	protected $emailInvitations;
	
	/**
	 *
	 * @var boolean
	 */
	protected $registration;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'enrollmentNumber':
				return 'integer';
	
			case 'destinationURL':
				return 'string';
	
			case 'allowInviteFriend':
				return 'boolean';
	
			case 'viewAttendeeList':
				return 'WebexXmlComAttendeeListViewType';
	
			case 'participantLimit':
				return 'WebexXml';
	
			case 'displayQuickStartHost':
				return 'boolean';
	
			case 'voip':
				return 'boolean';
	
			case 'emailInvitations':
				return 'boolean';
	
			case 'registration':
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
			'enrollmentNumber',
			'destinationURL',
			'allowInviteFriend',
			'viewAttendeeList',
			'participantLimit',
			'displayQuickStartHost',
			'voip',
			'emailInvitations',
			'registration',
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
		return 'extOptionsType';
	}
	
	/**
	 * @param integer $enrollmentNumber
	 */
	public function setEnrollmentNumber($enrollmentNumber)
	{
		$this->enrollmentNumber = $enrollmentNumber;
	}
	
	/**
	 * @return integer $enrollmentNumber
	 */
	public function getEnrollmentNumber()
	{
		return $this->enrollmentNumber;
	}
	
	/**
	 * @param string $destinationURL
	 */
	public function setDestinationURL($destinationURL)
	{
		$this->destinationURL = $destinationURL;
	}
	
	/**
	 * @return string $destinationURL
	 */
	public function getDestinationURL()
	{
		return $this->destinationURL;
	}
	
	/**
	 * @param boolean $allowInviteFriend
	 */
	public function setAllowInviteFriend($allowInviteFriend)
	{
		$this->allowInviteFriend = $allowInviteFriend;
	}
	
	/**
	 * @return boolean $allowInviteFriend
	 */
	public function getAllowInviteFriend()
	{
		return $this->allowInviteFriend;
	}
	
	/**
	 * @param WebexXmlComAttendeeListViewType $viewAttendeeList
	 */
	public function setViewAttendeeList(WebexXmlComAttendeeListViewType $viewAttendeeList)
	{
		$this->viewAttendeeList = $viewAttendeeList;
	}
	
	/**
	 * @return WebexXmlComAttendeeListViewType $viewAttendeeList
	 */
	public function getViewAttendeeList()
	{
		return $this->viewAttendeeList;
	}
	
	/**
	 * @param WebexXml $participantLimit
	 */
	public function setParticipantLimit(WebexXml $participantLimit)
	{
		$this->participantLimit = $participantLimit;
	}
	
	/**
	 * @return WebexXml $participantLimit
	 */
	public function getParticipantLimit()
	{
		return $this->participantLimit;
	}
	
	/**
	 * @param boolean $displayQuickStartHost
	 */
	public function setDisplayQuickStartHost($displayQuickStartHost)
	{
		$this->displayQuickStartHost = $displayQuickStartHost;
	}
	
	/**
	 * @return boolean $displayQuickStartHost
	 */
	public function getDisplayQuickStartHost()
	{
		return $this->displayQuickStartHost;
	}
	
	/**
	 * @param boolean $voip
	 */
	public function setVoip($voip)
	{
		$this->voip = $voip;
	}
	
	/**
	 * @return boolean $voip
	 */
	public function getVoip()
	{
		return $this->voip;
	}
	
	/**
	 * @param boolean $emailInvitations
	 */
	public function setEmailInvitations($emailInvitations)
	{
		$this->emailInvitations = $emailInvitations;
	}
	
	/**
	 * @return boolean $emailInvitations
	 */
	public function getEmailInvitations()
	{
		return $this->emailInvitations;
	}
	
	/**
	 * @param boolean $registration
	 */
	public function setRegistration($registration)
	{
		$this->registration = $registration;
	}
	
	/**
	 * @return boolean $registration
	 */
	public function getRegistration()
	{
		return $this->registration;
	}
	
}
		
