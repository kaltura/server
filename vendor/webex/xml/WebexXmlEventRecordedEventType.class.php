<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventRecordedEventType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $eventName;
	
	/**
	 *
	 * @var string
	 */
	protected $eventType;
	
	/**
	 *
	 * @var string
	 */
	protected $recordingStartTime;
	
	/**
	 *
	 * @var integer
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var long
	 */
	protected $playTime;
	
	/**
	 *
	 * @var string
	 */
	protected $panelistsInfo;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var string
	 */
	protected $recordFilePath;
	
	/**
	 *
	 * @var string
	 */
	protected $destinationURL;
	
	/**
	 *
	 * @var integer
	 */
	protected $size;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isAccessPassword;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isEnrollmentPassword;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var string
	 */
	protected $viewURL;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'eventName':
				return 'string';
	
			case 'eventType':
				return 'string';
	
			case 'recordingStartTime':
				return 'string';
	
			case 'timeZoneID':
				return 'integer';
	
			case 'playTime':
				return 'long';
	
			case 'panelistsInfo':
				return 'string';
	
			case 'description':
				return 'string';
	
			case 'recordFilePath':
				return 'string';
	
			case 'destinationURL':
				return 'string';
	
			case 'size':
				return 'integer';
	
			case 'isAccessPassword':
				return 'boolean';
	
			case 'isEnrollmentPassword':
				return 'boolean';
	
			case 'hostWebExID':
				return 'string';
	
			case 'viewURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'eventName',
			'eventType',
			'recordingStartTime',
			'timeZoneID',
			'playTime',
			'panelistsInfo',
			'description',
			'recordFilePath',
			'destinationURL',
			'size',
			'isAccessPassword',
			'isEnrollmentPassword',
			'hostWebExID',
			'viewURL',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'eventName',
			'eventType',
			'destinationURL',
			'size',
			'viewURL',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'recordedEventType';
	}
	
	/**
	 * @param string $eventName
	 */
	public function setEventName($eventName)
	{
		$this->eventName = $eventName;
	}
	
	/**
	 * @return string $eventName
	 */
	public function getEventName()
	{
		return $this->eventName;
	}
	
	/**
	 * @param string $eventType
	 */
	public function setEventType($eventType)
	{
		$this->eventType = $eventType;
	}
	
	/**
	 * @return string $eventType
	 */
	public function getEventType()
	{
		return $this->eventType;
	}
	
	/**
	 * @param string $recordingStartTime
	 */
	public function setRecordingStartTime($recordingStartTime)
	{
		$this->recordingStartTime = $recordingStartTime;
	}
	
	/**
	 * @return string $recordingStartTime
	 */
	public function getRecordingStartTime()
	{
		return $this->recordingStartTime;
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
	 * @param long $playTime
	 */
	public function setPlayTime($playTime)
	{
		$this->playTime = $playTime;
	}
	
	/**
	 * @return long $playTime
	 */
	public function getPlayTime()
	{
		return $this->playTime;
	}
	
	/**
	 * @param string $panelistsInfo
	 */
	public function setPanelistsInfo($panelistsInfo)
	{
		$this->panelistsInfo = $panelistsInfo;
	}
	
	/**
	 * @return string $panelistsInfo
	 */
	public function getPanelistsInfo()
	{
		return $this->panelistsInfo;
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
	 * @param string $recordFilePath
	 */
	public function setRecordFilePath($recordFilePath)
	{
		$this->recordFilePath = $recordFilePath;
	}
	
	/**
	 * @return string $recordFilePath
	 */
	public function getRecordFilePath()
	{
		return $this->recordFilePath;
	}
	
	/**
	 * @param string $destinationURL
	 */
	public function setDestinationURL($destinationURL)
	{
		$this->destinationURL = $destinationURL;
	}
	
	/**
	 * @return string $destinationURL
	 */
	public function getDestinationURL()
	{
		return $this->destinationURL;
	}
	
	/**
	 * @param integer $size
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}
	
	/**
	 * @return integer $size
	 */
	public function getSize()
	{
		return $this->size;
	}
	
	/**
	 * @param boolean $isAccessPassword
	 */
	public function setIsAccessPassword($isAccessPassword)
	{
		$this->isAccessPassword = $isAccessPassword;
	}
	
	/**
	 * @return boolean $isAccessPassword
	 */
	public function getIsAccessPassword()
	{
		return $this->isAccessPassword;
	}
	
	/**
	 * @param boolean $isEnrollmentPassword
	 */
	public function setIsEnrollmentPassword($isEnrollmentPassword)
	{
		$this->isEnrollmentPassword = $isEnrollmentPassword;
	}
	
	/**
	 * @return boolean $isEnrollmentPassword
	 */
	public function getIsEnrollmentPassword()
	{
		return $this->isEnrollmentPassword;
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
	 * @param string $viewURL
	 */
	public function setViewURL($viewURL)
	{
		$this->viewURL = $viewURL;
	}
	
	/**
	 * @return string $viewURL
	 */
	public function getViewURL()
	{
		return $this->viewURL;
	}
	
}
		
