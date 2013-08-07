<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSessionSummaryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlComServiceTypeType.class.php');
require_once(__DIR__ . '/WebexXmlComListingType.class.php');
require_once(__DIR__ . '/WebexXmlComPsoFieldsType.class.php');
require_once(__DIR__ . '/WebexXmlServMeetingAssistType.class.php');

class WebexXmlSessionSummaryInstanceTypeRequest extends WebexXmlRequestBodyContent
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
	 * @var integer
	 */
	protected $sessionType;
	
	/**
	 *
	 * @var WebexXmlComServiceTypeType
	 */
	protected $serviceType;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var string
	 */
	protected $hostFirstName;
	
	/**
	 *
	 * @var string
	 */
	protected $hostLastName;
	
	/**
	 *
	 * @var string
	 */
	protected $otherHostWebExID;
	
	/**
	 *
	 * @var integer
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var string
	 */
	protected $timeZone;
	
	/**
	 *
	 * @var string
	 */
	protected $status;
	
	/**
	 *
	 * @var string
	 */
	protected $startTime;
	
	/**
	 *
	 * @var string
	 */
	protected $actualStartTime;
	
	/**
	 *
	 * @var int
	 */
	protected $openTime;
	
	/**
	 *
	 * @var integer
	 */
	protected $duration;
	
	/**
	 *
	 * @var WebexXmlComListingType
	 */
	protected $listStatus;
	
	/**
	 *
	 * @var string
	 */
	protected $hostEmail;
	
	/**
	 *
	 * @var boolean
	 */
	protected $passwordReq;
	
	/**
	 *
	 * @var boolean
	 */
	protected $hostJoined;
	
	/**
	 *
	 * @var boolean
	 */
	protected $participantsJoined;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $registration;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isRecurring;
	
	/**
	 *
	 * @var boolean
	 */
	protected $altHost;
	
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
	
	/**
	 *
	 * @var string
	 */
	protected $hostType;
	
	/**
	 *
	 * @var string
	 */
	protected $audioStatus;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isAudioOnly;
	
	/**
	 *
	 * @var boolean
	 */
	protected $telePresence;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isTCSingleRecurrence;
	
	/**
	 *
	 * @var long
	 */
	protected $subSessionNo;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'confName',
			'sessionType',
			'serviceType',
			'hostWebExID',
			'hostFirstName',
			'hostLastName',
			'otherHostWebExID',
			'timeZoneID',
			'timeZone',
			'status',
			'startTime',
			'actualStartTime',
			'openTime',
			'duration',
			'listStatus',
			'hostEmail',
			'passwordReq',
			'hostJoined',
			'participantsJoined',
			'confID',
			'registration',
			'isRecurring',
			'altHost',
			'psoFields',
			'assistService',
			'hostType',
			'audioStatus',
			'isAudioOnly',
			'telePresence',
			'isTCSingleRecurrence',
			'subSessionNo',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'sessionKey',
			'confName',
			'sessionType',
			'serviceType',
			'hostWebExID',
			'timeZoneID',
			'startTime',
			'duration',
			'listStatus',
			'confID',
			'isRecurring',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'ep';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'ep:sessionSummaryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSessionSummaryInstanceType';
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
	 * @param integer $sessionType
	 */
	public function setSessionType($sessionType)
	{
		$this->sessionType = $sessionType;
	}
	
	/**
	 * @param WebexXmlComServiceTypeType $serviceType
	 */
	public function setServiceType(WebexXmlComServiceTypeType $serviceType)
	{
		$this->serviceType = $serviceType;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
	/**
	 * @param string $hostFirstName
	 */
	public function setHostFirstName($hostFirstName)
	{
		$this->hostFirstName = $hostFirstName;
	}
	
	/**
	 * @param string $hostLastName
	 */
	public function setHostLastName($hostLastName)
	{
		$this->hostLastName = $hostLastName;
	}
	
	/**
	 * @param string $otherHostWebExID
	 */
	public function setOtherHostWebExID($otherHostWebExID)
	{
		$this->otherHostWebExID = $otherHostWebExID;
	}
	
	/**
	 * @param integer $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @param string $timeZone
	 */
	public function setTimeZone($timeZone)
	{
		$this->timeZone = $timeZone;
	}
	
	/**
	 * @param string $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	/**
	 * @param string $startTime
	 */
	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
	}
	
	/**
	 * @param string $actualStartTime
	 */
	public function setActualStartTime($actualStartTime)
	{
		$this->actualStartTime = $actualStartTime;
	}
	
	/**
	 * @param int $openTime
	 */
	public function setOpenTime($openTime)
	{
		$this->openTime = $openTime;
	}
	
	/**
	 * @param integer $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @param WebexXmlComListingType $listStatus
	 */
	public function setListStatus(WebexXmlComListingType $listStatus)
	{
		$this->listStatus = $listStatus;
	}
	
	/**
	 * @param string $hostEmail
	 */
	public function setHostEmail($hostEmail)
	{
		$this->hostEmail = $hostEmail;
	}
	
	/**
	 * @param boolean $passwordReq
	 */
	public function setPasswordReq($passwordReq)
	{
		$this->passwordReq = $passwordReq;
	}
	
	/**
	 * @param boolean $hostJoined
	 */
	public function setHostJoined($hostJoined)
	{
		$this->hostJoined = $hostJoined;
	}
	
	/**
	 * @param boolean $participantsJoined
	 */
	public function setParticipantsJoined($participantsJoined)
	{
		$this->participantsJoined = $participantsJoined;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @param boolean $registration
	 */
	public function setRegistration($registration)
	{
		$this->registration = $registration;
	}
	
	/**
	 * @param boolean $isRecurring
	 */
	public function setIsRecurring($isRecurring)
	{
		$this->isRecurring = $isRecurring;
	}
	
	/**
	 * @param boolean $altHost
	 */
	public function setAltHost($altHost)
	{
		$this->altHost = $altHost;
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
	
	/**
	 * @param string $hostType
	 */
	public function setHostType($hostType)
	{
		$this->hostType = $hostType;
	}
	
	/**
	 * @param string $audioStatus
	 */
	public function setAudioStatus($audioStatus)
	{
		$this->audioStatus = $audioStatus;
	}
	
	/**
	 * @param boolean $isAudioOnly
	 */
	public function setIsAudioOnly($isAudioOnly)
	{
		$this->isAudioOnly = $isAudioOnly;
	}
	
	/**
	 * @param boolean $telePresence
	 */
	public function setTelePresence($telePresence)
	{
		$this->telePresence = $telePresence;
	}
	
	/**
	 * @param boolean $isTCSingleRecurrence
	 */
	public function setIsTCSingleRecurrence($isTCSingleRecurrence)
	{
		$this->isTCSingleRecurrence = $isTCSingleRecurrence;
	}
	
	/**
	 * @param long $subSessionNo
	 */
	public function setSubSessionNo($subSessionNo)
	{
		$this->subSessionNo = $subSessionNo;
	}
	
}
		
