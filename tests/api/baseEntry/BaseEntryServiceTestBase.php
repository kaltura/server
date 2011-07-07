<?php

/**
 * baseEntry service base test case.
 */
abstract class BaseEntryServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setAddContentActionTestData();
		$this->setAddFromUploadedFileActionTestData();
		$this->setGetActionTestData();
		$this->setUpdateActionTestData();
		$this->setUpdateContentActionTestData();
		$this->setGetByIdsActionTestData();
		$this->setDeleteActionTestData();
		$this->setListActionTestData();
		$this->setCountActionTestData();
		$this->setUploadActionTestData();
		$this->setUpdateThumbnailJpegActionTestData();
		$this->setUpdateThumbnailFromUrlActionTestData();
		$this->setUpdateThumbnailFromSourceEntryActionTestData();
		$this->setFlagActionTestData();
		$this->setRejectActionTestData();
		$this->setApproveActionTestData();
		$this->setListFlagsTestData();
		$this->setAnonymousRankActionTestData();
		$this->setGetContextDataTestData();

		parent::setUp();
	}

	/**
	 * Set up the testAddAction initial data (If needed)
	 */
	protected function setAddActionTestData(){}

	/**
	 * Set up the testAddContentAction initial data (If needed)
	 */
	protected function setAddContentActionTestData(){}

	/**
	 * Set up the testAddFromUploadedFileAction initial data (If needed)
	 */
	protected function setAddFromUploadedFileActionTestData(){}

	/**
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testUpdateAction initial data (If needed)
	 */
	protected function setUpdateActionTestData(){}

	/**
	 * Set up the testUpdateContentAction initial data (If needed)
	 */
	protected function setUpdateContentActionTestData(){}

	/**
	 * Set up the testGetByIdsAction initial data (If needed)
	 */
	protected function setGetByIdsActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Set up the testCountAction initial data (If needed)
	 */
	protected function setCountActionTestData(){}

	/**
	 * Set up the testUploadAction initial data (If needed)
	 */
	protected function setUploadActionTestData(){}

	/**
	 * Set up the testUpdateThumbnailJpegAction initial data (If needed)
	 */
	protected function setUpdateThumbnailJpegActionTestData(){}

	/**
	 * Set up the testUpdateThumbnailFromUrlAction initial data (If needed)
	 */
	protected function setUpdateThumbnailFromUrlActionTestData(){}

	/**
	 * Set up the testUpdateThumbnailFromSourceEntryAction initial data (If needed)
	 */
	protected function setUpdateThumbnailFromSourceEntryActionTestData(){}

	/**
	 * Set up the testFlagAction initial data (If needed)
	 */
	protected function setFlagActionTestData(){}

	/**
	 * Set up the testRejectAction initial data (If needed)
	 */
	protected function setRejectActionTestData(){}

	/**
	 * Set up the testApproveAction initial data (If needed)
	 */
	protected function setApproveActionTestData(){}

	/**
	 * Set up the testListFlags initial data (If needed)
	 */
	protected function setListFlagsTestData(){}

	/**
	 * Set up the testAnonymousRankAction initial data (If needed)
	 */
	protected function setAnonymousRankActionTestData(){}

	/**
	 * Set up the testGetContextData initial data (If needed)
	 */
	protected function setGetContextDataTestData(){}

	/**
	 * Tests baseEntry->add action
	 * @param KalturaBaseEntry $entry 
	 * @param KalturaEntryType $type 
	 * @param KalturaBaseEntry $reference 
	 * @return KalturaBaseEntry
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaBaseEntry $entry, $type = null, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->baseEntry->add($entry, $type);
		$this->assertInstanceOf('KalturaBaseEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($entry, $type, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaBaseEntry $entry, $type = null, KalturaBaseEntry $reference)
	{
	}

	/**
	 * Tests baseEntry->get action
	 * @param string $entryId Entry id
	 * @param int $version Desired version of the data
	 * @param KalturaBaseEntry $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->baseEntry->get($entryId, $version);
		$this->assertInstanceOf('KalturaBaseEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($entryId, $version, $reference);
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
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaBaseEntry $baseEntry, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->baseEntry->update($entryId, $baseEntry);
		$this->assertInstanceOf('KalturaBaseEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($entryId, $baseEntry, $reference);
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
	 * @depends testAdd
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
	 * Tests baseEntry->listAction action
	 * @param KalturaBaseEntryFilter $filter Entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaBaseEntryListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseEntryListResponse $reference)
	{
		$resultObject = $this->client->baseEntry->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaBaseEntryListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseEntryListResponse $reference)
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
		return new KalturaTestSuite('BaseEntryServiceTest');
	}

}
