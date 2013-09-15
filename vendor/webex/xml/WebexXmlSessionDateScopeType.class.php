<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSessionDateScopeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $startDateStart;
	
	/**
	 *
	 * @var string
	 */
	protected $startDateEnd;
	
	/**
	 *
	 * @var string
	 */
	protected $endDateStart;
	
	/**
	 *
	 * @var string
	 */
	protected $endDateEnd;
	
	/**
	 *
	 * @var long
	 */
	protected $timeZoneID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'startDateStart':
				return 'string';
	
			case 'startDateEnd':
				return 'string';
	
			case 'endDateStart':
				return 'string';
	
			case 'endDateEnd':
				return 'string';
	
			case 'timeZoneID':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'startDateStart',
			'startDateEnd',
			'endDateStart',
			'endDateEnd',
			'timeZoneID',
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
		return 'dateScopeType';
	}
	
	/**
	 * @param string $startDateStart
	 */
	public function setStartDateStart($startDateStart)
	{
		$this->startDateStart = $startDateStart;
	}
	
	/**
	 * @return string $startDateStart
	 */
	public function getStartDateStart()
	{
		return $this->startDateStart;
	}
	
	/**
	 * @param string $startDateEnd
	 */
	public function setStartDateEnd($startDateEnd)
	{
		$this->startDateEnd = $startDateEnd;
	}
	
	/**
	 * @return string $startDateEnd
	 */
	public function getStartDateEnd()
	{
		return $this->startDateEnd;
	}
	
	/**
	 * @param string $endDateStart
	 */
	public function setEndDateStart($endDateStart)
	{
		$this->endDateStart = $endDateStart;
	}
	
	/**
	 * @return string $endDateStart
	 */
	public function getEndDateStart()
	{
		return $this->endDateStart;
	}
	
	/**
	 * @param string $endDateEnd
	 */
	public function setEndDateEnd($endDateEnd)
	{
		$this->endDateEnd = $endDateEnd;
	}
	
	/**
	 * @return string $endDateEnd
	 */
	public function getEndDateEnd()
	{
		return $this->endDateEnd;
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
	
}

