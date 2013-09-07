<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingRepeatType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlMeetRepeatTypeType
	 */
	protected $repeatType;
	
	/**
	 *
	 * @var integer
	 */
	protected $interval;
	
	/**
	 *
	 * @var integer
	 */
	protected $afterMeetingNumber;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlComDayOfWeekType>
	 */
	protected $dayInWeek;
	
	/**
	 *
	 * @var string
	 */
	protected $expirationDate;
	
	/**
	 *
	 * @var long
	 */
	protected $dayInMonth;
	
	/**
	 *
	 * @var long
	 */
	protected $weekInMonth;
	
	/**
	 *
	 * @var long
	 */
	protected $monthInYear;
	
	/**
	 *
	 * @var long
	 */
	protected $dayInYear;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'repeatType':
				return 'WebexXmlMeetRepeatTypeType';
	
			case 'interval':
				return 'integer';
	
			case 'afterMeetingNumber':
				return 'integer';
	
			case 'dayInWeek':
				return 'WebexXmlArray<WebexXmlComDayOfWeekType>';
	
			case 'expirationDate':
				return 'string';
	
			case 'dayInMonth':
				return 'long';
	
			case 'weekInMonth':
				return 'long';
	
			case 'monthInYear':
				return 'long';
	
			case 'dayInYear':
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
			'repeatType',
			'interval',
			'afterMeetingNumber',
			'dayInWeek',
			'expirationDate',
			'dayInMonth',
			'weekInMonth',
			'monthInYear',
			'dayInYear',
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
	 * @param WebexXmlMeetRepeatTypeType $repeatType
	 */
	public function setRepeatType(WebexXmlMeetRepeatTypeType $repeatType)
	{
		$this->repeatType = $repeatType;
	}
	
	/**
	 * @return WebexXmlMeetRepeatTypeType $repeatType
	 */
	public function getRepeatType()
	{
		return $this->repeatType;
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
	 * @param integer $afterMeetingNumber
	 */
	public function setAfterMeetingNumber($afterMeetingNumber)
	{
		$this->afterMeetingNumber = $afterMeetingNumber;
	}
	
	/**
	 * @return integer $afterMeetingNumber
	 */
	public function getAfterMeetingNumber()
	{
		return $this->afterMeetingNumber;
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
	 * @param long $dayInMonth
	 */
	public function setDayInMonth($dayInMonth)
	{
		$this->dayInMonth = $dayInMonth;
	}
	
	/**
	 * @return long $dayInMonth
	 */
	public function getDayInMonth()
	{
		return $this->dayInMonth;
	}
	
	/**
	 * @param long $weekInMonth
	 */
	public function setWeekInMonth($weekInMonth)
	{
		$this->weekInMonth = $weekInMonth;
	}
	
	/**
	 * @return long $weekInMonth
	 */
	public function getWeekInMonth()
	{
		return $this->weekInMonth;
	}
	
	/**
	 * @param long $monthInYear
	 */
	public function setMonthInYear($monthInYear)
	{
		$this->monthInYear = $monthInYear;
	}
	
	/**
	 * @return long $monthInYear
	 */
	public function getMonthInYear()
	{
		return $this->monthInYear;
	}
	
	/**
	 * @param long $dayInYear
	 */
	public function setDayInYear($dayInYear)
	{
		$this->dayInYear = $dayInYear;
	}
	
	/**
	 * @return long $dayInYear
	 */
	public function getDayInYear()
	{
		return $this->dayInYear;
	}
	
}
		
