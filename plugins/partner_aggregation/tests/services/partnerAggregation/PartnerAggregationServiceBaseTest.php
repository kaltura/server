<?php

/**
 * partnerAggregation service base test case.
 */
abstract class PartnerAggregationServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests partnerAggregation->list action
	 * @param KalturaDwhHourlyPartnerFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaDwhHourlyPartnerListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaDwhHourlyPartnerFilter $filter, KalturaFilterPager $pager = null, KalturaDwhHourlyPartnerListResponse $reference)
	{
		$resultObject = $this->client->partnerAggregation->list($filter, $pager);
		$this->assertType('KalturaDwhHourlyPartnerListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaDwhHourlyPartnerFilter $filter, KalturaFilterPager $pager = null, KalturaDwhHourlyPartnerListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
