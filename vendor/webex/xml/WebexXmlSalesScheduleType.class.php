<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesScheduleType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $startDate;
	
	/**
	 *
	 * @var integer
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var WebexXmlComTimeZoneType
	 */
	protected $timeZone;
	
	/**
	 *
	 * @var integer
	 */
	protected $duration;
	
	/**
	 *
	 * @var boolean
	 */
	protected $openTimeEnabled;
	
	/**
	 *
	 * @var integer
	 */
	protected $openTime;
	
	/**
	 *
	 * @var string
	 */
	protected $templateFilePath;
	
	/**
	 *
	 * @var string
	 */
	protected $showFilePath;
	
	/**
	 *
	 * @var boolean
	 */
	protected $showFileStartMode;
	
	/**
	 *
	 * @var boolean
	 */
	protected $showFileContPlayFlag;
	
	/**
	 *
	 * @var long
	 */
	protected $showFileInterVal;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendScheduleEmail;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $extURL;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $extNotifyTime;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $joinNotifyURL;
	
	/**
	 *
	 * @var boolean
	 */
	protected $joinTeleconfBeforeHost;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'startDate':
				return 'string';
	
			case 'timeZoneID':
				return 'integer';
	
			case 'timeZone':
				return 'WebexXmlComTimeZoneType';
	
			case 'duration':
				return 'integer';
	
			case 'openTimeEnabled':
				return 'boolean';
	
			case 'openTime':
				return 'integer';
	
			case 'templateFilePath':
				return 'string';
	
			case 'showFilePath':
				return 'string';
	
			case 'showFileStartMode':
				return 'boolean';
	
			case 'showFileContPlayFlag':
				return 'boolean';
	
			case 'showFileInterVal':
				return 'long';
	
			case 'sendScheduleEmail':
				return 'boolean';
	
			case 'extURL':
				return 'WebexXml';
	
			case 'extNotifyTime':
				return 'WebexXml';
	
			case 'joinNotifyURL':
				return 'WebexXml';
	
			case 'joinTeleconfBeforeHost':
				return 'boolean';
	
			case 'hostWebExID':
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
			'startDate',
			'timeZoneID',
			'timeZone',
			'duration',
			'openTimeEnabled',
			'openTime',
			'templateFilePath',
			'showFilePath',
			'showFileStartMode',
			'showFileContPlayFlag',
			'showFileInterVal',
			'sendScheduleEmail',
			'extURL',
			'extNotifyTime',
			'joinNotifyURL',
			'joinTeleconfBeforeHost',
			'hostWebExID',
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
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'scheduleType';
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
	 * @param WebexXmlComTimeZoneType $timeZone
	 */
	public function setTimeZone(WebexXmlComTimeZoneType $timeZone)
	{
		$this->timeZone = $timeZone;
	}
	
	/**
	 * @return WebexXmlComTimeZoneType $timeZone
	 */
	public function getTimeZone()
	{
		return $this->timeZone;
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
	 * @param boolean $openTimeEnabled
	 */
	public function setOpenTimeEnabled($openTimeEnabled)
	{
		$this->openTimeEnabled = $openTimeEnabled;
	}
	
	/**
	 * @return boolean $openTimeEnabled
	 */
	public function getOpenTimeEnabled()
	{
		return $this->openTimeEnabled;
	}
	
	/**
	 * @param integer $openTime
	 */
	public function setOpenTime($openTime)
	{
		$this->openTime = $openTime;
	}
	
	/**
	 * @return integer $openTime
	 */
	public function getOpenTime()
	{
		return $this->openTime;
	}
	
	/**
	 * @param string $templateFilePath
	 */
	public function setTemplateFilePath($templateFilePath)
	{
		$this->templateFilePath = $templateFilePath;
	}
	
	/**
	 * @return string $templateFilePath
	 */
	public function getTemplateFilePath()
	{
		return $this->templateFilePath;
	}
	
	/**
	 * @param string $showFilePath
	 */
	public function setShowFilePath($showFilePath)
	{
		$this->showFilePath = $showFilePath;
	}
	
	/**
	 * @return string $showFilePath
	 */
	public function getShowFilePath()
	{
		return $this->showFilePath;
	}
	
	/**
	 * @param boolean $showFileStartMode
	 */
	public function setShowFileStartMode($showFileStartMode)
	{
		$this->showFileStartMode = $showFileStartMode;
	}
	
	/**
	 * @return boolean $showFileStartMode
	 */
	public function getShowFileStartMode()
	{
		return $this->showFileStartMode;
	}
	
	/**
	 * @param boolean $showFileContPlayFlag
	 */
	public function setShowFileContPlayFlag($showFileContPlayFlag)
	{
		$this->showFileContPlayFlag = $showFileContPlayFlag;
	}
	
	/**
	 * @return boolean $showFileContPlayFlag
	 */
	public function getShowFileContPlayFlag()
	{
		return $this->showFileContPlayFlag;
	}
	
	/**
	 * @param long $showFileInterVal
	 */
	public function setShowFileInterVal($showFileInterVal)
	{
		$this->showFileInterVal = $showFileInterVal;
	}
	
	/**
	 * @return long $showFileInterVal
	 */
	public function getShowFileInterVal()
	{
		return $this->showFileInterVal;
	}
	
	/**
	 * @param boolean $sendScheduleEmail
	 */
	public function setSendScheduleEmail($sendScheduleEmail)
	{
		$this->sendScheduleEmail = $sendScheduleEmail;
	}
	
	/**
	 * @return boolean $sendScheduleEmail
	 */
	public function getSendScheduleEmail()
	{
		return $this->sendScheduleEmail;
	}
	
	/**
	 * @param WebexXml $extURL
	 */
	public function setExtURL(WebexXml $extURL)
	{
		$this->extURL = $extURL;
	}
	
	/**
	 * @return WebexXml $extURL
	 */
	public function getExtURL()
	{
		return $this->extURL;
	}
	
	/**
	 * @param WebexXml $extNotifyTime
	 */
	public function setExtNotifyTime(WebexXml $extNotifyTime)
	{
		$this->extNotifyTime = $extNotifyTime;
	}
	
	/**
	 * @return WebexXml $extNotifyTime
	 */
	public function getExtNotifyTime()
	{
		return $this->extNotifyTime;
	}
	
	/**
	 * @param WebexXml $joinNotifyURL
	 */
	public function setJoinNotifyURL(WebexXml $joinNotifyURL)
	{
		$this->joinNotifyURL = $joinNotifyURL;
	}
	
	/**
	 * @return WebexXml $joinNotifyURL
	 */
	public function getJoinNotifyURL()
	{
		return $this->joinNotifyURL;
	}
	
	/**
	 * @param boolean $joinTeleconfBeforeHost
	 */
	public function setJoinTeleconfBeforeHost($joinTeleconfBeforeHost)
	{
		$this->joinTeleconfBeforeHost = $joinTeleconfBeforeHost;
	}
	
	/**
	 * @return boolean $joinTeleconfBeforeHost
	 */
	public function getJoinTeleconfBeforeHost()
	{
		return $this->joinTeleconfBeforeHost;
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
	
}
		
