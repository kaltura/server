<?php

class kSchedulingICal
{
	const TIME_FORMAT = 'Ymd\THis\Z';
	const TIME_PARSE = '%Y%m%dT%H%i%sZ';
	
	const METHOD_CANCEL = 'CANCEL';
	
	const TYPE_CALENDAR = 'VCALENDAR';
	const TYPE_EVENT = 'VEVENT';
	
	/**
	 * @param string $data
	 * @param KalturaScheduleEventType $eventsType
	 * @return kSchedulingICalCalendar
	 */
	public static function parse($data, $eventsType)
	{
		return new kSchedulingICalCalendar($data, $eventsType);
	}
	
	/**
	 * @param string $type
	 * @param array $lines
	 * @return kSchedulingICalComponent
	 * @throws Exception
	 */
	public static function parseComponent($type, array &$lines)
	{
		$component = null;
		
		switch($type)
		{
			case self::TYPE_CALENDAR:
				$component = new kSchedulingICalCalendar();
				break;

			case self::TYPE_EVENT:
				$component = new kSchedulingICalEvent();
				break;
				
			default:
				throw new Exception("Component type [$type] is not supported");
		}
		
		$component->parseLines($lines);
		return $component;
	}

	public static function formatDate($time)
	{
		return  date(kSchedulingICal::TIME_FORMAT, $time);
	}
	
	public static function parseDate($str)
	{
		$replace = array(
				'%Y' => '([1-2][0-9]{3})',
				'%m' => '([0-1][0-9])',
				'%d' => '([0-3][0-9])',
				'%H' => '([0-2][0-9])',
				'%i' => '([0-5][0-9])',
				'%s' => '([0-5][0-9])',
				//			'%T' => '([A-Z]{3})',
		);
		
		$arr = null;
		if(!preg_match_all('/%([YmdTHis])/', self::TIME_PARSE, $arr))
			return false;
		
		$fields = $arr[1];		
		$regex = '/' . str_replace(array_keys($replace), $replace, self::TIME_PARSE) . '/';
	
		$values = null;
		if(!preg_match($regex, $str, $values))
			return null;
				
			$hour = 0;
			$minute = 0;
			$second = 0;
			$month = 0;
			$day = 0;
			$year = 0;
			$is_dst = 0;
	
			foreach($fields as $index => $field)
			{
				$value = $values[$index + 1];
					
				switch($field)
				{
					case 'Y':
						$year = intval($value);
						break;
							
					case 'm':
						$month = intval($value);
						break;
							
					case 'd':
						$day = intval($value);
						break;
							
					case 'H':
						$hour = intval($value);
						break;
							
					case 'i':
						$minute = intval($value);
						break;
							
					case 's':
						$second = intval($value);
						break;
							
						//				case 'T':
						//					$date = date_parse($value);
						//					$hour -= ($date['zone'] / 60);
						//					break;
							
				}
			}
	
			$ret = gmmktime($hour, $minute, $second, $month, $day, $year);
			if($ret)
			{
				$timezone = new DateTimeZone(date_default_timezone_get());
				return $ret - $timezone->getOffset(new DateTime());
			}
			
			return null;
	}
}
