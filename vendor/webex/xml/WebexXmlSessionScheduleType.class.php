<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSessionScheduleType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $startDate;
	
	/**
	 *
	 * @var WebexXmlComTimeZoneType
	 */
	protected $timeZone;
	
	/**
	 *
	 * @var long
	 */
	protected $duration;
	
	/**
	 *
	 * @var long
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var integer
	 */
	protected $openTime;
	
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
	 * @var WebexXmlComEntryExitToneType
	 */
	protected $entryExitTone;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $destinationURL;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'startDate':
				return 'WebexXmlArray<string>';
	
			case 'timeZone':
				return 'WebexXmlComTimeZoneType';
	
			case 'duration':
				return 'long';
	
			case 'timeZoneID':
				return 'long';
	
			case 'hostWebExID':
				return 'string';
	
			case 'openTime':
				return 'integer';
	
			case 'extURL':
				return 'WebexXml';
	
			case 'extNotifyTime':
				return 'WebexXml';
	
			case 'joinNotifyURL':
				return 'WebexXml';
	
			case 'joinTeleconfBeforeHost':
				return 'boolean';
	
			case 'entryExitTone':
				return 'WebexXmlComEntryExitToneType';
	
			case 'destinationURL':
				return 'WebexXml';
	
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
			'timeZone',
			'duration',
			'timeZoneID',
			'hostWebExID',
			'openTime',
			'extURL',
			'extNotifyTime',
			'joinNotifyURL',
			'joinTeleconfBeforeHost',
			'entryExitTone',
			'destinationURL',
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
	 * @param WebexXmlArray<string> $startDate
	 */
	public function setStartDate($startDate)
	{
		if($startDate->getType() != 'string')
			throw new WebexXmlException(get_class($this) . "::startDate must be of type string");
		
		$this->startDate = $startDate;
	}
	
	/**
	 * @return WebexXmlArray $startDate
	 */
	public function getStartDate()
	{
		return $this->startDate;
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
	 * @param long $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @return long $duration
	 */
	public function getDuration()
	{
		return $this->duration;
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
	 * @param WebexXml $destinationURL
	 */
	public function setDestinationURL(WebexXml $destinationURL)
	{
		$this->destinationURL = $destinationURL;
	}
	
	/**
	 * @return WebexXml $destinationURL
	 */
	public function getDestinationURL()
	{
		return $this->destinationURL;
	}
	
}
		
