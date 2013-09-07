<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSupportAttendeeHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryParticipantTypeType.class.php');

class WebexXmlSupportAttendeeHistoryInstanceTypeRequest extends WebexXmlRequestBodyContent
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
	protected $company;
	
	/**
	 *
	 * @var string
	 */
	protected $phone;
	
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
			'attendeeName',
			'attendeeEmail',
			'startTime',
			'endTime',
			'duration',
			'company',
			'phone',
			'ipAddress',
			'participantType',
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
		return 'history:supportAttendeeHistoryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSupportAttendeeHistoryInstanceType';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
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
	 * @param string $company
	 */
	public function setCompany($company)
	{
		$this->company = $company;
	}
	
	/**
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
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

