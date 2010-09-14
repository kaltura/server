<?php
require_once("tests/bootstrapTests.php");

class StatsTests extends PHPUnit_Framework_TestCase 
{
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
	}
	
	public function testCollect()
	{
		$this->markTestIncomplete("FIXME");
		$statsService = KalturaTestsHelpers::getServiceInitializedForAction("stats", "collect");
		
		$_SERVER['REMOTE_ADDR'] = "127.0.0.1";
		$_SERVER['HTTP_REFERER'] = "referer";
		$event = $this->prepareStatsEvent();
		$result = $statsService->collectAction(clone $event);
		$this->assertTrue($result);
		
		$line = $this->prepareLineForStatsEvent($event);
		$eventLogFullPath = kConf::get("event_log_file_path");
		$handle = fopen($eventLogFullPath, "r");
		$lastLine = "";
		while(!feof($handle))
		{
			$tempLine = fgets($handle, 4096);
			if ($tempLine)
				$lastLine = $tempLine;
		}
		
		$this->assertEquals($line, $lastLine);
	}
	
	private function prepareStatsEvent()
	{
		$event = new KalturaStatsEvent();
		$event->clientVer = KalturaTestsHelpers::getRandomNumber(10,99);
		$event->eventType = KalturaStatsEventType::PLAY;
		$event->eventTimestamp = time();
		$event->sessionId = KalturaTestsHelpers::getRandomString(20);	
		$event->partnerId = KalturaTestsHelpers::getRandomNumber(1000, 9999);
		$event->entryId = KalturaTestsHelpers::getRandomString(5);
		$event->uniqueViewer = KalturaTestsHelpers::getRandomString(20);
		$event->widgetId = KalturaTestsHelpers::getRandomString(5);
		$event->uiconfId = KalturaTestsHelpers::getRandomNumber(1000, 9999);	
		$event->userId = KalturaTestsHelpers::getRandomString(10);
		$event->currentPoint = KalturaTestsHelpers::getRandomNumber(1000, 9999);
		$event->duration = KalturaTestsHelpers::getRandomNumber(100, 999);
		$event->processDuration = KalturaTestsHelpers::getRandomNumber(100, 999);
		$event->controlId = KalturaTestsHelpers::getRandomString(10);	
		$event->seek = KalturaTestsHelpers::getRandomNumber(100, 999);	
		$event->newPoint = KalturaTestsHelpers::getRandomNumber(100, 999);	
		return $event;
	}
	
	private function prepareLineForStatsEvent($event)
	{
		return $event->clientVer . StatsService::SEPARATOR 
			. $event->eventType  . StatsService::SEPARATOR
			. date ( "Y-m-d H:i:s" , $event->eventTimestamp ) . StatsService::SEPARATOR
			. $event->sessionId  . StatsService::SEPARATOR
			. $event->partnerId  . StatsService::SEPARATOR
			. $event->entryId  . StatsService::SEPARATOR
			. $event->uniqueViewer  . StatsService::SEPARATOR
			. $event->widgetId  . StatsService::SEPARATOR
			. $event->uiconfId  . StatsService::SEPARATOR
			. $event->userId  . StatsService::SEPARATOR
			. $event->currentPoint  . StatsService::SEPARATOR
			. $event->duration  . StatsService::SEPARATOR
			. $_SERVER['REMOTE_ADDR']  . StatsService::SEPARATOR
			. $event->processDuration  . StatsService::SEPARATOR
			. $event->controlId  . StatsService::SEPARATOR
			. $event->seek  . StatsService::SEPARATOR
			. $event->newPoint  . StatsService::SEPARATOR
			. $_SERVER['HTTP_REFERER'] 
			. PHP_EOL;
	}
}


