<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTeleconferenceonlyRepeatType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlAuoRecurrenceType
	 */
	protected $repeatType;
	
	/**
	 *
	 * @var string
	 */
	protected $expirationDate;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlComDayOfWeekType>
	 */
	protected $dayInWeek;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $dayInMonth;
	
	/**
	 *
	 * @var integer
	 */
	protected $interval;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $weekInMonth;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'repeatType':
				return 'WebexXmlAuoRecurrenceType';
	
			case 'expirationDate':
				return 'string';
	
			case 'dayInWeek':
				return 'WebexXmlArray<WebexXmlComDayOfWeekType>';
	
			case 'dayInMonth':
				return 'WebexXml';
	
			case 'interval':
				return 'integer';
	
			case 'weekInMonth':
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
			'repeatType',
			'expirationDate',
			'dayInWeek',
			'dayInMonth',
			'interval',
			'weekInMonth',
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
		return 'repeatType';
	}
	
	/**
	 * @param WebexXmlAuoRecurrenceType $repeatType
	 */
	public function setRepeatType(WebexXmlAuoRecurrenceType $repeatType)
	{
		$this->repeatType = $repeatType;
	}
	
	/**
	 * @return WebexXmlAuoRecurrenceType $repeatType
	 */
	public function getRepeatType()
	{
		return $this->repeatType;
	}
	
	/**
	 * @param string $expirationDate
	 */
	public function setExpirationDate($expirationDate)
	{
		$this->expirationDate = $expirationDate;
	}
	
	/**
	 * @return string $expirationDate
	 */
	public function getExpirationDate()
	{
		return $this->expirationDate;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlComDayOfWeekType> $dayInWeek
	 */
	public function setDayInWeek(WebexXmlArray $dayInWeek)
	{
		if($dayInWeek->getType() != 'WebexXmlComDayOfWeekType')
			throw new WebexXmlException(get_class($this) . "::dayInWeek must be of type WebexXmlComDayOfWeekType");
		
		$this->dayInWeek = $dayInWeek;
	}
	
	/**
	 * @return WebexXmlArray $dayInWeek
	 */
	public function getDayInWeek()
	{
		return $this->dayInWeek;
	}
	
	/**
	 * @param WebexXml $dayInMonth
	 */
	public function setDayInMonth(WebexXml $dayInMonth)
	{
		$this->dayInMonth = $dayInMonth;
	}
	
	/**
	 * @return WebexXml $dayInMonth
	 */
	public function getDayInMonth()
	{
		return $this->dayInMonth;
	}
	
	/**
	 * @param integer $interval
	 */
	public function setInterval($interval)
	{
		$this->interval = $interval;
	}
	
	/**
	 * @return integer $interval
	 */
	public function getInterval()
	{
		return $this->interval;
	}
	
	/**
	 * @param WebexXml $weekInMonth
	 */
	public function setWeekInMonth(WebexXml $weekInMonth)
	{
		$this->weekInMonth = $weekInMonth;
	}
	
	/**
	 * @return WebexXml $weekInMonth
	 */
	public function getWeekInMonth()
	{
		return $this->weekInMonth;
	}
	
}
		
