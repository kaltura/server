<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlMeetingUsageHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlComTimeZoneType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');
require_once(__DIR__ . '/WebexXmlServMeetingAssistType.class.php');

class WebexXmlMeetingUsageHistoryInstanceTypeRequest extends WebexXmlRequestBodyContent
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
	protected $meetingStartTime;
	
	/**
	 *
	 * @var string
	 */
	protected $meetingEndTime;
	
	/**
	 *
	 * @var long
	 */
	protected $duration;
	
	/**
	 *
	 * @var WebexXmlComTimeZoneType
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
	 * @var long
	 */
	protected $totalCallInMinutes;
	
	/**
	 *
	 * @var long
	 */
	protected $totalPeopleMinutes;
	
	/**
	 *
	 * @var long
	 */
	protected $totalCallInTollfreeMinutes;
	
	/**
	 *
	 * @var long
	 */
	protected $totalCallOutDomestic;
	
	/**
	 *
	 * @var long
	 */
	protected $totalCallOutInternational;
	
	/**
	 *
	 * @var long
	 */
	protected $totalVoipMinutes;
	
	/**
	 *
	 * @var integer
	 */
	protected $userID;
	
	/**
	 *
	 * @var integer
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
	 * @var long
	 */
	protected $confID;
	
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
			'sessionKey',
			'confName',
			'meetingStartTime',
			'meetingEndTime',
			'duration',
			'timezone',
			'trackingCode',
			'meetingType',
			'hostWebExID',
			'hostName',
			'hostEmail',
			'totalCallInMinutes',
			'totalPeopleMinutes',
			'totalCallInTollfreeMinutes',
			'totalCallOutDomestic',
			'totalCallOutInternational',
			'totalVoipMinutes',
			'userID',
			'totalParticipants',
			'totalParticipantsVoip',
			'totalParticipantsCallIn',
			'totalParticipantsCallOut',
			'confID',
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
		return 'history:meetingUsageHistoryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlMeetingUsageHistoryInstanceType';
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
	 * @param string $meetingStartTime
	 */
	public function setMeetingStartTime($meetingStartTime)
	{
		$this->meetingStartTime = $meetingStartTime;
	}
	
	/**
	 * @param string $meetingEndTime
	 */
	public function setMeetingEndTime($meetingEndTime)
	{
		$this->meetingEndTime = $meetingEndTime;
	}
	
	/**
	 * @param long $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @param WebexXmlComTimeZoneType $timezone
	 */
	public function setTimezone(WebexXmlComTimeZoneType $timezone)
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
	 * @param long $totalCallInMinutes
	 */
	public function setTotalCallInMinutes($totalCallInMinutes)
	{
		$this->totalCallInMinutes = $totalCallInMinutes;
	}
	
	/**
	 * @param long $totalPeopleMinutes
	 */
	public function setTotalPeopleMinutes($totalPeopleMinutes)
	{
		$this->totalPeopleMinutes = $totalPeopleMinutes;
	}
	
	/**
	 * @param long $totalCallInTollfreeMinutes
	 */
	public function setTotalCallInTollfreeMinutes($totalCallInTollfreeMinutes)
	{
		$this->totalCallInTollfreeMinutes = $totalCallInTollfreeMinutes;
	}
	
	/**
	 * @param long $totalCallOutDomestic
	 */
	public function setTotalCallOutDomestic($totalCallOutDomestic)
	{
		$this->totalCallOutDomestic = $totalCallOutDomestic;
	}
	
	/**
	 * @param long $totalCallOutInternational
	 */
	public function setTotalCallOutInternational($totalCallOutInternational)
	{
		$this->totalCallOutInternational = $totalCallOutInternational;
	}
	
	/**
	 * @param long $totalVoipMinutes
	 */
	public function setTotalVoipMinutes($totalVoipMinutes)
	{
		$this->totalVoipMinutes = $totalVoipMinutes;
	}
	
	/**
	 * @param integer $userID
	 */
	public function setUserID($userID)
	{
		$this->userID = $userID;
	}
	
	/**
	 * @param integer $totalParticipants
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
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
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

