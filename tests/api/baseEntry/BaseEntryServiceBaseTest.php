<?php

/**
 * baseEntry service base test case.
 */
abstract class BaseEntryServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests baseEntry->get action
	 * @param string $entryId
	 * @param int $version
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($entryId, $version = -1)
	{
		$resultObject = $this->client->baseEntry->get($entryId, $version);
		$this->assertType('KalturaBaseEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests baseEntry->update action
	 * @param string $entryId
	 * @param KalturaBaseEntry $baseEntry
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaBaseEntry $baseEntry)
	{
		$resultObject = $this->client->baseEntry->update($entryId, $baseEntry);
		$this->assertType('KalturaBaseEntry', $resultObject);
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
	 * Tests baseEntry->delete action
	 * @param string $entryId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->baseEntry->delete($entryId);
	}

	/**
	 * Tests baseEntry->list action
	 * @param KalturaBaseEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->baseEntry->listAction($filter, $pager);
		$this->assertType('KalturaBaseEntryListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
