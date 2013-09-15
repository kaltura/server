<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlMeetingSummaryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlComListingType.class.php');

class WebexXmlMeetingSummaryInstanceTypeRequest extends WebexXmlRequestBodyContent
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
	 * @var WebexXmlComListingType
	 */
	protected $listStatus;
	
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
	 * @var boolean
	 */
	protected $telePresence;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingKey',
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
			'hostJoined',
			'participantsJoined',
			'telePresence',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'meetingKey',
			'confName',
			'meetingType',
			'startDate',
			'duration',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'meeting';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'meeting:meetingSummaryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlMeetingSummaryInstanceType';
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
	 * @param WebexXmlComListingType $listStatus
	 */
	public function setListStatus(WebexXmlComListingType $listStatus)
	{
		$this->listStatus = $listStatus;
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
	 * @param boolean $telePresence
	 */
	public function setTelePresence($telePresence)
	{
		$this->telePresence = $telePresence;
	}
	
}
		
