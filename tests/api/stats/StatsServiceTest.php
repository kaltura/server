<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/StatsServiceBaseTest.php');

/**
 * stats service test case.
 */
class StatsServiceTest extends StatsServiceBaseTest
{
	/**
	 * Tests stats->collect action
	 * @param KalturaStatsEvent $event
	 * @dataProvider provideData
	 */
	public function testCollect(KalturaStatsEvent $event)
	{
		$resultObject = $this->client->stats->collect($event);
	}

	/**
	 * Tests stats->kmcCollect action
	 * @param KalturaStatsKmcEvent $kmcEvent
	 * @dataProvider provideData
	 */
	public function testKmcCollect(KalturaStatsKmcEvent $kmcEvent)
	{
		$resultObject = $this->client->stats->kmcCollect($kmcEvent);
	}

	/**
	 * Tests stats->reportKceError action
	 * @param KalturaCEError $kalturaCEError
	 * @dataProvider provideData
	 */
	public function testReportKceError(KalturaCEError $kalturaCEError)
	{
		$resultObject = $this->client->stats->reportKceError($kalturaCEError);
		$this->assertType('KalturaCEError', $resultObject);
	}

}
