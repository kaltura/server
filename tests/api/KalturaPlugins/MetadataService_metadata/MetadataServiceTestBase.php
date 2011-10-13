<?php

/**
 * metadata service base test case.
 */
abstract class MetadataServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setAddFromFileActionTestData();
		$this->setAddFromUrlActionTestData();
		$this->setAddFromBulkActionTestData();
		$this->setGetActionTestData();
		$this->setUpdateActionTestData();
		$this->setUpdateFromFileActionTestData();
		$this->setListActionTestData();
		$this->setDeleteActionTestData();
		$this->setInvalidateActionTestData();
		$this->setServeActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testAddAction initial data (If needed)
	 */
	protected function setAddActionTestData(){}

	/**
	 * Set up the testAddFromFileAction initial data (If needed)
	 */
	protected function setAddFromFileActionTestData(){}

	/**
	 * Set up the testAddFromUrlAction initial data (If needed)
	 */
	protected function setAddFromUrlActionTestData(){}

	/**
	 * Set up the testAddFromBulkAction initial data (If needed)
	 */
	protected function setAddFromBulkActionTestData(){}

	/**
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testUpdateAction initial data (If needed)
	 */
	protected function setUpdateActionTestData(){}

	/**
	 * Set up the testUpdateFromFileAction initial data (If needed)
	 */
	protected function setUpdateFromFileActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Set up the testInvalidateAction initial data (If needed)
	 */
	protected function setInvalidateActionTestData(){}

	/**
	 * Set up the testServeAction initial data (If needed)
	 */
	protected function setServeActionTestData(){}

	/**
	 * Tests metadata->add action
	 * @param int $metadataProfileId 
	 * @param KalturaMetadataObjectType $objectType 
	 * @param string $objectId 
	 * @param string $xmlData XML metadata
	 * @param KalturaMetadata $reference 
	 * @return KalturaMetadata
	 * @dataProvider provideData
	 */
	public function testAdd($metadataProfileId, $objectType, $objectId, $xmlData, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->add($metadataProfileId, $objectType, $objectId, $xmlData);
		$this->assertInstanceOf('KalturaMetadata', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($metadataProfileId, $objectType, $objectId, $xmlData, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($metadataProfileId, $objectType, $objectId, $xmlData, KalturaMetadata $reference)
	{
	}

	/**
	 * Tests metadata->get action
	 * @param int $id 
	 * @param KalturaMetadata $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->get($id);
		$this->assertInstanceOf('KalturaMetadata', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaMetadata $reference)
	{
	}

	/**
	 * Tests metadata->update action
	 * @param int $id 
	 * @param string $xmlData XML metadata
	 * @param int $version Enable update only if the metadata object version did not change by other process
	 * @param KalturaMetadata $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, $xmlData = "", $version = "", KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->update($id, $xmlData, $version);
		$this->assertInstanceOf('KalturaMetadata', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateUpdate($id, $xmlData, $version, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, $xmlData = "", $version = "", KalturaMetadata $reference)
	{
	}

	/**
	 * Tests metadata->listAction action
	 * @param KalturaMetadataFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaMetadataListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataListResponse $reference)
	{
		$resultObject = $this->client->metadata->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaMetadataListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataListResponse $reference)
	{
	}

	/**
	 * Tests metadata->delete action
	 * @param int $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->metadata->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
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
		return new KalturaTestSuite('MetadataServiceTest');
	}

}
