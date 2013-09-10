<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlRegisterAttendeeType.class.php');
require_once(__DIR__ . '/WebexXmlComPersonType.class.php');
require_once(__DIR__ . '/WebexXmlAttJoinStatusType.class.php');
require_once(__DIR__ . '/WebexXmlAttRoleType.class.php');

class WebexXmlRegisterAttendeeTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlComPersonType
	 */
	protected $person;
	
	/**
	 *
	 * @var WebexXmlAttJoinStatusType
	 */
	protected $joinStatus;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var WebexXmlAttRoleType
	 */
	protected $role;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $emailInvitations;
	
	/**
	 *
	 * @var string
	 */
	protected $language;
	
	/**
	 *
	 * @var string
	 */
	protected $locale;
	
	/**
	 *
	 * @var long
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var int
	 */
	protected $sessionNum;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'person',
			'joinStatus',
			'sessionKey',
			'role',
			'confID',
			'emailInvitations',
			'language',
			'locale',
			'timeZoneID',
			'sessionNum',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'person',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'attendee';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'attendee:registerAttendeeType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlRegisterAttendeeType';
	}
	
	/**
	 * @param WebexXmlComPersonType $person
	 */
	public function setPerson(WebexXmlComPersonType $person)
	{
		$this->person = $person;
	}
	
	/**
	 * @param WebexXmlAttJoinStatusType $joinStatus
	 */
	public function setJoinStatus(WebexXmlAttJoinStatusType $joinStatus)
	{
		$this->joinStatus = $joinStatus;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param WebexXmlAttRoleType $role
	 */
	public function setRole(WebexXmlAttRoleType $role)
	{
		$this->role = $role;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @param boolean $emailInvitations
	 */
	public function setEmailInvitations($emailInvitations)
	{
		$this->emailInvitations = $emailInvitations;
	}
	
	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}
	
	/**
	 * @param string $locale
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;
	}
	
	/**
	 * @param long $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @param int $sessionNum
	 */
	public function setSessionNum($sessionNum)
	{
		$this->sessionNum = $sessionNum;
	}
	
}
		
