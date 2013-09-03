<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSupportSessionHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');

class WebexXmlSupportSessionHistoryInstanceTypeRequest extends WebexXmlRequestBodyContent
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
	 * @var int
	 */
	protected $totalPeopleMinutes;
	
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
	 * @var WebexXmlComTrackingType
	 */
	protected $trackingCode;
	
	
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
			'timezone',
			'meetingType',
			'hostWebExID',
			'hostName',
			'hostEmail',
			'totalPeopleMinutes',
			'confID',
			'peakAttendee',
			'trackingCode',
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
		return 'history:supportSessionHistoryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSupportSessionHistoryInstanceType';
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
	 * @param int $totalPeopleMinutes
	 */
	public function setTotalPeopleMinutes($totalPeopleMinutes)
	{
		$this->totalPeopleMinutes = $totalPeopleMinutes;
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
	 * @param WebexXmlComTrackingType $trackingCode
	 */
	public function setTrackingCode(WebexXmlComTrackingType $trackingCode)
	{
		$this->trackingCode = $trackingCode;
	}
	
}

