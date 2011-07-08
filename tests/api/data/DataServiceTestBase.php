<?php

/**
 * data service base test case.
 */
abstract class DataServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setGetActionTestData();
		$this->setUpdateActionTestData();
		$this->setDeleteActionTestData();
		$this->setListActionTestData();
		$this->setServeActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testAddAction initial data (If needed)
	 */
	protected function setAddActionTestData(){}

	/**
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testUpdateAction initial data (If needed)
	 */
	protected function setUpdateActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Set up the testServeAction initial data (If needed)
	 */
	protected function setServeActionTestData(){}

	/**
	 * Tests data->add action
	 * @param KalturaDataEntry $dataEntry Data entry
	 * @param KalturaDataEntry $reference 
	 * @return KalturaDataEntry
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaDataEntry $dataEntry, KalturaDataEntry $reference)
	{
		$resultObject = $this->client->data->add($dataEntry);
		$this->assertInstanceOf('KalturaDataEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
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
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaDataEntry $reference)
	{
		$resultObject = $this->client->data->get($entryId, $version);
		$this->assertInstanceOf('KalturaDataEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($entryId, $version, $reference);
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
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaDataEntry $documentEntry, KalturaDataEntry $reference)
	{
		$resultObject = $this->client->data->update($entryId, $documentEntry);
		$this->assertInstanceOf('KalturaDataEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($entryId, $documentEntry, $reference);
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
	 * @depends testAdd
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
		$this->assertInstanceOf('KalturaDataListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
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
	 * TODO: replace testAdd with last test function that uses that id
	 * @depends testAdd
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * 
	 * Returns the suite for the test
	 */
	public static function suite()
	{
		return new KalturaTestSuite('DataServiceTest');
	}

}
