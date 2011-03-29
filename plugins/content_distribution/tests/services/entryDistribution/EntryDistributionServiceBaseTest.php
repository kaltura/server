<?php

/**
 * entryDistribution service base test case.
 */
abstract class EntryDistributionServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests entryDistribution->add action
	 * @param KalturaEntryDistribution $entryDistribution 
	 * @param KalturaEntryDistribution $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaEntryDistribution $entryDistribution, KalturaEntryDistribution $reference)
	{
		$resultObject = $this->client->entryDistribution->add($entryDistribution);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($entryDistribution, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaEntryDistribution $entryDistribution, KalturaEntryDistribution $reference)
	{
	}

	/**
	 * Tests entryDistribution->get action
	 * @param KalturaEntryDistribution $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaEntryDistribution $reference, $id)
	{
		$resultObject = $this->client->entryDistribution->get($id);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaEntryDistribution $reference, $id)
	{
	}

	/**
	 * Tests entryDistribution->update action
	 * @param KalturaEntryDistribution $entryDistribution 
	 * @param KalturaEntryDistribution $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaEntryDistribution $entryDistribution, KalturaEntryDistribution $reference, $id)
	{
		$resultObject = $this->client->entryDistribution->update($id, $entryDistribution);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($entryDistribution, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaEntryDistribution $entryDistribution, KalturaEntryDistribution $reference, $id)
	{
	}

	/**
	 * Tests entryDistribution->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->entryDistribution->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests entryDistribution->listAction action
	 * @param KalturaEntryDistributionFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaEntryDistributionListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaEntryDistributionFilter $filter = null, KalturaFilterPager $pager = null, KalturaEntryDistributionListResponse $reference)
	{
		$resultObject = $this->client->entryDistribution->listAction($filter, $pager);
		$this->assertType('KalturaEntryDistributionListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaEntryDistributionFilter $filter = null, KalturaFilterPager $pager = null, KalturaEntryDistributionListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
