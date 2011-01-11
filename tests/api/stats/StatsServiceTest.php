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
		// TODO - add here your own validations
	}

	/**
	 * Tests stats->kmcCollect action
	 * @param KalturaStatsKmcEvent $kmcEvent
	 * @dataProvider provideData
	 */
	public function testKmcCollect(KalturaStatsKmcEvent $kmcEvent)
	{
		$resultObject = $this->client->stats->kmcCollect($kmcEvent);
		// TODO - add here your own validations
	}

	/**
	 * Tests stats->reportKceError action
	 * @param KalturaCEError $kalturaCEError
	 * @param KalturaCEError $reference
	 * @dataProvider provideData
	 */
	public function testReportKceError(KalturaCEError $kalturaCEError, KalturaCEError $reference)
	{
		$resultObject = $this->client->stats->reportKceError($kalturaCEError, $reference);
		$this->assertType('KalturaCEError', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
