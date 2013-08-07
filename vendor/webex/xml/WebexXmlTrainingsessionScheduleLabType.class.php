<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionScheduleLabType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $labName;
	
	/**
	 *
	 * @var string
	 */
	protected $confName;
	
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
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var integer
	 */
	protected $numComputers;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'labName':
				return 'string';
	
			case 'confName':
				return 'string';
	
			case 'timeZoneID':
				return 'integer';
	
			case 'sessionStartTime':
				return 'string';
	
			case 'sessionEndTime':
				return 'string';
	
			case 'hostWebExID':
				return 'string';
	
			case 'numComputers':
				return 'integer';
	
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
			'confName',
			'timeZoneID',
			'sessionStartTime',
			'sessionEndTime',
			'hostWebExID',
			'numComputers',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'labName',
			'confName',
			'timeZoneID',
			'sessionStartTime',
			'sessionEndTime',
			'hostWebExID',
			'numComputers',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'scheduleLabType';
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
	 * @param string $confName
	 */
	public function setConfName($confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @return string $confName
	 */
	public function getConfName()
	{
		return $this->confName;
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
	
}
		
