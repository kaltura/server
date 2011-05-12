<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/DistributionProfileServiceBaseTest.php');

/**
 * distributionProfile service test case.
 */
class DistributionProfileServiceTest extends DistributionProfileServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaDistributionProfile $distributionProfile, KalturaDistributionProfile $reference)
	{
		parent::validateAdd($distributionProfile, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaDistributionProfile $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaDistributionProfile $distributionProfile, KalturaDistributionProfile $reference, $id)
	{
		parent::validateUpdate($distributionProfile, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests distributionProfile->updateStatus action
	 * @param KalturaDistributionProfileStatus $status
	 * @param KalturaDistributionProfile $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateStatus($status, KalturaDistributionProfile $reference, $id)
	{
		$resultObject = $this->client->distributionProfile->updateStatus($id, $status, $reference);
		$this->assertType('KalturaDistributionProfile', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete();
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaDistributionProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaDistributionProfileListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests distributionProfile->listByPartner action
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @param KalturaDistributionProfileListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListByPartner(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null, KalturaDistributionProfileListResponse $reference)
	{
		$resultObject = $this->client->distributionProfile->listByPartner($filter, $pager, $reference);
		$this->assertType('KalturaDistributionProfileListResponse', $resultObject);
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
