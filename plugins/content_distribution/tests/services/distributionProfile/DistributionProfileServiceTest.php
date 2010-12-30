<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/DistributionProfileServiceBaseTest.php');

/**
 * distributionProfile service test case.
 */
class DistributionProfileServiceTest extends DistributionProfileServiceBaseTest
{
	/**
	 * Tests distributionProfile->updateStatus action
	 * @param KalturaDistributionProfileStatus $status
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateStatus(KalturaDistributionProfileStatus $status, $id)
	{
		$resultObject = $this->client->distributionProfile->updateStatus($id, $status);
		$this->assertType('KalturaDistributionProfile', $resultObject);
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * Tests distributionProfile->listByPartner action
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testListByPartner(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->distributionProfile->listByPartner($filter, $pager);
		$this->assertType('KalturaDistributionProfileListResponse', $resultObject);
	}

}
