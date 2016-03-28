<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaScheduleEventRecurrence extends KalturaObject
{
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var KalturaScheduleEventRecurrenceFrequency
	 */
	public $frequency;
	
	/**
	 * @var time
	 */
	public $until;
	
	/**
	 * @var int
	 */
	public $count;
	
	/**
	 * @var int
	 */
	public $interval;
	
	/**
	 * Comma separated numbers between 0 to 59
	 * @var string 
	 */
	public $bySecond;
	
	/**
	 * Comma separated numbers between 0 to 59
	 * @var string 
	 */
	public $byMinute;
	
	/**
	 * Comma separated numbers between 0 to 23
	 * @var string 
	 */
	public $byHour;
	
	/**
	 * Comma separated of KalturaScheduleEventRecurrenceDay
	 * Each byDay value can also be preceded by a positive (+n) or negative (-n) integer.
	 * If present, this indicates the nth occurrence of the specific day within the MONTHLY or YEARLY RRULE.
	 * For example, within a MONTHLY rule, +1MO (or simply 1MO) represents the first Monday within the month, whereas -1MO represents the last Monday of the month.
	 * If an integer modifier is not present, it means all days of this type within the specified frequency.
	 * For example, within a MONTHLY rule, MO represents all Mondays within the month.
	 * @var string
	 */
	public $byDay;
	
	/**
	 * Comma separated of numbers between -31 to 31, excluding 0.
	 * For example, -10 represents the tenth to the last day of the month.
	 * @var string
	 */
	public $byMonthDay;
	
	/**
	 * Comma separated of numbers between -366 to 366, excluding 0.
	 * For example, -1 represents the last day of the year (December 31st) and -306 represents the 306th to the last day of the year (March 1st).
	 * @var string
	 */
	public $byYearDay;
	
	/**
	 * Comma separated of numbers between -53 to 53, excluding 0.
	 * This corresponds to weeks according to week numbering.
	 * A week is defined as a seven day period, starting on the day of the week defined to be the week start.
	 * Week number one of the calendar year is the first week which contains at least four (4) days in that calendar year.
	 * This rule part is only valid for YEARLY frequency.
	 * For example, 3 represents the third week of the year.
	 * @var string
	 */
	public $byWeekNumber;
	
	/**
	 * Comma separated numbers between 1 to 12
	 * @var string
	 */
	public $byMonth;
	
	/** 
	 * Comma separated of numbers between -366 to 366, excluding 0.
	 * Corresponds to the nth occurrence within the set of events specified by the rule.
	 * It must only be used in conjunction with another by* rule part.
	 * For example "the last work day of the month" could be represented as: frequency=MONTHLY;byDay=MO,TU,WE,TH,FR;byOffset=-1
	 * Each byOffset value can include a positive (+n) or negative (-n) integer.
	 * If present, this indicates the nth occurrence of the specific occurrence within the set of events specified by the rule.
	 * @var string
	 */
	public $byOffset;
	
	/**
	 * @var KalturaScheduleEventRecurrenceDay
	 * Specifies the day on which the workweek starts.
	 * This is significant when a WEEKLY frequency has an interval greater than 1, and a byDay rule part is specified.
	 * This is also significant when in a YEARLY frequency when a byWeekNumber rule part is specified.
	 * The default value is MONDAY.
	 */
	public $weekStartDay;
	
	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(	
		'name',
		'frequency',
		'until',
		'count',
		'interval',
		'bySecond',
		'byMinute',
		'byHour',
		'byDay',
		'byMonthDay',
		'byYearDay',
		'byWeekNumber',
		'byMonth',
		'byOffset',
		'weekStartDay',
	 );
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		if(!$this->isNull('until') && !$this->isNull('count'))
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE, 'until / count');
		}
		
		parent::validateForUsage($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(!$sourceObject)
		{
			$sourceObject = new kScheduleEventRecurrence();
		}
		
		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}
