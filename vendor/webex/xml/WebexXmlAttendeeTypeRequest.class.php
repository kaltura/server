<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlAttendeeType.class.php');
require_once(__DIR__ . '/WebexXmlComPersonType.class.php');
require_once(__DIR__ . '/WebexXmlAttJoinStatusType.class.php');
require_once(__DIR__ . '/WebexXmlAttRoleType.class.php');

class WebexXmlAttendeeTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlComPersonType
	 */
	protected $person;
	
	/**
	 *
	 * @var long
	 */
	protected $contactID;
	
	/**
	 *
	 * @var WebexXmlAttJoinStatusType
	 */
	protected $joinStatus;
	
	/**
	 *
	 * @var long
	 */
	protected $meetingKey;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var string
	 */
	protected $language;
	
	/**
	 *
	 * @var WebexXmlAttRoleType
	 */
	protected $role;
	
	/**
	 *
	 * @var boolean
	 */
	protected $emailInvitations;
	
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
	 * @var long
	 */
	protected $languageID;
	
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
			'contactID',
			'joinStatus',
			'meetingKey',
			'sessionKey',
			'language',
			'role',
			'emailInvitations',
			'locale',
			'timeZoneID',
			'languageID',
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
		return 'attendee:attendeeType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlAttendeeType';
	}
	
	/**
	 * @param WebexXmlComPersonType $person
	 */
	public function setPerson(WebexXmlComPersonType $person)
	{
		$this->person = $person;
	}
	
	/**
	 * @param long $contactID
	 */
	public function setContactID($contactID)
	{
		$this->contactID = $contactID;
	}
	
	/**
	 * @param WebexXmlAttJoinStatusType $joinStatus
	 */
	public function setJoinStatus(WebexXmlAttJoinStatusType $joinStatus)
	{
		$this->joinStatus = $joinStatus;
	}
	
	/**
	 * @param long $meetingKey
	 */
	public function setMeetingKey($meetingKey)
	{
		$this->meetingKey = $meetingKey;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}
	
	/**
	 * @param WebexXmlAttRoleType $role
	 */
	public function setRole(WebexXmlAttRoleType $role)
	{
		$this->role = $role;
	}
	
	/**
	 * @param boolean $emailInvitations
	 */
	public function setEmailInvitations($emailInvitations)
	{
		$this->emailInvitations = $emailInvitations;
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
	 * @param long $languageID
	 */
	public function setLanguageID($languageID)
	{
		$this->languageID = $languageID;
	}
	
	/**
	 * @param int $sessionNum
	 */
	public function setSessionNum($sessionNum)
	{
		$this->sessionNum = $sessionNum;
	}
	
}

