<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlTrainingSessionSummaryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlSessListingType.class.php');

class WebexXmlTrainingSessionSummaryInstanceTypeRequest extends WebexXmlRequestBodyContent
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
	 * @var long
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
	protected $otherHostWebExID;
	
	/**
	 *
	 * @var long
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
	protected $startDate;
	
	/**
	 *
	 * @var integer
	 */
	protected $duration;
	
	/**
	 *
	 * @var WebexXmlSessListingType
	 */
	protected $listStatus;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'confName',
			'meetingType',
			'hostWebExID',
			'otherHostWebExID',
			'timeZoneID',
			'timeZone',
			'status',
			'startDate',
			'duration',
			'listStatus',
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
		return 'trainingsession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'trainingsession:trainingSessionSummaryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlTrainingSessionSummaryInstanceType';
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
	 * @param long $meetingType
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
	 * @param string $otherHostWebExID
	 */
	public function setOtherHostWebExID($otherHostWebExID)
	{
		$this->otherHostWebExID = $otherHostWebExID;
	}
	
	/**
	 * @param long $timeZoneID
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
	 * @param string $startDate
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}
	
	/**
	 * @param integer $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @param WebexXmlSessListingType $listStatus
	 */
	public function setListStatus(WebexXmlSessListingType $listStatus)
	{
		$this->listStatus = $listStatus;
	}
	
}
		
