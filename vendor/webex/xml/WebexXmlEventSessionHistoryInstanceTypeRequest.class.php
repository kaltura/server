<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlEventSessionHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');
require_once(__DIR__ . '/WebexXmlHistorySourceType.class.php');
require_once(__DIR__ . '/WebexXmlServMeetingAssistType.class.php');

class WebexXmlEventSessionHistoryInstanceTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
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
	protected $sessionStartTime;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionEndTime;
	
	/**
	 *
	 * @var int
	 */
	protected $duration;
	
	/**
	 *
	 * @var long
	 */
	protected $timezone;
	
	/**
	 *
	 * @var WebexXmlComTrackingType
	 */
	protected $trackingCode;
	
	/**
	 *
	 * @var string
	 */
	protected $meetingType;
	
	/**
	 *
	 * @var int
	 */
	protected $userID;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var string
	 */
	protected $hostName;
	
	/**
	 *
	 * @var string
	 */
	protected $hostEmail;
	
	/**
	 *
	 * @var int
	 */
	protected $totalPeopleMinutes;
	
	/**
	 *
	 * @var int
	 */
	protected $totalCallInMinutes;
	
	/**
	 *
	 * @var int
	 */
	protected $totalCallInTollfreeMinutes;
	
	/**
	 *
	 * @var int
	 */
	protected $totalCallOutDomestic;
	
	/**
	 *
	 * @var int
	 */
	protected $totalCallOutInternational;
	
	/**
	 *
	 * @var int
	 */
	protected $totalVoipMinutes;
	
	/**
	 *
	 * @var int
	 */
	protected $totalParticipants;
	
	/**
	 *
	 * @var int
	 */
	protected $totalParticipantsVoip;
	
	/**
	 *
	 * @var integer
	 */
	protected $totalParticipantsCallIn;
	
	/**
	 *
	 * @var integer
	 */
	protected $totalParticipantsCallOut;
	
	/**
	 *
	 * @var WebexXmlHistorySourceType
	 */
	protected $source;
	
	/**
	 *
	 * @var long
	 */
	protected $peakAttendee;
	
	/**
	 *
	 * @var WebexXmlServMeetingAssistType
	 */
	protected $assistService;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'confID',
			'sessionKey',
			'confName',
			'sessionStartTime',
			'sessionEndTime',
			'duration',
			'timezone',
			'trackingCode',
			'meetingType',
			'userID',
			'hostWebExID',
			'hostName',
			'hostEmail',
			'totalPeopleMinutes',
			'totalCallInMinutes',
			'totalCallInTollfreeMinutes',
			'totalCallOutDomestic',
			'totalCallOutInternational',
			'totalVoipMinutes',
			'totalParticipants',
			'totalParticipantsVoip',
			'totalParticipantsCallIn',
			'totalParticipantsCallOut',
			'source',
			'peakAttendee',
			'assistService',
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
		return 'history:eventSessionHistoryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlEventSessionHistoryInstanceType';
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
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
	 * @param string $sessionStartTime
	 */
	public function setSessionStartTime($sessionStartTime)
	{
		$this->sessionStartTime = $sessionStartTime;
	}
	
	/**
	 * @param string $sessionEndTime
	 */
	public function setSessionEndTime($sessionEndTime)
	{
		$this->sessionEndTime = $sessionEndTime;
	}
	
	/**
	 * @param int $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @param long $timezone
	 */
	public function setTimezone($timezone)
	{
		$this->timezone = $timezone;
	}
	
	/**
	 * @param WebexXmlComTrackingType $trackingCode
	 */
	public function setTrackingCode(WebexXmlComTrackingType $trackingCode)
	{
		$this->trackingCode = $trackingCode;
	}
	
	/**
	 * @param string $meetingType
	 */
	public function setMeetingType($meetingType)
	{
		$this->meetingType = $meetingType;
	}
	
	/**
	 * @param int $userID
	 */
	public function setUserID($userID)
	{
		$this->userID = $userID;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
	/**
	 * @param string $hostName
	 */
	public function setHostName($hostName)
	{
		$this->hostName = $hostName;
	}
	
	/**
	 * @param string $hostEmail
	 */
	public function setHostEmail($hostEmail)
	{
		$this->hostEmail = $hostEmail;
	}
	
	/**
	 * @param int $totalPeopleMinutes
	 */
	public function setTotalPeopleMinutes($totalPeopleMinutes)
	{
		$this->totalPeopleMinutes = $totalPeopleMinutes;
	}
	
	/**
	 * @param int $totalCallInMinutes
	 */
	public function setTotalCallInMinutes($totalCallInMinutes)
	{
		$this->totalCallInMinutes = $totalCallInMinutes;
	}
	
	/**
	 * @param int $totalCallInTollfreeMinutes
	 */
	public function setTotalCallInTollfreeMinutes($totalCallInTollfreeMinutes)
	{
		$this->totalCallInTollfreeMinutes = $totalCallInTollfreeMinutes;
	}
	
	/**
	 * @param int $totalCallOutDomestic
	 */
	public function setTotalCallOutDomestic($totalCallOutDomestic)
	{
		$this->totalCallOutDomestic = $totalCallOutDomestic;
	}
	
	/**
	 * @param int $totalCallOutInternational
	 */
	public function setTotalCallOutInternational($totalCallOutInternational)
	{
		$this->totalCallOutInternational = $totalCallOutInternational;
	}
	
	/**
	 * @param int $totalVoipMinutes
	 */
	public function setTotalVoipMinutes($totalVoipMinutes)
	{
		$this->totalVoipMinutes = $totalVoipMinutes;
	}
	
	/**
	 * @param int $totalParticipants
	 */
	public function setTotalParticipants($totalParticipants)
	{
		$this->totalParticipants = $totalParticipants;
	}
	
	/**
	 * @param int $totalParticipantsVoip
	 */
	public function setTotalParticipantsVoip($totalParticipantsVoip)
	{
		$this->totalParticipantsVoip = $totalParticipantsVoip;
	}
	
	/**
	 * @param integer $totalParticipantsCallIn
	 */
	public function setTotalParticipantsCallIn($totalParticipantsCallIn)
	{
		$this->totalParticipantsCallIn = $totalParticipantsCallIn;
	}
	
	/**
	 * @param integer $totalParticipantsCallOut
	 */
	public function setTotalParticipantsCallOut($totalParticipantsCallOut)
	{
		$this->totalParticipantsCallOut = $totalParticipantsCallOut;
	}
	
	/**
	 * @param WebexXmlHistorySourceType $source
	 */
	public function setSource(WebexXmlHistorySourceType $source)
	{
		$this->source = $source;
	}
	
	/**
	 * @param long $peakAttendee
	 */
	public function setPeakAttendee($peakAttendee)
	{
		$this->peakAttendee = $peakAttendee;
	}
	
	/**
	 * @param WebexXmlServMeetingAssistType $assistService
	 */
	public function setAssistService(WebexXmlServMeetingAssistType $assistService)
	{
		$this->assistService = $assistService;
	}
	
}

