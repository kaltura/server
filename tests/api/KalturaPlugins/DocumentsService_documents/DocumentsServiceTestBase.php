<?php

/**
 * documents service base test case.
 */
abstract class DocumentsServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddFromUploadedFileActionTestData();
		$this->setAddFromEntryActionTestData();
		$this->setAddFromFlavorAssetActionTestData();
		$this->setConvertActionTestData();
		$this->setGetActionTestData();
		$this->setUpdateActionTestData();
		$this->setDeleteActionTestData();
		$this->setListActionTestData();
		$this->setUploadActionTestData();
		$this->setConvertPptToSwfTestData();
		$this->setServeActionTestData();
		$this->setServeByFlavorParamsIdActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testAddFromUploadedFileAction initial data (If needed)
	 */
	protected function setAddFromUploadedFileActionTestData(){}

	/**
	 * Set up the testAddFromEntryAction initial data (If needed)
	 */
	protected function setAddFromEntryActionTestData(){}

	/**
	 * Set up the testAddFromFlavorAssetAction initial data (If needed)
	 */
	protected function setAddFromFlavorAssetActionTestData(){}

	/**
	 * Set up the testConvertAction initial data (If needed)
	 */
	protected function setConvertActionTestData(){}

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
	 * Set up the testUploadAction initial data (If needed)
	 */
	protected function setUploadActionTestData(){}

	/**
	 * Set up the testConvertPptToSwf initial data (If needed)
	 */
	protected function setConvertPptToSwfTestData(){}

	/**
	 * Set up the testServeAction initial data (If needed)
	 */
	protected function setServeActionTestData(){}

	/**
	 * Set up the testServeByFlavorParamsIdAction initial data (If needed)
	 */
	protected function setServeByFlavorParamsIdActionTestData(){}

	/**
	 * Tests documents->addFromEntry action
	 * @param string $sourceEntryId Document entry id to copy from
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata
	 * @param int $sourceFlavorParamsId The flavor to be used as the new entry source, source flavor will be used if not specified
	 * @param KalturaDocumentEntry $reference 
	 * @return KalturaDocumentEntry
	 * @dataProvider provideData
	 */
	public function testAddFromEntry($sourceEntryId, KalturaDocumentEntry $documentEntry = null, $sourceFlavorParamsId = "", KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->documents->addFromEntry($sourceEntryId, $documentEntry, $sourceFlavorParamsId);
		$this->assertInstanceOf('KalturaDocumentEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateAddFromEntry($sourceEntryId, $documentEntry, $sourceFlavorParamsId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAddFromEntry results
	 */
	protected function validateAddFromEntry($sourceEntryId, KalturaDocumentEntry $documentEntry = null, $sourceFlavorParamsId = "", KalturaDocumentEntry $reference)
	{
	}

	/**
	 * Tests documents->get action
	 * @param string $entryId Document entry id
	 * @param int $version Desired version of the data
	 * @param KalturaDocumentEntry $reference 
	 * @depends testAddFromEntry
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->documents->get($entryId, $version);
		$this->assertInstanceOf('KalturaDocumentEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($entryId, $version, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaDocumentEntry $reference)
	{
	}

	/**
	 * Tests documents->update action
	 * @param string $entryId Document entry id to update
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata to update
	 * @param KalturaDocumentEntry $reference 
	 * @depends testAddFromEntry
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaDocumentEntry $documentEntry, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->documents->update($entryId, $documentEntry);
		$this->assertInstanceOf('KalturaDocumentEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateUpdate($entryId, $documentEntry, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaDocumentEntry $documentEntry, KalturaDocumentEntry $reference)
	{
	}

	/**
	 * Tests documents->delete action
	 * @param string $entryId Document entry id to delete
	 * @depends testAddFromEntry
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->documents->delete($entryId);
		$this->validateDelete($entryId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
	}

	/**
	 * Tests documents->listAction action
	 * @param KalturaDocumentEntryFilter $filter Document entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaDocumentListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaDocumentListResponse $reference)
	{
		$resultObject = $this->client->documents->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaDocumentListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
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
	 * TODO: replace testAddFromEntry with last test function that uses that id
	 * @depends testAddFromEntry
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
		return new KalturaTestSuite('DocumentsServiceTest');
	}

}
