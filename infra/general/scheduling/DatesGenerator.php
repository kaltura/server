<?php
/**
 * Dates candidates generator for Kaltura scheduling
 *
 * @package infra
 * @subpackage general
 */
class DatesGenerator
{
	const SECONDLY = 'seconds';
	const MINUTELY = 'minutes';
	const HOURLY = 'hours';
	const DAILY = 'days';
	const WEEKLY = 'weeks';
	const MONTHLY = 'months';
	const YEARLY = 'years';

	/**
	 * @var int
	 */
	protected $maxDuration;

	/**
	 * @var int
	 */
	protected $maxRecurrences;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $frequency;

	/**
	 * @var int unix timestamp
	 */
	private $until;

	/**
	 * @var string timeZone
	 */
	private $timeZone = null;

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
	 * @var string
	 * Specifies the day on which the workweek starts.
	 * This is significant when a WEEKLY frequency has an interval greater than 1, and a byDay rule part is specified.
	 * This is also significant when in a YEARLY frequency when a byWeekNumber rule part is specified.
	 * The default value is MONDAY.
	 */
	private $weekStartDay;

	/**
	 * @var array
	 * this array callable as function name who can print data to the log
	 */
	private $logger;

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
	 * @return tge $timeZone
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
	 * @param string $frequency
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
	 * @param string $timeZone
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
	 * @param string $weekStartDay
	 */
	public function setWeekStartDay($weekStartDay)
	{
		$this->weekStartDay = $weekStartDay;
	}

	/**
	 * @param int $maxRecurrences
	 * @param array $paramsArray
	 * @param string $logger
	 */
	public function __construct($maxRecurrences = null, $paramsArray = array(), $logger = null)
	{
		$this->maxRecurrences = $maxRecurrences;
		$this->logger = $logger;
		foreach($paramsArray as $param => $value)
		{
			$this->$param = $value;
		}

		if (!$this->interval)
			$this->interval = 1;
		if (!$this->maxRecurrences)
			$this->maxRecurrences = 10;
		if (!$this->weekStartDay)
			$this->weekStartDay = 'MO';
		$this->reOrderDays($this->weekStartDay);
	}


	/**
	 * Returns a list of timestamps in the specified period.
	 * @param int $periodStart the starting timestamp of the period
	 * @return array
	 */
	public function getDates($periodStart = null)
	{
		$original = null;
		if (!is_null($this->timeZone))
		{
			$original = date_default_timezone_get();
			date_default_timezone_set($this->timeZone);
		}

		if(!$periodStart)
			$periodStart = time();

		$dates = $this->getRecurrencesDates($periodStart, $periodStart, $this->maxRecurrences);

		sort($dates);
		if(count($dates) > $this->maxRecurrences)
			$dates = array_slice($dates, 0, $this->maxRecurrences);

		if (!is_null($original))
			date_default_timezone_set($original);;

		return $dates;
	}

	/**
	 * Returns a list of timestamps in the specified period.
	 * @param int $periodStart the starting timestamp of the period
	 * @param int $seed the timestamp of this Recurrence's first instance
	 * @param int $limit maximum number of dates
	 * @return array
	 */
	public function getRecurrencesDates($periodStart, $seed = null, $limit = null)
	{
		$periodEnd = strtotime('+2 year', $periodStart);
		if(!is_null($this->until) && $this->until < $periodEnd)
			$periodEnd = $this->until;

		$this->log("Fetching dates name [$this->name] start-time[" . date('d/n/y G:i:s', $periodStart) . "] end-time[" . date('d/n/y G:i:s', $periodEnd) . "] seed[" . date('d/n/y G:i:s', $seed) . "] max-recurrences [$limit]");
		if(!$seed)
			$seed = $periodStart;

		$dates = array();
		$cal = $seed;
		$calParts = getdate($cal);

		if(!is_null($this->bySecond))
			$calParts['seconds'] = $this->bySecond;

		if(!is_null($this->byMinute))
			$calParts['minutes'] = $this->byMinute;

		if(!is_null($this->byHour))
			$calParts['hours'] = $this->byHour;

		if(!is_null($this->byMonthDay))
			$calParts['mday'] = $this->byMonthDay;

		if(!is_null($this->byMonth))
			$calParts['mon'] = $this->byMonth;

		$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $calParts['mon'], $calParts['year']);
		if ($calParts['mday'] >= $daysInMonth)
			$cal = mktime($calParts['hours'], $calParts['minutes'], $calParts['seconds'], $calParts['mon']+1, 0, $calParts['year']);
		else
			$cal = mktime($calParts['hours'], $calParts['minutes'], $calParts['seconds'], $calParts['mon'], $calParts['mday'], $calParts['year']);

		$this->log("Start calendar [" . date('d/n/y G:i:s', $cal) . "]");
		
		$invalidCandidateCount = 0;
		if($limit && $this->count && $this->count < $limit)
			$limit = $this->count;

		while(!$limit || $limit > count($dates))
		{
			$candidates = $this->getCandidates($cal);
			foreach($candidates as $candidate)
			{
				// don't count candidates that occur before the seed date..
				if($candidate >= $seed)
				{
					if($candidate < $periodStart || $candidate > $periodEnd)
					{
						$invalidCandidateCount++;
					}
					elseif($limit && (count($dates) + $invalidCandidateCount) >= $limit)
					{
						break;
					}
					elseif(!($this->until && $candidate > $this->until))
					{
						$dates[] = $candidate;
					}
				}
			}
			if($this->until && $cal > $this->until)
			{
				$this->log("Calendar [" . date('d/n/y G:i:s', $cal) . "] passed until [" . date('d/n/y G:i:s', $this->until) . "]");
				break;
			}
			if($cal > $periodEnd)
			{
				$this->log("Calendar [" . date('d/n/y G:i:s', $cal) . "] passed period-end [" . date('d/n/y G:i:s', $periodEnd) . "]");
				break;
			}
			if($limit && (count($dates) + $invalidCandidateCount) >= $limit)
			{
				$this->log("Count [" . count($dates) . "] passed limit [$limit]");
				break;
			}


			// We went through all the candidates, and still need more
			// Go to the start of the next time period
			$d = getdate($cal);
			switch($this->frequency)
			{
				case DatesGenerator::MONTHLY:
					$cal = mktime($d['hours'], $d['minutes'], $d['seconds'], $d['mon'] + $this->interval, 1, $d['year']);
					break;

				default:
					$cal = strtotime("+{$this->interval} {$this->frequency}", $cal);
			}
		}
		sort($dates);
		return $dates;
	}


	/**
	 * Get the [optionally formatted] temporal [start_date] column value.
	 *
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid conversations to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getStartDate($format = 'Y-m-d H:i:s')
	{
		if ($this->start_date === null) {
			return null;
		}


		if ($this->start_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->start_date);
			} catch (Exception $x) {
				throw new Exception("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->start_date, true),null,$x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Returns a list of possible dates generated from the applicable BY* rules, using the specified date as a seed.
	 *
	 * @param int the seed date
	 * @return array An array of timestamps
	 */
	public function getCandidates($date)
	{
		$dates = array($date);

		$dates = $this->getMonthVariants($dates);
		$dates = $this->getWeekNoVariants($dates);
		$dates = $this->getYearDayVariants($dates);
		$dates = $this->getMonthDayVariants($dates);
		$dates = $this->getDayVariants($dates);
		$dates = $this->getHourVariants($dates);
		$dates = $this->getMinuteVariants($dates);
		$dates = $this->getSecondVariants($dates);
		$dates = $this->getOffsetVariants($dates);
		return $dates;
	}

	/**
	 * Applies BYSETPOS rules to candidate timestamps.
	 *
	 * Valid positions are from 1 to the size of the array.
	 * Invalid positions are ignored.
	 *
	 * @param array $dates An array of timestamps
	 * @return array An array of timestamps
	 */
	private function getOffsetVariants(array $dates)
	{
		// return if no SETPOS rules specified
		if(!$this->byOffset)
		{
			return $dates;
		}

		// sort the array before processing
		sort($dates);
		$offsetDates = array();
		$size = count($dates);
		$offsets = explode(',', $this->byOffset);
		foreach($offsets as $offset)
		{
			if($offset > 0 && $offset <= $size)
			{
				$offsetDates[] = $dates[$offset - 1];
			}
			elseif($offset < 0 && $offset >= -$size)
			{
				$offsetDates[] = $dates[$size + $offset];
			}
		}
		return $offsetDates;
	}

	/**
	 * Applies BYMONTH rules specified in this Recur instance to the specified date list.
	 *
	 * If no BYMONTH rules are specified the date list is returned unmodified.
	 *
	 * @param array $dates An array of timestamps
	 * @return array An array of timestamps
	 */
	private function getMonthVariants(array $dates)
	{
		if(!$this->byMonth)
		{
			return $dates;
		}
		$months = explode(',', $this->byMonth);
		$monthlyDates = array();
		foreach($dates as $date)
		{
			$currentMonth = date('n', $date);
			foreach($months as $targetMonth)
			{
				if($targetMonth < $currentMonth)
				{
					$targetMonth += 12;
				}
				$distance = $targetMonth - $currentMonth;
				$monthlyDates[] = strtotime("+$distance months", $date);
			}
		}
		return $monthlyDates;
	}

	/**
	 * Applies BYSECOND rules specified in this Recur instance to the specified date list.
	 *
	 * If no BYSECOND rules are specified the date list is returned unmodified.
	 *
	 * @param array $dates An array of timestamps
	 * @return array An array of timestamps
	 */
	private function getSecondVariants(array $dates)
	{
		if(!$this->bySecond)
		{
			return $dates;
		}
		$secondlyDates = array();
		$seconds = explode(',', $this->bySecond);
		foreach($dates as $date)
		{
			$cal = getdate($date);
			foreach($seconds as $second)
			{
				$secondlyDates[] = mktime($cal['hours'], $cal['minutes'], $second, $cal['mon'], $cal['mday'], $cal['year']);
			}
		}
		return $secondlyDates;
	}

	/**
	 * Applies BYWEEKNO rules specified in this Recur instance to the specified date list.
	 *
	 * If no BYWEEKNO rules are specified the date list is returned unmodified.
	 *
	 * @param array $dates An array of timestamps
	 * @return array An array of timestamps
	 */
	private function getWeekNoVariants(array $dates)
	{
		if(!$this->byWeekNumber)
		{
			return $dates;
		}
		$weekNoDates = array();
		$weekNumbers = explode(',', $this->byWeekNumber);
		foreach($dates as $date)
		{
			$cal = getdate($date);
			$year = $cal['year'];
			$currentWeek = date('W', $date);
			$currentWeekDay = date('N', $date) - 1;
			foreach($weekNumbers as $targetWeek)
			{
				$y = ($targetWeek < $currentWeek) ? $year + 1 : $year;
				$week = $this->getWeek($targetWeek, $y);
				$target = getdate($week[$currentWeekDay]);
				$weekNoDates[] = mktime($cal['hours'], $cal['minutes'], $cal['seconds'], $target['mon'], $target['mday'], $target['year']);
			}
		}
		return $weekNoDates;
	}

	/**
	 * Returns an array of timestamps for the given week,year
	 *
	 * Returns timestamps in an array Mon-Sun, 0-6
	 *
	 * @param int $weekNumber
	 * @param int $year
	 */
	private function getWeek($weekNumber, $year)
	{
		// Count from '0104' because January 4th is always in week 1
		// (according to ISO 8601).
		$time = strtotime($year . '0104 +' . ($weekNumber - 1) . ' weeks');
		// Get the time of the first day of the week
		$mondayTime = strtotime('-' . (date('w', $time) - 1) . ' days', $time);
		// Get the times of days 0 -> 6
		$dayTimes = array();
		for($i = 0; $i < 7; ++$i)
		{
			$dayTimes[] = strtotime('+' . $i . ' days', $mondayTime);
		}
		// Return timestamps for mon-sun.
		return $dayTimes;
	}

	/**
	 * Applies BYYEARDAY rules specified in this Recur instance to the specified date list.
	 *
	 * If no BYYEARDAY rules are specified the date list is returned unmodified.
	 *
	 * @param array $dates An array of timestamps
	 * @return array An array of timestamps
	 */
	private function getYearDayVariants(array $dates)
	{
		if(!$this->byYearDay)
		{
			return $dates;
		}
		$yearDayDates = array();
		$yearDays = explode(',', $this->byYearDay);
		foreach($dates as $date)
		{
			// PHP's year days start counting at 0
			// iCalendar starts counding at 1
			$currentYearDay = date('z', $date) + 1;
			$year = date('Y', $date);
			foreach($yearDays as $targetYearDay)
			{
				if($targetYearDay < $currentYearDay)
				{
					$numDays = date('z', mktime(0, 0, 0, 12, 31, $year)) + 1;
					$targetYearDay += $numDays;
				}
				$distance = $targetYearDay - $currentYearDay;
				$yearDayDates[] = strtotime("+$distance days", $date);
			}
		}
		return $yearDayDates;
	}

	/**
	 * Applies BYMONTHDAY rules specified in this Recur instance to the specified date list.
	 *
	 * If no BYMONTHDAY rules are specified the date list is returned unmodified.
	 *
	 * @param array $dates An array of timestamps
	 * @return array An array of timestamps
	 */
	private function getMonthDayVariants(array $dates)
	{
		if(!$this->byMonthDay)
		{
			return $dates;
		}
		$monthDayDates = array();
		$monthDays = explode(',', $this->byMonthDay);
		natsort($monthDays);

		foreach($dates as $date)
		{
			$cal = getdate($date);
			$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $cal['mon'], $cal['year']);

			foreach($monthDays as $monthDay)
			{
				if ($monthDay >= $daysInMonth)
				{
					$monthDayDates[] = mktime($cal['hours'], $cal['minutes'], $cal['seconds'], $cal['mon']+1, 0, $cal['year']);
					break;
				}

				$monthDayDates[] = mktime($cal['hours'], $cal['minutes'], $cal['seconds'], $cal['mon'], $monthDay, $cal['year']);
			}
		}
		return $monthDayDates;
	}

	/**
	 * Applies BYDAY rules specified in this Recur instance to the specified date list.
	 *
	 * If no BYDAY rules are specified the date list is returned unmodified.
	 *
	 * @param array $dates An array of timestamps
	 * @return array An array of timestamps
	 */
	private function getDayVariants(array $dates)
	{
		if(!$this->byDay)
		{
			return $dates;
		}
		$weekDayDates = array();
		$days = explode(',', $this->byDay);

		$order = $this->dayOrder;
		usort($days, function ($a, $b) use ($order) {
			return ($order[$a] - $order[$b]);
		});

		foreach($dates as $date)
		{
			foreach($days as $weekDay)
			{
				// if BYYEARDAY or BYMONTHDAY is specified filter existing list
				if($this->byYearDay || $this->byMonthDay)
				{
					$currentDayOfWeek = date('l', $date);
					if($this->getDayName($weekDay) == $currentDayOfWeek)
					{
						$weekDayDates[] = $date;
					}
				}
				else
				{
					$absDays = $this->getAbsWeekDays($date, $weekDay);
					$weekDayDates = array_merge($weekDayDates, $absDays);
				}
			}
		}
		return $weekDayDates;
	}

	public static $dayNames = array(
		'SU'=>'Sunday',
		'MO'=>'Monday',
		'TU'=>'Tuesday',
		'WE'=>'Wednesday',
		'TH'=>'Thursday',
		'FR'=>'Friday',
		'SA'=>'Saturday'
	);

	private $dayOrder = array(
		'SU'=>0,
		'MO'=>1,
		'TU'=>2,
		'WE'=>3,
		'TH'=>4,
		'FR'=>5,
		'SA'=>6
	);

	private function reOrderDays($first) {
		$diff = $this->dayOrder[$first];
		foreach ($this->dayOrder as $key => $value)
			$this->dayOrder[$key] = ($value - $diff + 7) % 7;
	}

	private function getDayName($day){
		if (strlen($day) > 2) {
			$day = substr($day, -2);
		}

		return self::$dayNames[$day];
	}

	private function getDayOffset($day){
		if (strlen($day) > 2) {
			return (int) substr($day, 0, -2);
		}

		return 0;
	}

	/**
	 * Returns a list of applicable dates corresponding to the specified week day in accordance
	 * with the frequency specified by this recurrence rule.
	 *
	 * @param int $date timestamp
	 * @param string $weekDay
	 * @return array An array of timestamps
	 */
	private function getAbsWeekDays($date, $weekDay)
	{
		$cal = $date;
		$days = array();
		$calDay = $this->getDayName($weekDay);
		if($this->frequency == DatesGenerator::DAILY)
		{
			$current = getdate($cal);
			if($current['weekday'] == $calDay)
			{
				$days[] = $cal;
			}
		}
		elseif($this->frequency == DatesGenerator::WEEKLY || $this->byWeekNumber)
		{
			// Back up to WeekStartDay
			$t = $cal;
			$current = getdate($t);
			$weekStartDay = self::$dayNames[$this->weekStartDay];
			if($current['weekday'] != $weekStartDay)
			{
				$startDay = getdate(strtotime("-1 $weekStartDay", $cal));
				$t = mktime($current['hours'], $current['minutes'], $current['seconds'], $startDay['mon'], $startDay['mday'], $startDay['year']);
			}
			// if wanted day is not startDay then move forward to him
			if ($calDay != $weekStartDay)
			{
				$target = getdate(strtotime("+1 $calDay", $t));
				$t = mktime($current['hours'], $current['minutes'], $current['seconds'], $target['mon'], $target['mday'], $target['year']);
			}

			$days[] = $t;
		}
		elseif($this->frequency == DatesGenerator::MONTHLY || $this->byMonth)
		{
			// Add all of this weekDay's dates for the current month
			$currentMonth = date('n', $cal);
			$t = getdate($cal);
			$cal = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon'], 1, $t['year']);
			if(date('l', $cal) != $calDay)
			{
				// If the first day of the month is not valid,
				// jump ahead to the first valid day
				$target = getdate(strtotime("+1 $calDay", $cal));
				$cal = mktime($t['hours'], $t['minutes'], $t['seconds'], $target['mon'], $target['mday'], $target['year']);
			}
			while(date('n', $cal) == $currentMonth)
			{
				$days[] = $cal;
				$target = getdate(strtotime('+1 week', $cal));
				$cal = mktime($t['hours'], $t['minutes'], $t['seconds'], $target['mon'], $target['mday'], $target['year']);
			}
		}
		elseif($this->frequency == DatesGenerator::YEARLY)
		{
			// Add all of this weekDays dates for the current year
			$current = getdate($cal);
			// Go to the first day of the year
			$cal = mktime($current['hours'], $current['minutes'], $current['seconds'], 1, 1, $current['year']);
			if(!date('l', $cal) == $calDay)
			{
				$target = getdate(strtotime("+1 $calDay", $cal));
				$cal = mktime($current['hours'], $current['minutes'], $current['seconds'], $target['mon'], $target['mday'], $target['year']);
			}
			while(date('Y', $cal) == $current['year'])
			{
				$days[] = $cal;
				$cal = strtotime('+1 week', $cal);
			}
		}
		return $this->getOffsetDates($days, $this->getDayOffset($weekDay));
	}

	/**
	 * Returns a single-element sublist containing the element of $dates at $offset.
	 *
	 * Valid offsets are from 1 to the size of the list.
	 * If an invalid offset is supplied, all elements from $dates are returned
	 *
	 * @param array $dates An array of timestamps
	 * @param int $offset
	 * @return array An array of timestamps
	 */
	private function getOffsetDates(array $dates, $offset)
	{
		if($offset == 0)
		{
			return $dates;
		}
		$offsetDates = array();
		$size = count($dates);
		if($offset < 0 && $offset >= -$size)
		{
			$offsetDates[] = $dates[$size + $offset];
		}
		elseif($offset > 0 && $offset <= $size)
		{
			$offsetDates[] = $dates[$offset - 1];
		}
		return $offsetDates;
	}

	/**
	 * Applies BYHOUR rules specified in this Recur instance to the specified date list.
	 *
	 * If no BYHOUR rules are specified the date list is returned unmodified.
	 *
	 * @param array $dates An array of timestamps
	 * @return array An array of timestamps
	 */
	private function getHourVariants(array $dates)
	{
		if(!$this->byHour)
		{
			return $dates;
		}
		$hourlyDates = array();
		$hours = explode(',', $this->byHour);
		foreach($dates as $date)
		{
			$cal = getdate($date);
			foreach($hours as $hour)
			{
				$hourlyDates[] = mktime($hour, $cal['minutes'], $cal['seconds'], $cal['mon'], $cal['mday'], $cal['year']);
			}
		}
		return $hourlyDates;
	}

	/**
	 * Applies BYMINUTE rules specified in this Recur instance to the specified date list.
	 *
	 * If no BYMINUTE rules are specified the date list is returned unmodified.
	 *
	 * @param array $dates an array of timestamps
	 */
	private function getMinuteVariants(array $dates)
	{
		if(!$this->byMinute)
		{
			return $dates;
		}
		$minutelyDates = array();
		$minutes = explode(',', $this->byMinute);
		foreach($dates as $date)
		{
			$cal = getdate($date);
			foreach($minutes as $minute)
			{
				$minutelyDates[] = mktime($cal['hours'], $minute, $cal['seconds'], $cal['mon'], $cal['mday'], $cal['year']);
			}
		}
		return $minutelyDates;
	}

	private function log($str)
	{
		if ($this->logger)
			call_user_func($this->logger, '[From DatesGenerator] ' .$str);
	}

}