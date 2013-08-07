<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionAvailabilityLabType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $labName;
	
	/**
	 *
	 * @var integer
	 */
	protected $timeZoneID;
	
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
	 * @var integer
	 */
	protected $numComputers;
	
	/**
	 *
	 * @var WebexXmlComLabStatusType
	 */
	protected $status;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'labName':
				return 'string';
	
			case 'timeZoneID':
				return 'integer';
	
			case 'sessionStartTime':
				return 'string';
	
			case 'sessionEndTime':
				return 'string';
	
			case 'numComputers':
				return 'integer';
	
			case 'status':
				return 'WebexXmlComLabStatusType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'labName',
			'timeZoneID',
			'sessionStartTime',
			'sessionEndTime',
			'numComputers',
			'status',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'labName',
			'timeZoneID',
			'sessionStartTime',
			'sessionEndTime',
			'numComputers',
			'status',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'availabilityLabType';
	}
	
	/**
	 * @param string $labName
	 */
	public function setLabName($labName)
	{
		$this->labName = $labName;
	}
	
	/**
	 * @return string $labName
	 */
	public function getLabName()
	{
		return $this->labName;
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
	 * @param string $sessionStartTime
	 */
	public function setSessionStartTime($sessionStartTime)
	{
		$this->sessionStartTime = $sessionStartTime;
	}
	
	/**
	 * @return string $sessionStartTime
	 */
	public function getSessionStartTime()
	{
		return $this->sessionStartTime;
	}
	
	/**
	 * @param string $sessionEndTime
	 */
	public function setSessionEndTime($sessionEndTime)
	{
		$this->sessionEndTime = $sessionEndTime;
	}
	
	/**
	 * @return string $sessionEndTime
	 */
	public function getSessionEndTime()
	{
		return $this->sessionEndTime;
	}
	
	/**
	 * @param integer $numComputers
	 */
	public function setNumComputers($numComputers)
	{
		$this->numComputers = $numComputers;
	}
	
	/**
	 * @return integer $numComputers
	 */
	public function getNumComputers()
	{
		return $this->numComputers;
	}
	
	/**
	 * @param WebexXmlComLabStatusType $status
	 */
	public function setStatus(WebexXmlComLabStatusType $status)
	{
		$this->status = $status;
	}
	
	/**
	 * @return WebexXmlComLabStatusType $status
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
}

