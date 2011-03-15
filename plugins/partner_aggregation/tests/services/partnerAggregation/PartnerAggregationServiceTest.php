<?php

require_once(dirname(__FILE__) . '/../../../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/PartnerAggregationServiceBaseTest.php');

/**
 * partnerAggregation service test case.
 */
class PartnerAggregationServiceTest extends PartnerAggregationServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaDwhHourlyPartnerFilter $filter, KalturaFilterPager $pager = null, KalturaDwhHourlyPartnerListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
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
