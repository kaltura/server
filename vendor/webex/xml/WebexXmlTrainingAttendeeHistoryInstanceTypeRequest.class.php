<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlTrainingAttendeeHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryParticipantTypeType.class.php');

class WebexXmlTrainingAttendeeHistoryInstanceTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var string
	 */
	protected $confName;
	
	/**
	 *
	 * @var string
	 */
	protected $attendeeName;
	
	/**
	 *
	 * @var string
	 */
	protected $attendeeEmail;
	
	/**
	 *
	 * @var string
	 */
	protected $startTime;
	
	/**
	 *
	 * @var string
	 */
	protected $endTime;
	
	/**
	 *
	 * @var int
	 */
	protected $duration;
	
	/**
	 *
	 * @var string
	 */
	protected $registered;
	
	/**
	 *
	 * @var string
	 */
	protected $invited;
	
	/**
	 *
	 * @var string
	 */
	protected $company;
	
	/**
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 *
	 * @var string
	 */
	protected $phone;
	
	/**
	 *
	 * @var string
	 */
	protected $address1;
	
	/**
	 *
	 * @var string
	 */
	protected $address2;
	
	/**
	 *
	 * @var string
	 */
	protected $city;
	
	/**
	 *
	 * @var string
	 */
	protected $state;
	
	/**
	 *
	 * @var string
	 */
	protected $country;
	
	/**
	 *
	 * @var string
	 */
	protected $zip;
	
	/**
	 *
	 * @var string
	 */
	protected $ipAddress;
	
	/**
	 *
	 * @var WebexXmlHistoryParticipantTypeType
	 */
	protected $participantType;
	
	/**
	 *
	 * @var int
	 */
	protected $voipDuration;
	
	/**
	 *
	 * @var string
	 */
	protected $clientAgent;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'confName',
			'attendeeName',
			'attendeeEmail',
			'startTime',
			'endTime',
			'duration',
			'registered',
			'invited',
			'company',
			'title',
			'phone',
			'address1',
			'address2',
			'city',
			'state',
			'country',
			'zip',
			'ipAddress',
			'participantType',
			'voipDuration',
			'clientAgent',
			'confID',
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
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'history';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'history:trainingAttendeeHistoryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlTrainingAttendeeHistoryInstanceType';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param string $confName
	 */
	public function setConfName($confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @param string $attendeeName
	 */
	public function setAttendeeName($attendeeName)
	{
		$this->attendeeName = $attendeeName;
	}
	
	/**
	 * @param string $attendeeEmail
	 */
	public function setAttendeeEmail($attendeeEmail)
	{
		$this->attendeeEmail = $attendeeEmail;
	}
	
	/**
	 * @param string $startTime
	 */
	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
	}
	
	/**
	 * @param string $endTime
	 */
	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
	}
	
	/**
	 * @param int $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @param string $registered
	 */
	public function setRegistered($registered)
	{
		$this->registered = $registered;
	}
	
	/**
	 * @param string $invited
	 */
	public function setInvited($invited)
	{
		$this->invited = $invited;
	}
	
	/**
	 * @param string $company
	 */
	public function setCompany($company)
	{
		$this->company = $company;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}
	
	/**
	 * @param string $address1
	 */
	public function setAddress1($address1)
	{
		$this->address1 = $address1;
	}
	
	/**
	 * @param string $address2
	 */
	public function setAddress2($address2)
	{
		$this->address2 = $address2;
	}
	
	/**
	 * @param string $city
	 */
	public function setCity($city)
	{
		$this->city = $city;
	}
	
	/**
	 * @param string $state
	 */
	public function setState($state)
	{
		$this->state = $state;
	}
	
	/**
	 * @param string $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}
	
	/**
	 * @param string $zip
	 */
	public function setZip($zip)
	{
		$this->zip = $zip;
	}
	
	/**
	 * @param string $ipAddress
	 */
	public function setIpAddress($ipAddress)
	{
		$this->ipAddress = $ipAddress;
	}
	
	/**
	 * @param WebexXmlHistoryParticipantTypeType $participantType
	 */
	public function setParticipantType(WebexXmlHistoryParticipantTypeType $participantType)
	{
		$this->participantType = $participantType;
	}
	
	/**
	 * @param int $voipDuration
	 */
	public function setVoipDuration($voipDuration)
	{
		$this->voipDuration = $voipDuration;
	}
	
	/**
	 * @param string $clientAgent
	 */
	public function setClientAgent($clientAgent)
	{
		$this->clientAgent = $clientAgent;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
}

