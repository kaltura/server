<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOpenSessionType extends WebexXmlRequestType
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
	 * @var string
	 */
	protected $hostWebExID;
	
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
	 * @var long
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var WebexXmlComListingType
	 */
	protected $listStatus;
	
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
	
			case 'hostWebExID':
				return 'string';
	
			case 'startTime':
				return 'string';
	
			case 'actualStartTime':
				return 'string';
	
			case 'timeZoneID':
				return 'long';
	
			case 'listStatus':
				return 'WebexXmlComListingType';
	
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
			'hostWebExID',
			'startTime',
			'actualStartTime',
			'timeZoneID',
			'listStatus',
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
			'hostWebExID',
			'startTime',
			'actualStartTime',
			'timeZoneID',
			'listStatus',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'openSessionType';
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
	 * @param string $startTime
	 */
	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
	}
	
	/**
	 * @return string $startTime
	 */
	public function getStartTime()
	{
		return $this->startTime;
	}
	
	/**
	 * @param string $actualStartTime
	 */
	public function setActualStartTime($actualStartTime)
	{
		$this->actualStartTime = $actualStartTime;
	}
	
	/**
	 * @return string $actualStartTime
	 */
	public function getActualStartTime()
	{
		return $this->actualStartTime;
	}
	
	/**
	 * @param long $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return long $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
	/**
	 * @param WebexXmlComListingType $listStatus
	 */
	public function setListStatus(WebexXmlComListingType $listStatus)
	{
		$this->listStatus = $listStatus;
	}
	
	/**
	 * @return WebexXmlComListingType $listStatus
	 */
	public function getListStatus()
	{
		return $this->listStatus;
	}
	
}

