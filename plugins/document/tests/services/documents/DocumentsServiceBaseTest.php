<?php

/**
 * documents service base test case.
 */
abstract class DocumentsServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests documents->get action
	 * @param string $entryId
	 * @param int $version
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($entryId, $version = -1)
	{
		$resultObject = $this->client->documents->get($entryId, $version);
		$this->assertType('KalturaDocumentEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests documents->update action
	 * @param string $entryId
	 * @param KalturaDocumentEntry $documentEntry
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaDocumentEntry $documentEntry)
	{
		$resultObject = $this->client->documents->update($entryId, $documentEntry);
		$this->assertType('KalturaDocumentEntry', $resultObject);
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
	 * Tests documents->delete action
	 * @param string $entryId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->documents->delete($entryId);
	}

	/**
	 * Tests documents->list action
	 * @param KalturaDocumentEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->documents->listAction($filter, $pager);
		$this->assertType('KalturaDocumentListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
