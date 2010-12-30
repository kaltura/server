<?php

/**
 * media service base test case.
 */
abstract class MediaServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests media->get action
	 * @param string $entryId
	 * @param int $version
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($entryId, $version = -1)
	{
		$resultObject = $this->client->media->get($entryId, $version);
		$this->assertType('KalturaMediaEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests media->update action
	 * @param string $entryId
	 * @param KalturaMediaEntry $mediaEntry
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaMediaEntry $mediaEntry)
	{
		$resultObject = $this->client->media->update($entryId, $mediaEntry);
		$this->assertType('KalturaMediaEntry', $resultObject);
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
	 * Tests media->delete action
	 * @param string $entryId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->media->delete($entryId);
	}

	/**
	 * Tests media->list action
	 * @param KalturaMediaEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaMediaEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->media->listAction($filter, $pager);
		$this->assertType('KalturaMediaListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
