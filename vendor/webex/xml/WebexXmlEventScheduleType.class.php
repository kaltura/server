<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventScheduleType extends WebexXmlRequestType
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
	 * @var integer
	 */
	protected $duration;
	
	/**
	 *
	 * @var integer
	 */
	protected $openTime;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var WebexXmlComEntryExitToneType
	 */
	protected $entryExitTone;
	
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
	
			case 'duration':
				return 'integer';
	
			case 'openTime':
				return 'integer';
	
			case 'hostWebExID':
				return 'string';
	
			case 'entryExitTone':
				return 'WebexXmlComEntryExitToneType';
	
			case 'extURL':
				return 'WebexXml';
	
			case 'extNotifyTime':
				return 'WebexXml';
	
			case 'joinNotifyURL':
				return 'WebexXml';
	
			case 'joinTeleconfBeforeHost':
				return 'boolean';
	
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
			'duration',
			'openTime',
			'hostWebExID',
			'entryExitTone',
			'extURL',
			'extNotifyTime',
			'joinNotifyURL',
			'joinTeleconfBeforeHost',
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
	 * @param WebexXmlComEntryExitToneType $entryExitTone
	 */
	public function setEntryExitTone(WebexXmlComEntryExitToneType $entryExitTone)
	{
		$this->entryExitTone = $entryExitTone;
	}
	
	/**
	 * @return WebexXmlComEntryExitToneType $entryExitTone
	 */
	public function getEntryExitTone()
	{
		return $this->entryExitTone;
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
	
}
		
