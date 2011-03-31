<?php

/**
 * data service base test case.
 */
abstract class DataServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests data->add action
	 * @param KalturaDataEntry $dataEntry Data entry
	 * @param KalturaDataEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaDataEntry $dataEntry, KalturaDataEntry $reference)
	{
		$resultObject = $this->client->data->add($dataEntry);
		$this->assertType('KalturaDataEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($dataEntry, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaDataEntry $dataEntry, KalturaDataEntry $reference)
	{
	}

	/**
	 * Tests data->get action
	 * @param string $entryId Data entry id
	 * @param int $version Desired version of the data
	 * @param KalturaDataEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaDataEntry $reference)
	{
		$resultObject = $this->client->data->get($entryId, $version);
		$this->assertType('KalturaDataEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($entryId, $version, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaDataEntry $reference)
	{
	}

	/**
	 * Tests data->update action
	 * @param string $entryId Data entry id to update
	 * @param KalturaDataEntry $documentEntry Data entry metadata to update
	 * @param KalturaDataEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaDataEntry $documentEntry, KalturaDataEntry $reference)
	{
		$resultObject = $this->client->data->update($entryId, $documentEntry);
		$this->assertType('KalturaDataEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($entryId, $documentEntry, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaDataEntry $documentEntry, KalturaDataEntry $reference)
	{
	}

	/**
	 * Tests data->delete action
	 * @param string $entryId Data entry id to delete
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->data->delete($entryId);
		$this->validateDelete($entryId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
	}

	/**
	 * Tests data->listAction action
	 * @param KalturaDataEntryFilter $filter Document entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaDataListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaDataEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaDataListResponse $reference)
	{
		$resultObject = $this->client->data->listAction($filter, $pager);
		$this->assertType('KalturaDataListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDataEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaDataListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
