<?php

/**
 * partnerAggregation service base test case.
 */
abstract class PartnerAggregationServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests partnerAggregation->listAction action
	 * @param KalturaDwhHourlyPartnerFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaDwhHourlyPartnerListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaDwhHourlyPartnerFilter $filter, KalturaFilterPager $pager = null, KalturaDwhHourlyPartnerListResponse $reference)
	{
		$resultObject = $this->client->partnerAggregation->listAction($filter, $pager);
		$this->assertType('KalturaDwhHourlyPartnerListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDwhHourlyPartnerFilter $filter, KalturaFilterPager $pager = null, KalturaDwhHourlyPartnerListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
