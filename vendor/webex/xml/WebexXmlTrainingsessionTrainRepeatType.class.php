<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionTrainRepeatType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlTrainRepeatTypeType
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
	 * @var int
	 */
	protected $endAfter;
	
	/**
	 *
	 * @var WebexXmlTrainOccurentTypeType
	 */
	protected $occurenceType;
	
	/**
	 *
	 * @var int
	 */
	protected $interval;
	
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
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainRepeatSessionType>
	 */
	protected $repeatSession;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'repeatType':
				return 'WebexXmlTrainRepeatTypeType';
	
			case 'expirationDate':
				return 'string';
	
			case 'dayInWeek':
				return 'WebexXmlArray<WebexXmlComDayOfWeekType>';
	
			case 'endAfter':
				return 'int';
	
			case 'occurenceType':
				return 'WebexXmlTrainOccurentTypeType';
	
			case 'interval':
				return 'int';
	
			case 'dayInMonth':
				return 'long';
	
			case 'weekInMonth':
				return 'long';
	
			case 'monthInYear':
				return 'long';
	
			case 'dayInYear':
				return 'long';
	
			case 'repeatSession':
				return 'WebexXmlArray<WebexXmlTrainRepeatSessionType>';
	
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
			'endAfter',
			'occurenceType',
			'interval',
			'dayInMonth',
			'weekInMonth',
			'monthInYear',
			'dayInYear',
			'repeatSession',
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
		return 'trainRepeatType';
	}
	
	/**
	 * @param WebexXmlTrainRepeatTypeType $repeatType
	 */
	public function setRepeatType(WebexXmlTrainRepeatTypeType $repeatType)
	{
		$this->repeatType = $repeatType;
	}
	
	/**
	 * @return WebexXmlTrainRepeatTypeType $repeatType
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
	 * @param int $endAfter
	 */
	public function setEndAfter($endAfter)
	{
		$this->endAfter = $endAfter;
	}
	
	/**
	 * @return int $endAfter
	 */
	public function getEndAfter()
	{
		return $this->endAfter;
	}
	
	/**
	 * @param WebexXmlTrainOccurentTypeType $occurenceType
	 */
	public function setOccurenceType(WebexXmlTrainOccurentTypeType $occurenceType)
	{
		$this->occurenceType = $occurenceType;
	}
	
	/**
	 * @return WebexXmlTrainOccurentTypeType $occurenceType
	 */
	public function getOccurenceType()
	{
		return $this->occurenceType;
	}
	
	/**
	 * @param int $interval
	 */
	public function setInterval($interval)
	{
		$this->interval = $interval;
	}
	
	/**
	 * @return int $interval
	 */
	public function getInterval()
	{
		return $this->interval;
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
	
	/**
	 * @param WebexXmlArray<WebexXmlTrainRepeatSessionType> $repeatSession
	 */
	public function setRepeatSession(WebexXmlArray $repeatSession)
	{
		if($repeatSession->getType() != 'WebexXmlTrainRepeatSessionType')
			throw new WebexXmlException(get_class($this) . "::repeatSession must be of type WebexXmlTrainRepeatSessionType");
		
		$this->repeatSession = $repeatSession;
	}
	
	/**
	 * @return WebexXmlArray $repeatSession
	 */
	public function getRepeatSession()
	{
		return $this->repeatSession;
	}
	
}
		
