<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */
chdir(dirname( __FILE__ ) . "/../../");
require_once(__DIR__ . "/../../bootstrap.php");

/**
 * @package Scheduler
 * @subpackage Debug
 */
class KAsyncBulkUploadTest extends PHPUnit_Framework_TestCase
{
	const JOB_NAME = 'KAsyncBulkUpload';

	private static $createdRecordsCount = 0;
	private static $errorRecordsCount = 0;
	
	public function setUp() 
	{
		parent::setUp();
	}
	
	public function tearDown() 
	{
		parent::tearDown();
	}
	
	public function getSimleICal($events)
	{
		$content = "BEGIN:VCALENDAR\r\n";
		$content .= "VERSION:1.0\r\n";
		$content .= "PRODID:-//Kaltura/tests//Bulk-Upload//EN\r\n";
		
		$now = time();
		for($i = 1; $i <= $events; $i++) 
		{
			$id = uniqid();
			
			$content .= "BEGIN:VEVENT\r\n";
			$content .= "UID:$id\r\n";
			$content .= "DTSTAMP:" .  kSchedulingICal::formatDate($now). "\r\n";
			$content .= "DTSTART:" .  kSchedulingICal::formatDate($now + (60 * 60 * $i)). "\r\n";
			$content .= "DTEND:" .  kSchedulingICal::formatDate($now + (60 * 60 * ($i + 1))). "\r\n";
			$content .= "SUMMARY:Test $i - $id\r\n";
			$content .= "END:VEVENT\r\n";
		}

		$content .= "END:VCALENDAR\r\n";
		
		return $content;
	}
	
	public function getICal($fields = array())
	{
		$content = "BEGIN:VCALENDAR\r\n";
		$content .= "VERSION:1.0\r\n";
		$content .= "PRODID:-//Kaltura/tests//Bulk-Upload//EN\r\n";
		
		$now = time();
		$id = uniqid();
			
		$content .= "BEGIN:VEVENT\r\n";
		$content .= "UID:$id\r\n";
		$content .= "DTSTAMP:" .  kSchedulingICal::formatDate($now). "\r\n";
		$content .= "DTSTART:" .  kSchedulingICal::formatDate($now + (60 * 60 * 2)). "\r\n";
		$content .= "DTEND:" .  kSchedulingICal::formatDate($now + (60 * 60 * 3)). "\r\n";
		$content .= "SUMMARY:Test $id\r\n";
		
		foreach($fields as $field => $value)
			$content .= "$field:$value\r\n";
		
		$content .= "END:VEVENT\r\n";

		$content .= "END:VCALENDAR\r\n";
		
		return $content;
	}
	
	public function validateICal($content)
	{
		var_dump($content);
		
		$calendar = kSchedulingICal::parse($content, KalturaScheduleEventType::RECORD);
		$components = $calendar->getComponents();
		
		$events = array();
		foreach($components as $component)
		{
			/* @var $component kSchedulingICalEvent */
			$this->assertTrue(is_object($component));
			$this->assertEquals('kSchedulingICalEvent', get_class($component));
			
			$event = $component->toObject();
			$this->assertEquals('KalturaRecordScheduleEvent', get_class($event));
			
			$events[$component->getUid()] = $event;
		}
		var_dump($events);
		
		return $events;
	}
	
	/**
	 * @param string $rule
	 * @return KalturaScheduleEventRecurance
	 */
	public function doTestICalWithRules($rule)
	{
		$content = $this->getICal(array('RRULE' => $rule));
		$events = $this->validateICal($content);
		$event = reset($events);
		/* @var $event KalturaRecordScheduleEvent */
		
		return reset($event->recurances);
	}
	
	public function testICalWithRules()
	{
		$rule = $this->doTestICalWithRules('FREQ=YEARLY;INTERVAL=2;BYMONTH=1;BYDAY=SU;BYHOUR=8,9;BYMINUTE=30');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals(1, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals('8,9', $rule->byHour, "byHour [$rule->byHour]");
		$this->assertEquals(30, $rule->byMinute, "byMinute [$rule->byMinute]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		
		$until = time() + (60 * 60 * 24 * 365 * 6);
		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYMONTH=4;BYDAY=-1SU;UNTIL=' . kSchedulingICal::formatDate($until));
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(4, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('-1SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$until = time() - (60 * 60 * 24 * 365 * 6);
		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU;UNTIL=' . kSchedulingICal::formatDate($until));
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(10, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('-1SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$until = time() - (60 * 60 * 24 * 365 * 3);
		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYMONTH=4;BYDAY=-1SU;UNTIL=' . kSchedulingICal::formatDate($until));
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(4, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('-1SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$until = time() + (60 * 60 * 24 * 365 * 5);
		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYMONTH=4;BYDAY=1SU;UNTIL=' . kSchedulingICal::formatDate($until));
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(4, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('1SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYMONTH=3;BYDAY=2SU');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(3, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('2SU', $rule->byDay, "byDay [$rule->byDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYMONTH=11;BYDAY=1SU');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(11, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('1SU', $rule->byDay, "byDay [$rule->byDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYMONTH=11;BYDAY=1SU');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(11, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('1SU', $rule->byDay, "byDay [$rule->byDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYMONTH=3;BYDAY=2SU');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(3, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('2SU', $rule->byDay, "byDay [$rule->byDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(10, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('-1SU', $rule->byDay, "byDay [$rule->byDay]");
		

		$until = time() + (60 * 60 * 24 * 365);
		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYDAY=1SU;BYMONTH=4;UNTIL=' . kSchedulingICal::formatDate($until));
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(4, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('1SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(10, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('-1SU', $rule->byDay, "byDay [$rule->byDay]");
		

		$until = time() + (60 * 60 * 24 * 365 * 2);
		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYDAY=1SU;BYMONTH=4;UNTIL=' . kSchedulingICal::formatDate($until));
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(4, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('1SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYDAY=-1SU;BYMONTH=4');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(4, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('-1SU', $rule->byDay, "byDay [$rule->byDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=DAILY;COUNT=10');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::DAILY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		

		$until = time() + (60 * 60 * 24 * 365 * 6);
		$rule = $this->doTestICalWithRules('FREQ=DAILY;UNTIL=' . kSchedulingICal::formatDate($until));
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::DAILY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=DAILY;INTERVAL=2');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::DAILY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		

		$rule = $this->doTestICalWithRules('FREQ=DAILY;INTERVAL=10;COUNT=5');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::DAILY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(10, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals(5, $rule->count, "count [$rule->count]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;UNTIL=' . kSchedulingICal::formatDate($until) . ';BYMONTH=1;BYDAY=SU,MO,TU,WE,TH,FR,SA');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=DAILY;UNTIL=' . kSchedulingICal::formatDate($until) . ';BYMONTH=1');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::DAILY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(1, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=WEEKLY;COUNT=10');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::WEEKLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		

		$until = time() + (60 * 60 * 24 * 365 * 6);
		$rule = $this->doTestICalWithRules('FREQ=WEEKLY;UNTIL=' . kSchedulingICal::formatDate($until));
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::WEEKLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=WEEKLY;INTERVAL=2;WKST=SU');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::WEEKLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('SU', $rule->weekStartDay, "weekStartDay [$rule->weekStartDay]");
		

		$until = time() + (60 * 60 * 24 * 365 * 6);
		$rule = $this->doTestICalWithRules('FREQ=WEEKLY;UNTIL=' . kSchedulingICal::formatDate($until) . ';WKST=SU;BYDAY=TU,TH');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::WEEKLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=WEEKLY;COUNT=10;WKST=SU;BYDAY=TU,TH');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::WEEKLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('TU,TH', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		$this->assertEquals('SU', $rule->weekStartDay, "weekStartDay [$rule->weekStartDay]");
		

		$until = time() + (60 * 60 * 24 * 365 * 6);
		$rule = $this->doTestICalWithRules('FREQ=WEEKLY;INTERVAL=2;UNTIL=' . kSchedulingICal::formatDate($until) . ';WKST=SU;BYDAY=MO,WE,FR');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::WEEKLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('MO,WE,FR', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		$this->assertEquals('SU', $rule->weekStartDay, "weekStartDay [$rule->weekStartDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=WEEKLY;INTERVAL=2;COUNT=8;WKST=SU;BYDAY=TU,TH');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::WEEKLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('TU,TH', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(8, $rule->count, "count [$rule->count]");
		$this->assertEquals('SU', $rule->weekStartDay, "weekStartDay [$rule->weekStartDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;COUNT=10;BYDAY=1FR');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('1FR', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		

		$until = time() + (60 * 60 * 24 * 365 * 6);
		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;UNTIL=' . kSchedulingICal::formatDate($until) . ';BYDAY=1FR');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('1FR', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;INTERVAL=2;COUNT=10;BYDAY=1SU,-1SU');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('1SU,-1SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;COUNT=6;BYDAY=-2MO');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('-2MO', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(6, $rule->count, "count [$rule->count]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;BYMONTHDAY=-3');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(-3, $rule->byMonthDay, "byMonthDay [$rule->byMonthDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;COUNT=10;BYMONTHDAY=2,15');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('2,15', $rule->byMonthDay, "byMonthDay [$rule->byMonthDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;COUNT=10;BYMONTHDAY=1,-1');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('1,-1', $rule->byMonthDay, "byMonthDay [$rule->byMonthDay]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;INTERVAL=18;COUNT=10;BYMONTHDAY=10,11,12,13,14,15');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('10,11,12,13,14,15', $rule->byMonthDay, "byMonthDay [$rule->byMonthDay]");
		$this->assertEquals(18, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;INTERVAL=2;BYDAY=TU');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('TU', $rule->byDay, "byDay [$rule->byDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;COUNT=10;BYMONTH=6,7');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('6,7', $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;INTERVAL=2;COUNT=10;BYMONTH=1,2,3');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('1,2,3', $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;INTERVAL=3;COUNT=10;BYYEARDAY=1,100,200');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(3, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals(10, $rule->count, "count [$rule->count]");
		$this->assertEquals('1,100,200', $rule->byYearDay, "byYearDay [$rule->byYearDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYDAY=20MO');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('20MO', $rule->byDay, "byDay [$rule->byDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYWEEKNO=20;BYDAY=MO');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('MO', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(20, $rule->byWeekNumber, "byWeekNumber [$rule->byWeekNumber]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYMONTH=3;BYDAY=TH');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('TH', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(3, $rule->byMonth, "byMonth [$rule->byMonth]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;BYDAY=TH;BYMONTH=6,7,8');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('TH', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals('6,7,8', $rule->byMonth, "byMonth [$rule->byMonth]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;BYDAY=FR;BYMONTHDAY=13');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('FR', $rule->byDay, "byDay [$rule->byDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;BYDAY=SA;BYMONTHDAY=7,8,9,10,11,12,13');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('SA', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals('7,8,9,10,11,12,13', $rule->byMonthDay, "byMonthDay [$rule->byMonthDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=YEARLY;INTERVAL=4;BYMONTH=11;BYDAY=TU;BYMONTHDAY=2,3,4,5,6,7,8');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::YEARLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(4, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('TU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(11, $rule->byMonth, "byMonth [$rule->byMonth]");
		$this->assertEquals('2,3,4,5,6,7,8', $rule->byMonthDay, "byMonthDay [$rule->byMonthDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;COUNT=3;BYDAY=TU,WE,TH;BYSETPOS=3');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('TU,WE,TH', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(3, $rule->count, "count [$rule->count]");
		$this->assertEquals(3, $rule->byOffset, "byOffset [$rule->byOffset]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;BYDAY=MO,TU,WE,TH,FR;BYSETPOS=-2');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('MO,TU,WE,TH,FR', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(-2, $rule->byOffset, "byOffset [$rule->byOffset]");
		

		$until = time() + (60 * 60 * 24 * 365 * 6);
		$rule = $this->doTestICalWithRules('FREQ=HOURLY;INTERVAL=3;UNTIL=' . kSchedulingICal::formatDate($until));
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::HOURLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(3, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals($until, $rule->until, "until [$rule->until]");
		

		$rule = $this->doTestICalWithRules('FREQ=MINUTELY;INTERVAL=15;COUNT=6');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MINUTELY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(15, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals(6, $rule->count, "count [$rule->count]");
		

		$rule = $this->doTestICalWithRules('FREQ=MINUTELY;INTERVAL=90;COUNT=4');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MINUTELY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(90, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals(4, $rule->count, "count [$rule->count]");
		

		$rule = $this->doTestICalWithRules('FREQ=DAILY;BYHOUR=9,10,11,12,13,14,15,16;BYMINUTE=0,20,40');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::DAILY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals('0,20,40', $rule->byMinute, "byMinute [$rule->byMinute]");
		

		$rule = $this->doTestICalWithRules('FREQ=MINUTELY;INTERVAL=20;BYHOUR=9,10,11,12,13,14,15,16');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MINUTELY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(20, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('9,10,11,12,13,14,15,16', $rule->byHour, "byHour [$rule->byHour]");
		

		$rule = $this->doTestICalWithRules('FREQ=WEEKLY;INTERVAL=2;COUNT=4;BYDAY=TU,SU;WKST=MO');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::WEEKLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('TU,SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(4, $rule->count, "count [$rule->count]");
		$this->assertEquals('MO', $rule->weekStartDay, "weekStartDay [$rule->weekStartDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=WEEKLY;INTERVAL=2;COUNT=4;BYDAY=TU,SU;WKST=SU');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::WEEKLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(2, $rule->interval, "interval [$rule->interval]");
		$this->assertEquals('TU,SU', $rule->byDay, "byDay [$rule->byDay]");
		$this->assertEquals(4, $rule->count, "count [$rule->count]");
		$this->assertEquals('SU', $rule->weekStartDay, "weekStartDay [$rule->weekStartDay]");
		

		$rule = $this->doTestICalWithRules('FREQ=MONTHLY;BYMONTHDAY=15,30;COUNT=5');
		$this->assertEquals(KalturaScheduleEventRecuranceFrequency::MONTHLY, $rule->frequency, "frequency [$rule->frequency]");
		$this->assertEquals(5, $rule->count, "count [$rule->count]");
		$this->assertEquals('15,30', $rule->byMonthDay, "byMonthDay [$rule->byMonthDay]");
	}
	
	public function _testSimlpeICal()
	{
		$count = 3;
		$content = $this->getSimleICal($count);
		$events = $this->validateICal($content);
		$this->assertEquals($count, count($events));
	}
	
	public function _testSimlpeICalBulkUpload()
	{
		$count = 3;
		self::$createdRecordsCount = $count;
		$content = $this->getSimleICal($count);
		
		$this->doTest(KalturaBulkUploadType::ICAL, $content);
	}
	
	
	public function doTest($subType, $content, $expectedStatus = KalturaBatchJobStatus::ALMOST_DONE)
	{
		$iniFile = realpath(__DIR__ . "/../../../configurations/batch");
		$schedulerConfig = new KSchedulerConfig($iniFile);
	
		$taskConfigs = $schedulerConfig->getTaskConfigList();
		$config = null;
		foreach($taskConfigs as $taskConfig)
		{
			if($taskConfig->name == self::JOB_NAME)
				$config = $taskConfig;
		}
		$this->assertNotNull($config);
		
		$jobs = $this->prepareJobs($subType, $content);
		
		$config->setTaskIndex(1);
		$instance = new $config->type($config);
		KBatchBase::$kClient->batch = $this;
		$instance->setUnitTest(true);
		$jobs = $instance->run($jobs); 
		$instance->done();
		
		foreach($jobs as $job)
			$this->assertEquals($expectedStatus, $job->status);
	}
	
	private function prepareJobs($subType, $content)
	{
		if($subType == KalturaBulkUploadType::ICAL)
		{
			$data = new KalturaBulkUploadICalJobData();
			$data->eventsType = KalturaScheduleEventType::RECORD;
		}
		
		$data->fileName = 'test';
		$data->filePath = tempnam(sys_get_temp_dir(), 'bulk.');
		file_put_contents($data->filePath, $content);
		
		$job = new KalturaBatchJob();
		$job->id = 1;
		$job->status = KalturaBatchJobStatus::PENDING;
		$job->data = $data;
		$job->jobSubType = $subType;
		
		return array($job);
	}

	public function getBulkUploadLastResult($bulkUploadJobId)
	{
		return null;
	}
	
	function countBulkUploadEntries($bulkUploadJobId, $bulkUploadObjectType = KalturaBulkUploadObjectType::ENTRY)
	{
		$res = array();
		$created = new KalturaKeyValue();
		$created->key = 'created';
		$created->value = self::$createdRecordsCount;
		$res[] = $created;		
		$error = new KalturaKeyValue();
		$error->key = 'error';
		$error->value = self::$errorRecordsCount;
		$res[] = $error;
		
		return $res;
	}
}
