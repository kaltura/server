<?php

/**
 * entryDistribution service base test case.
 */
abstract class EntryDistributionServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests entryDistribution->add action
	 * @param KalturaEntryDistribution $entryDistribution
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaEntryDistribution $entryDistribution)
	{
		$resultObject = $this->client->entryDistribution->add($entryDistribution);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests entryDistribution->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->entryDistribution->get($id);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests entryDistribution->update action
	 * @param KalturaEntryDistribution $entryDistribution
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaEntryDistribution $entryDistribution, $id)
	{
		$resultObject = $this->client->entryDistribution->update($id, $entryDistribution);
		$this->assertType('KalturaEntryDistribution', $resultObject);
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
	 * Tests entryDistribution->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->entryDistribution->delete($id);
	}

	/**
	 * Tests entryDistribution->list action
	 * @param KalturaEntryDistributionFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaEntryDistributionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->entryDistribution->listAction($filter, $pager);
		$this->assertType('KalturaEntryDistributionListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
