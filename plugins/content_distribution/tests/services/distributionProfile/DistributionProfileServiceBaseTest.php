<?php

/**
 * distributionProfile service base test case.
 */
abstract class DistributionProfileServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests distributionProfile->add action
	 * @param KalturaDistributionProfile $distributionProfile
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaDistributionProfile $distributionProfile)
	{
		$resultObject = $this->client->distributionProfile->add($distributionProfile);
		$this->assertType('KalturaDistributionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests distributionProfile->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->distributionProfile->get($id);
		$this->assertType('KalturaDistributionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests distributionProfile->update action
	 * @param KalturaDistributionProfile $distributionProfile
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaDistributionProfile $distributionProfile, $id)
	{
		$resultObject = $this->client->distributionProfile->update($id, $distributionProfile);
		$this->assertType('KalturaDistributionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

	/**
	 * Tests distributionProfile->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->distributionProfile->delete($id);
	}

	/**
	 * Tests distributionProfile->list action
	 * @param KalturaDistributionProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaDistributionProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->distributionProfile->listAction($filter, $pager);
		$this->assertType('KalturaDistributionProfileListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
