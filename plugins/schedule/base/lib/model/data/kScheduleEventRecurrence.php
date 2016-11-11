<?php
/**
 * @package plugins.schedule
 * @subpackage model.data
 */
class kScheduleEventRecurrence
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var ScheduleEventRecurrenceFrequency
	 */
	private $frequency;
	
	/**
	 * @var int unix timestamp
	 */
	private $until;


	/**
	 * @var string timeZone
	 */
	private $timeZone;

	/**
	 * @var int
	 */
	private $count;
	
	/**
	 * @var int
	 */
	private $interval = 1;
	
	/**
	 * @var string comma separated numbers between 0 to 59
	 */
	private $bySecond;
	
	/**
	 * @var string comma separated numbers between 0 to 59
	 */
	private $byMinute;
	
	/**
	 * @var string comma separated numbers between 0 to 23
	 */
	private $byHour;
	
	/**
	 * @var string comma separated of ScheduleEventRecurrenceDay
	 * Each byDay value can also be preceded by a positive (+n) or negative (-n) integer.
	 * If present, this indicates the nth occurrence of the specific day within the MONTHLY or YEARLY RRULE.
	 * For example, within a MONTHLY rule, +1MO (or simply 1MO) represents the first Monday within the month, whereas -1MO represents the last Monday of the month.
	 * If an integer modifier is not present, it means all days of this type within the specified frequency.
	 * For example, within a MONTHLY rule, MO represents all Mondays within the month.
	 */
	private $byDay;
	
	/**
	 * @var string comma separated of numbers between -31 to 31, excluding 0.
	 * For example, -10 represents the tenth to the last day of the month.
	 */
	private $byMonthDay;
	
	/**
	 * @var string comma separated of numbers between -366 to 366, excluding 0.
	 * For example, -1 represents the last day of the year (December 31st) and -306 represents the 306th to the last day of the year (March 1st).
	 */
	private $byYearDay;
	
	/**
	 * @var string comma separated of numbers between -53 to 53, excluding 0.
	 * This corresponds to weeks according to week numbering.
	 * A week is defined as a seven day period, starting on the day of the week defined to be the week start.
	 * Week number one of the calendar year is the first week which contains at least four (4) days in that calendar year.
	 * This rule part is only valid for YEARLY frequency.
	 * For example, 3 represents the third week of the year.
	 */
	private $byWeekNumber;
	
	/**
	 * @var string comma separated numbers between 1 to 12
	 */
	private $byMonth;
	
	/**
	 * @var string comma separated of numbers between -366 to 366, excluding 0.
	 * Corresponds to the nth occurrence within the set of events specified by the rule.
	 * It must only be used in conjunction with another by* rule part.
	 * For example "the last work day of the month" could be represented as: frequency=MONTHLY;byDay=MO,TU,WE,TH,FR;byOffset=-1
	 * Each byOffset value can include a positive (+n) or negative (-n) integer.
	 * If present, this indicates the nth occurrence of the specific occurrence within the set of events specified by the rule.
	 */
	private $byOffset;
	
	/**
	 * @var ScheduleEventRecurrenceDay
	 * Specifies the day on which the workweek starts.
	 * This is significant when a WEEKLY frequency has an interval greater than 1, and a byDay rule part is specified.
	 * This is also significant when in a YEARLY frequency when a byWeekNumber rule part is specified.
	 * The default value is MONDAY.
	 */
	private $weekStartDay;

	/**
	 * @return the $name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return the $frequency
	 */
	public function getFrequency()
	{
		return $this->frequency;
	}

	/**
	 * @return the $until
	 */
	public function getUntil()
	{
		return $this->until;
	}

	/**
	 * @return the $timeZone
	 */
	public function getTimeZone()
	{
		return $this->timeZone;
	}

	/**
	 * @return the $count
	 */
	public function getCount()
	{
		return $this->count;
	}

	/**
	 * @return the $interval
	 */
	public function getInterval()
	{
		return $this->interval;
	}

	/**
	 * @return the $bySecond
	 */
	public function getBySecond()
	{
		return $this->bySecond;
	}

	/**
	 * @return the $byMinute
	 */
	public function getByMinute()
	{
		return $this->byMinute;
	}

	/**
	 * @return the $byHour
	 */
	public function getByHour()
	{
		return $this->byHour;
	}

	/**
	 * @return the $byDay
	 */
	public function getByDay()
	{
		return $this->byDay;
	}

	/**
	 * @return the $byMonthDay
	 */
	public function getByMonthDay()
	{
		return $this->byMonthDay;
	}

	/**
	 * @return the $byYearDay
	 */
	public function getByYearDay()
	{
		return $this->byYearDay;
	}

	/**
	 * @return the $byWeekNumber
	 */
	public function getByWeekNumber()
	{
		return $this->byWeekNumber;
	}

	/**
	 * @return the $byMonth
	 */
	public function getByMonth()
	{
		return $this->byMonth;
	}

	/**
	 * @return the $byOffset
	 */
	public function getByOffset()
	{
		return $this->byOffset;
	}

	/**
	 * @return the $weekStartDay
	 */
	public function getWeekStartDay()
	{
		return $this->weekStartDay;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param ScheduleEventRecurrenceFrequency $frequency
	 */
	public function setFrequency($frequency)
	{
		$this->frequency = $frequency;
	}

	/**
	 * @param int $until
	 */
	public function setUntil($until)
	{
		$this->until = $until;
	}

	/**
	 * @parma string $timeZone
	 */
	public function setTimeZone($timeZone)
	{
		$this->timeZone = $timeZone;
	}

	/**
	 * @param int $count
	 */
	public function setCount($count)
	{
		$this->count = $count;
	}

	/**
	 * @param int $interval
	 */
	public function setInterval($interval)
	{
		$this->interval = $interval;
	}

	/**
	 * @param string $bySecond
	 */
	public function setBySecond($bySecond)
	{
		$this->bySecond = $bySecond;
	}

	/**
	 * @param string $byMinute
	 */
	public function setByMinute($byMinute)
	{
		$this->byMinute = $byMinute;
	}

	/**
	 * @param string $byHour
	 */
	public function setByHour($byHour)
	{
		$this->byHour = $byHour;
	}

	/**
	 * @param string $byDay
	 */
	public function setByDay($byDay)
	{
		$this->byDay = $byDay;
	}

	/**
	 * @param string $byMonthDay
	 */
	public function setByMonthDay($byMonthDay)
	{
		$this->byMonthDay = $byMonthDay;
	}

	/**
	 * @param string $byYearDay
	 */
	public function setByYearDay($byYearDay)
	{
		$this->byYearDay = $byYearDay;
	}

	/**
	 * @param string $byWeekNumber
	 */
	public function setByWeekNumber($byWeekNumber)
	{
		$this->byWeekNumber = $byWeekNumber;
	}

	/**
	 * @param string $byMonth
	 */
	public function setByMonth($byMonth)
	{
		$this->byMonth = $byMonth;
	}

	/**
	 * @param string $byOffset
	 */
	public function setByOffset($byOffset)
	{
		$this->byOffset = $byOffset;
	}

	/**
	 * @param ScheduleEventRecurrenceFrequency $weekStartDay
	 */
	public function setWeekStartDay($weekStartDay)
	{
		$this->weekStartDay = $weekStartDay;
	}

	/**
	 * @return array of params as key and values
	 */
	public function asArray()
	{
		$paramsArray = get_object_vars($this);
		return $paramsArray;

	}
	
}