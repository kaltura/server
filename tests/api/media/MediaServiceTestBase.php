<?php

/**
 * media service base test case.
 */
abstract class MediaServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setAddContentActionTestData();
		$this->setAddFromBulkActionTestData();
		$this->setAddFromUrlActionTestData();
		$this->setAddFromSearchResultActionTestData();
		$this->setAddFromUploadedFileActionTestData();
		$this->setAddFromRecordedWebcamActionTestData();
		$this->setAddFromEntryActionTestData();
		$this->setAddFromFlavorAssetActionTestData();
		$this->setConvertActionTestData();
		$this->setGetActionTestData();
		$this->setUpdateActionTestData();
		$this->setUpdateContentActionTestData();
		$this->setDeleteActionTestData();
		$this->setApproveReplaceActionTestData();
		$this->setCancelReplaceActionTestData();
		$this->setListActionTestData();
		$this->setCountActionTestData();
		$this->setUploadActionTestData();
		$this->setUpdateThumbnailActionTestData();
		$this->setUpdateThumbnailFromSourceEntryActionTestData();
		$this->setUpdateThumbnailJpegActionTestData();
		$this->setUpdateThumbnailFromUrlActionTestData();
		$this->setRequestConversionActionTestData();
		$this->setFlagActionTestData();
		$this->setRejectActionTestData();
		$this->setApproveActionTestData();
		$this->setListFlagsTestData();
		$this->setAnonymousRankActionTestData();

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
	 * Set up the testAddFromBulkAction initial data (If needed)
	 */
	protected function setAddFromBulkActionTestData(){}

	/**
	 * Set up the testAddFromUrlAction initial data (If needed)
	 */
	protected function setAddFromUrlActionTestData(){}

	/**
	 * Set up the testAddFromSearchResultAction initial data (If needed)
	 */
	protected function setAddFromSearchResultActionTestData(){}

	/**
	 * Set up the testAddFromUploadedFileAction initial data (If needed)
	 */
	protected function setAddFromUploadedFileActionTestData(){}

	/**
	 * Set up the testAddFromRecordedWebcamAction initial data (If needed)
	 */
	protected function setAddFromRecordedWebcamActionTestData(){}

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
	 * Set up the testUpdateContentAction initial data (If needed)
	 */
	protected function setUpdateContentActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Set up the testApproveReplaceAction initial data (If needed)
	 */
	protected function setApproveReplaceActionTestData(){}

	/**
	 * Set up the testCancelReplaceAction initial data (If needed)
	 */
	protected function setCancelReplaceActionTestData(){}

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
	 * Set up the testUpdateThumbnailAction initial data (If needed)
	 */
	protected function setUpdateThumbnailActionTestData(){}

	/**
	 * Set up the testUpdateThumbnailFromSourceEntryAction initial data (If needed)
	 */
	protected function setUpdateThumbnailFromSourceEntryActionTestData(){}

	/**
	 * Set up the testUpdateThumbnailJpegAction initial data (If needed)
	 */
	protected function setUpdateThumbnailJpegActionTestData(){}

	/**
	 * Set up the testUpdateThumbnailFromUrlAction initial data (If needed)
	 */
	protected function setUpdateThumbnailFromUrlActionTestData(){}

	/**
	 * Set up the testRequestConversionAction initial data (If needed)
	 */
	protected function setRequestConversionActionTestData(){}

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
	 * Tests media->add action
	 * @param KalturaMediaEntry $entry 
	 * @param KalturaMediaEntry $reference 
	 * @return KalturaMediaEntry
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaMediaEntry $entry, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->add($entry);
		$this->assertInstanceOf('KalturaMediaEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($entry, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaMediaEntry $entry, KalturaMediaEntry $reference)
	{
	}

	/**
	 * Tests media->get action
	 * @param string $entryId Media entry id
	 * @param int $version Desired version of the data
	 * @param KalturaMediaEntry $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->get($entryId, $version);
		$this->assertInstanceOf('KalturaMediaEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($entryId, $version, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaMediaEntry $reference)
	{
	}

	/**
	 * Tests media->update action
	 * @param string $entryId Media entry id to update
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata to update
	 * @param KalturaMediaEntry $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaMediaEntry $mediaEntry, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->update($entryId, $mediaEntry);
		$this->assertInstanceOf('KalturaMediaEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($entryId, $mediaEntry, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaMediaEntry $mediaEntry, KalturaMediaEntry $reference)
	{
	}

	/**
	 * Tests media->delete action
	 * @param string $entryId Media entry id to delete
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->media->delete($entryId);
		$this->validateDelete($entryId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
	}

	/**
	 * Tests media->listAction action
	 * @param KalturaMediaEntryFilter $filter Media entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaMediaListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaMediaEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaListResponse $reference)
	{
		$resultObject = $this->client->media->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaMediaListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaMediaEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaListResponse $reference)
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
		return new KalturaTestSuite('MediaServiceTest');
	}

}
