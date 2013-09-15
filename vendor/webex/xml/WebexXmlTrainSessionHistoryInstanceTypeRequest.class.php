<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlTrainSessionHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');
require_once(__DIR__ . '/WebexXmlComPsoFieldsType.class.php');
require_once(__DIR__ . '/WebexXmlServMeetingAssistType.class.php');

class WebexXmlTrainSessionHistoryInstanceTypeRequest extends WebexXmlRequestBodyContent
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
	protected $sessionStartTime;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionEndTime;
	
	/**
	 *
	 * @var long
	 */
	protected $duration;
	
	/**
	 *
	 * @var long
	 */
	protected $totalAttendee;
	
	/**
	 *
	 * @var long
	 */
	protected $totalRegistered;
	
	/**
	 *
	 * @var long
	 */
	protected $totalInvited;
	
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
	protected $totalCallInTolllfreeMinutes;
	
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
	 * @var integer
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
	 * @var WebexXmlComPsoFieldsType
	 */
	protected $psoFields;
	
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
			'sessionStartTime',
			'sessionEndTime',
			'duration',
			'totalAttendee',
			'totalRegistered',
			'totalInvited',
			'timezone',
			'trackingCode',
			'userID',
			'hostWebExID',
			'hostEmail',
			'totalPeopleMinutes',
			'totalCallInMinutes',
			'totalCallInTolllfreeMinutes',
			'totalCallOutDomestic',
			'totalCallOutInternational',
			'totalVoipMinutes',
			'totalParticipants',
			'totalParticipantsVoip',
			'totalParticipantsCallIn',
			'totalParticipantsCallOut',
			'confID',
			'peakAttendee',
			'psoFields',
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
		return 'history:trainSessionHistoryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlTrainSessionHistoryInstanceType';
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
	 * @param long $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @param long $totalAttendee
	 */
	public function setTotalAttendee($totalAttendee)
	{
		$this->totalAttendee = $totalAttendee;
	}
	
	/**
	 * @param long $totalRegistered
	 */
	public function setTotalRegistered($totalRegistered)
	{
		$this->totalRegistered = $totalRegistered;
	}
	
	/**
	 * @param long $totalInvited
	 */
	public function setTotalInvited($totalInvited)
	{
		$this->totalInvited = $totalInvited;
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
	 * @param int $totalCallInTolllfreeMinutes
	 */
	public function setTotalCallInTolllfreeMinutes($totalCallInTolllfreeMinutes)
	{
		$this->totalCallInTolllfreeMinutes = $totalCallInTolllfreeMinutes;
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
	 * @param integer $totalParticipantsVoip
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
	 * @param WebexXmlComPsoFieldsType $psoFields
	 */
	public function setPsoFields(WebexXmlComPsoFieldsType $psoFields)
	{
		$this->psoFields = $psoFields;
	}
	
	/**
	 * @param WebexXmlServMeetingAssistType $assistService
	 */
	public function setAssistService(WebexXmlServMeetingAssistType $assistService)
	{
		$this->assistService = $assistService;
	}
	
}

