<?php

/**
 * document service base test case.
 */
abstract class DocumentServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests document->get action
	 * @param string $entryId Document entry id
	 * @param int $version Desired version of the data
	 * @param KalturaDocumentEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->document->get($entryId, $version);
		$this->assertType('KalturaDocumentEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($entryId, $version, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaDocumentEntry $reference)
	{
	}

	/**
	 * Tests document->update action
	 * @param string $entryId Document entry id to update
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata to update
	 * @param KalturaDocumentEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaDocumentEntry $documentEntry, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->document->update($entryId, $documentEntry);
		$this->assertType('KalturaDocumentEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($entryId, $documentEntry, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaDocumentEntry $documentEntry, KalturaDocumentEntry $reference)
	{
	}

	/**
	 * Tests document->delete action
	 * @param string $entryId Document entry id to delete
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->document->delete($entryId);
		$this->validateDelete($entryId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
	}

	/**
	 * Tests document->listAction action
	 * @param KalturaDocumentEntryFilter $filter Document entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaDocumentListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaDocumentListResponse $reference)
	{
		$resultObject = $this->client->document->listAction($filter, $pager);
		$this->assertType('KalturaDocumentListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaDocumentListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
