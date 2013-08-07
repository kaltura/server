<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionAttendeeOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $request;
	
	/**
	 *
	 * @var boolean
	 */
	protected $registration;
	
	/**
	 *
	 * @var boolean
	 */
	protected $auto;
	
	/**
	 *
	 * @var string
	 */
	protected $registrationPWD;
	
	/**
	 *
	 * @var long
	 */
	protected $maxRegistrations;
	
	/**
	 *
	 * @var string
	 */
	protected $registrationCloseDate;
	
	/**
	 *
	 * @var boolean
	 */
	protected $emailInvitations;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $participantLimit;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'request':
				return 'boolean';
	
			case 'registration':
				return 'boolean';
	
			case 'auto':
				return 'boolean';
	
			case 'registrationPWD':
				return 'string';
	
			case 'maxRegistrations':
				return 'long';
	
			case 'registrationCloseDate':
				return 'string';
	
			case 'emailInvitations':
				return 'boolean';
	
			case 'participantLimit':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'request',
			'registration',
			'auto',
			'registrationPWD',
			'maxRegistrations',
			'registrationCloseDate',
			'emailInvitations',
			'participantLimit',
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
		return 'attendeeOptionsType';
	}
	
	/**
	 * @param boolean $request
	 */
	public function setRequest($request)
	{
		$this->request = $request;
	}
	
	/**
	 * @return boolean $request
	 */
	public function getRequest()
	{
		return $this->request;
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
	
	/**
	 * @param boolean $auto
	 */
	public function setAuto($auto)
	{
		$this->auto = $auto;
	}
	
	/**
	 * @return boolean $auto
	 */
	public function getAuto()
	{
		return $this->auto;
	}
	
	/**
	 * @param string $registrationPWD
	 */
	public function setRegistrationPWD($registrationPWD)
	{
		$this->registrationPWD = $registrationPWD;
	}
	
	/**
	 * @return string $registrationPWD
	 */
	public function getRegistrationPWD()
	{
		return $this->registrationPWD;
	}
	
	/**
	 * @param long $maxRegistrations
	 */
	public function setMaxRegistrations($maxRegistrations)
	{
		$this->maxRegistrations = $maxRegistrations;
	}
	
	/**
	 * @return long $maxRegistrations
	 */
	public function getMaxRegistrations()
	{
		return $this->maxRegistrations;
	}
	
	/**
	 * @param string $registrationCloseDate
	 */
	public function setRegistrationCloseDate($registrationCloseDate)
	{
		$this->registrationCloseDate = $registrationCloseDate;
	}
	
	/**
	 * @return string $registrationCloseDate
	 */
	public function getRegistrationCloseDate()
	{
		return $this->registrationCloseDate;
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
	
}

