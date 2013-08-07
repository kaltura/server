<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesRepeatType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlSalesRecurrenceType
	 */
	protected $repeatType;
	
	/**
	 *
	 * @var integer
	 */
	protected $endAfter;
	
	/**
	 *
	 * @var string
	 */
	protected $expirationDate;
	
	/**
	 *
	 * @var integer
	 */
	protected $interval;
	
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
	 * @var WebexXml
	 */
	protected $weekInMonth;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $monthInYear;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'repeatType':
				return 'WebexXmlSalesRecurrenceType';
	
			case 'endAfter':
				return 'integer';
	
			case 'expirationDate':
				return 'string';
	
			case 'interval':
				return 'integer';
	
			case 'dayInWeek':
				return 'WebexXmlArray<WebexXmlComDayOfWeekType>';
	
			case 'dayInMonth':
				return 'WebexXml';
	
			case 'weekInMonth':
				return 'WebexXml';
	
			case 'monthInYear':
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
			'endAfter',
			'expirationDate',
			'interval',
			'dayInWeek',
			'dayInMonth',
			'weekInMonth',
			'monthInYear',
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
	 * @param WebexXmlSalesRecurrenceType $repeatType
	 */
	public function setRepeatType(WebexXmlSalesRecurrenceType $repeatType)
	{
		$this->repeatType = $repeatType;
	}
	
	/**
	 * @return WebexXmlSalesRecurrenceType $repeatType
	 */
	public function getRepeatType()
	{
		return $this->repeatType;
	}
	
	/**
	 * @param integer $endAfter
	 */
	public function setEndAfter($endAfter)
	{
		$this->endAfter = $endAfter;
	}
	
	/**
	 * @return integer $endAfter
	 */
	public function getEndAfter()
	{
		return $this->endAfter;
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
	
	/**
	 * @param WebexXml $monthInYear
	 */
	public function setMonthInYear(WebexXml $monthInYear)
	{
		$this->monthInYear = $monthInYear;
	}
	
	/**
	 * @return WebexXml $monthInYear
	 */
	public function getMonthInYear()
	{
		return $this->monthInYear;
	}
	
}
		
