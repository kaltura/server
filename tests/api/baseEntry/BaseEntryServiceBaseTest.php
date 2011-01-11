<?php

/**
 * baseEntry service base test case.
 */
abstract class BaseEntryServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests baseEntry->get action
	 * @param string $entryId Entry id
	 * @param int $version Desired version of the data
	 * @param KalturaBaseEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->baseEntry->get($entryId, $version);
		$this->assertType('KalturaBaseEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($entryId, $version, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaBaseEntry $reference)
	{
	}

	/**
	 * Tests baseEntry->update action
	 * @param string $entryId Entry id to update
	 * @param KalturaBaseEntry $baseEntry Base entry metadata to update
	 * @param KalturaBaseEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaBaseEntry $baseEntry, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->baseEntry->update($entryId, $baseEntry);
		$this->assertType('KalturaBaseEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($entryId, $baseEntry, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaBaseEntry $baseEntry, KalturaBaseEntry $reference)
	{
	}

	/**
	 * Tests baseEntry->delete action
	 * @param string $entryId Entry id to delete
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->baseEntry->delete($entryId);
		$this->validateDelete($entryId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
	}

	/**
	 * Tests baseEntry->list action
	 * @param KalturaBaseEntryFilter $filter Entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaBaseEntryListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseEntryListResponse $reference)
	{
		$resultObject = $this->client->baseEntry->list($filter, $pager);
		$this->assertType('KalturaBaseEntryListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseEntryListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
