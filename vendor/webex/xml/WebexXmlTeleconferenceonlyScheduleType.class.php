<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTeleconferenceonlyScheduleType extends WebexXmlRequestType
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
	 * @var WebexXmlAuoEntryExitToneType
	 */
	protected $entryExitTone;
	
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
	
			case 'entryExitTone':
				return 'WebexXmlAuoEntryExitToneType';
	
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
			'entryExitTone',
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
	 * @param WebexXmlAuoEntryExitToneType $entryExitTone
	 */
	public function setEntryExitTone(WebexXmlAuoEntryExitToneType $entryExitTone)
	{
		$this->entryExitTone = $entryExitTone;
	}
	
	/**
	 * @return WebexXmlAuoEntryExitToneType $entryExitTone
	 */
	public function getEntryExitTone()
	{
		return $this->entryExitTone;
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
		
