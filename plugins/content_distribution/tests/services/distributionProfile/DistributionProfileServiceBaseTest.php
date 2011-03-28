<?php

/**
 * distributionProfile service base test case.
 */
abstract class DistributionProfileServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests distributionProfile->add action
	 * @param KalturaDistributionProfile $distributionProfile 
	 * @param KalturaDistributionProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaDistributionProfile $distributionProfile, KalturaDistributionProfile $reference)
	{
		$resultObject = $this->client->distributionProfile->add($distributionProfile);
		$this->assertType('KalturaDistributionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($distributionProfile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaDistributionProfile $distributionProfile, KalturaDistributionProfile $reference)
	{
	}

	/**
	 * Tests distributionProfile->get action
	 * @param KalturaDistributionProfile $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaDistributionProfile $reference, $id)
	{
		$resultObject = $this->client->distributionProfile->get($id);
		$this->assertType('KalturaDistributionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaDistributionProfile $reference, $id)
	{
	}

	/**
	 * Tests distributionProfile->update action
	 * @param KalturaDistributionProfile $distributionProfile 
	 * @param KalturaDistributionProfile $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaDistributionProfile $distributionProfile, KalturaDistributionProfile $reference, $id)
	{
		$resultObject = $this->client->distributionProfile->update($id, $distributionProfile);
		$this->assertType('KalturaDistributionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($distributionProfile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaDistributionProfile $distributionProfile, KalturaDistributionProfile $reference, $id)
	{
	}

	/**
	 * Tests distributionProfile->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->distributionProfile->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests distributionProfile->listAction action
	 * @param KalturaDistributionProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaDistributionProfileListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaDistributionProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaDistributionProfileListResponse $reference)
	{
		$resultObject = $this->client->distributionProfile->listAction($filter, $pager);
		$this->assertType('KalturaDistributionProfileListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDistributionProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaDistributionProfileListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
