<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlMeetingAttendeeHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryParticipantTypeType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryExternalParticipantType.class.php');

class WebexXmlMeetingAttendeeHistoryInstanceTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $meetingKey;
	
	/**
	 *
	 * @var string
	 */
	protected $confName;
	
	/**
	 *
	 * @var string
	 */
	protected $ipAddress;
	
	/**
	 *
	 * @var string
	 */
	protected $clientAgent;
	
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
	protected $phoneNumber;
	
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
	protected $zipCode;
	
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var string
	 */
	protected $email;
	
	/**
	 *
	 * @var string
	 */
	protected $joinTime;
	
	/**
	 *
	 * @var string
	 */
	protected $leaveTime;
	
	/**
	 *
	 * @var string
	 */
	protected $duration;
	
	/**
	 *
	 * @var WebexXmlHistoryParticipantTypeType
	 */
	protected $participantType;
	
	/**
	 *
	 * @var integer
	 */
	protected $voipDuration;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var WebexXmlHistoryExternalParticipantType
	 */
	protected $externalParticipant;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingKey',
			'confName',
			'ipAddress',
			'clientAgent',
			'company',
			'title',
			'phoneNumber',
			'address1',
			'address2',
			'city',
			'state',
			'country',
			'zipCode',
			'name',
			'email',
			'joinTime',
			'leaveTime',
			'duration',
			'participantType',
			'voipDuration',
			'confID',
			'externalParticipant',
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
		return 'history:meetingAttendeeHistoryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlMeetingAttendeeHistoryInstanceType';
	}
	
	/**
	 * @param long $meetingKey
	 */
	public function setMeetingKey($meetingKey)
	{
		$this->meetingKey = $meetingKey;
	}
	
	/**
	 * @param string $confName
	 */
	public function setConfName($confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @param string $ipAddress
	 */
	public function setIpAddress($ipAddress)
	{
		$this->ipAddress = $ipAddress;
	}
	
	/**
	 * @param string $clientAgent
	 */
	public function setClientAgent($clientAgent)
	{
		$this->clientAgent = $clientAgent;
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
	 * @param string $phoneNumber
	 */
	public function setPhoneNumber($phoneNumber)
	{
		$this->phoneNumber = $phoneNumber;
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
	 * @param string $zipCode
	 */
	public function setZipCode($zipCode)
	{
		$this->zipCode = $zipCode;
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	/**
	 * @param string $joinTime
	 */
	public function setJoinTime($joinTime)
	{
		$this->joinTime = $joinTime;
	}
	
	/**
	 * @param string $leaveTime
	 */
	public function setLeaveTime($leaveTime)
	{
		$this->leaveTime = $leaveTime;
	}
	
	/**
	 * @param string $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @param WebexXmlHistoryParticipantTypeType $participantType
	 */
	public function setParticipantType(WebexXmlHistoryParticipantTypeType $participantType)
	{
		$this->participantType = $participantType;
	}
	
	/**
	 * @param integer $voipDuration
	 */
	public function setVoipDuration($voipDuration)
	{
		$this->voipDuration = $voipDuration;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @param WebexXmlHistoryExternalParticipantType $externalParticipant
	 */
	public function setExternalParticipant(WebexXmlHistoryExternalParticipantType $externalParticipant)
	{
		$this->externalParticipant = $externalParticipant;
	}
	
}

