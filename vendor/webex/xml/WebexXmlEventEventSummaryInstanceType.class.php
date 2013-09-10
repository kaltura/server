<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventEventSummaryInstanceType extends WebexXmlRequestType
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
	protected $sessionName;
	
	/**
	 *
	 * @var integer
	 */
	protected $sessionType;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var string
	 */
	protected $startDate;
	
	/**
	 *
	 * @var string
	 */
	protected $endDate;
	
	/**
	 *
	 * @var integer
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var integer
	 */
	protected $duration;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var string
	 */
	protected $status;
	
	/**
	 *
	 * @var string
	 */
	protected $panelists;
	
	/**
	 *
	 * @var WebexXmlEventListingType
	 */
	protected $listStatus;
	
	/**
	 *
	 * @var WebexXmlEventAttendeeCountType
	 */
	protected $attendeeCount;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sessionKey':
				return 'long';
	
			case 'sessionName':
				return 'string';
	
			case 'sessionType':
				return 'integer';
	
			case 'hostWebExID':
				return 'string';
	
			case 'startDate':
				return 'string';
	
			case 'endDate':
				return 'string';
	
			case 'timeZoneID':
				return 'integer';
	
			case 'duration':
				return 'integer';
	
			case 'description':
				return 'string';
	
			case 'status':
				return 'string';
	
			case 'panelists':
				return 'string';
	
			case 'listStatus':
				return 'WebexXmlEventListingType';
	
			case 'attendeeCount':
				return 'WebexXmlEventAttendeeCountType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'sessionName',
			'sessionType',
			'hostWebExID',
			'startDate',
			'endDate',
			'timeZoneID',
			'duration',
			'description',
			'status',
			'panelists',
			'listStatus',
			'attendeeCount',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'sessionKey',
			'sessionName',
			'sessionType',
			'hostWebExID',
			'duration',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'eventSummaryInstanceType';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @return long $sessionKey
	 */
	public function getSessionKey()
	{
		return $this->sessionKey;
	}
	
	/**
	 * @param string $sessionName
	 */
	public function setSessionName($sessionName)
	{
		$this->sessionName = $sessionName;
	}
	
	/**
	 * @return string $sessionName
	 */
	public function getSessionName()
	{
		return $this->sessionName;
	}
	
	/**
	 * @param integer $sessionType
	 */
	public function setSessionType($sessionType)
	{
		$this->sessionType = $sessionType;
	}
	
	/**
	 * @return integer $sessionType
	 */
	public function getSessionType()
	{
		return $this->sessionType;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
	/**
	 * @return string $hostWebExID
	 */
	public function getHostWebExID()
	{
		return $this->hostWebExID;
	}
	
	/**
	 * @param string $startDate
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}
	
	/**
	 * @return string $startDate
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}
	
	/**
	 * @param string $endDate
	 */
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
	}
	
	/**
	 * @return string $endDate
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}
	
	/**
	 * @param integer $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return integer $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
	/**
	 * @param integer $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @return integer $duration
	 */
	public function getDuration()
	{
		return $this->duration;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return string $description
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @param string $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	/**
	 * @return string $status
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
	/**
	 * @param string $panelists
	 */
	public function setPanelists($panelists)
	{
		$this->panelists = $panelists;
	}
	
	/**
	 * @return string $panelists
	 */
	public function getPanelists()
	{
		return $this->panelists;
	}
	
	/**
	 * @param WebexXmlEventListingType $listStatus
	 */
	public function setListStatus(WebexXmlEventListingType $listStatus)
	{
		$this->listStatus = $listStatus;
	}
	
	/**
	 * @return WebexXmlEventListingType $listStatus
	 */
	public function getListStatus()
	{
		return $this->listStatus;
	}
	
	/**
	 * @param WebexXmlEventAttendeeCountType $attendeeCount
	 */
	public function setAttendeeCount(WebexXmlEventAttendeeCountType $attendeeCount)
	{
		$this->attendeeCount = $attendeeCount;
	}
	
	/**
	 * @return WebexXmlEventAttendeeCountType $attendeeCount
	 */
	public function getAttendeeCount()
	{
		return $this->attendeeCount;
	}
	
}
		
