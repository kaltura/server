<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingAttendeeOptionsType extends WebexXmlRequestType
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
	 * @var boolean
	 */
	protected $emailInvitations;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $participantLimit;
	
	/**
	 *
	 * @var boolean
	 */
	protected $excludePassword;
	
	/**
	 *
	 * @var boolean
	 */
	protected $joinRequiresAccount;
	
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
	
			case 'emailInvitations':
				return 'boolean';
	
			case 'participantLimit':
				return 'WebexXml';
	
			case 'excludePassword':
				return 'boolean';
	
			case 'joinRequiresAccount':
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
			'request',
			'registration',
			'auto',
			'emailInvitations',
			'participantLimit',
			'excludePassword',
			'joinRequiresAccount',
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
	
	/**
	 * @param boolean $excludePassword
	 */
	public function setExcludePassword($excludePassword)
	{
		$this->excludePassword = $excludePassword;
	}
	
	/**
	 * @return boolean $excludePassword
	 */
	public function getExcludePassword()
	{
		return $this->excludePassword;
	}
	
	/**
	 * @param boolean $joinRequiresAccount
	 */
	public function setJoinRequiresAccount($joinRequiresAccount)
	{
		$this->joinRequiresAccount = $joinRequiresAccount;
	}
	
	/**
	 * @return boolean $joinRequiresAccount
	 */
	public function getJoinRequiresAccount()
	{
		return $this->joinRequiresAccount;
	}
	
}
		
