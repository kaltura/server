<?php

/**
 * thumbAsset service base test case.
 */
abstract class ThumbAssetServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setSetContentActionTestData();
		$this->setUpdateActionTestData();
		$this->setServeByEntryIdActionTestData();
		$this->setServeActionTestData();
		$this->setSetAsDefaultActionTestData();
		$this->setGenerateByEntryIdActionTestData();
		$this->setGenerateActionTestData();
		$this->setRegenerateActionTestData();
		$this->setGetActionTestData();
		$this->setGetByEntryIdActionTestData();
		$this->setListActionTestData();
		$this->setAddFromUrlActionTestData();
		$this->setAddFromImageActionTestData();
		$this->setDeleteActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testAddAction initial data (If needed)
	 */
	protected function setAddActionTestData(){}

	/**
	 * Set up the testSetContentAction initial data (If needed)
	 */
	protected function setSetContentActionTestData(){}

	/**
	 * Set up the testUpdateAction initial data (If needed)
	 */
	protected function setUpdateActionTestData(){}

	/**
	 * Set up the testServeByEntryIdAction initial data (If needed)
	 */
	protected function setServeByEntryIdActionTestData(){}

	/**
	 * Set up the testServeAction initial data (If needed)
	 */
	protected function setServeActionTestData(){}

	/**
	 * Set up the testSetAsDefaultAction initial data (If needed)
	 */
	protected function setSetAsDefaultActionTestData(){}

	/**
	 * Set up the testGenerateByEntryIdAction initial data (If needed)
	 */
	protected function setGenerateByEntryIdActionTestData(){}

	/**
	 * Set up the testGenerateAction initial data (If needed)
	 */
	protected function setGenerateActionTestData(){}

	/**
	 * Set up the testRegenerateAction initial data (If needed)
	 */
	protected function setRegenerateActionTestData(){}

	/**
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testGetByEntryIdAction initial data (If needed)
	 */
	protected function setGetByEntryIdActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Set up the testAddFromUrlAction initial data (If needed)
	 */
	protected function setAddFromUrlActionTestData(){}

	/**
	 * Set up the testAddFromImageAction initial data (If needed)
	 */
	protected function setAddFromImageActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Tests thumbAsset->add action
	 * @param string $entryId 
	 * @param KalturaThumbAsset $thumbAsset 
	 * @param KalturaThumbAsset $reference 
	 * @return KalturaThumbAsset
	 * @dataProvider provideData
	 */
	public function testAdd($entryId, KalturaThumbAsset $thumbAsset, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->add($entryId, $thumbAsset);
		$this->assertInstanceOf('KalturaThumbAsset', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($entryId, $thumbAsset, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($entryId, KalturaThumbAsset $thumbAsset, KalturaThumbAsset $reference)
	{
	}

	/**
	 * Tests thumbAsset->update action
	 * @param string $id 
	 * @param KalturaThumbAsset $thumbAsset 
	 * @param KalturaContentResource $contentResource 
	 * @param KalturaThumbAsset $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource = null, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->update($id, $thumbAsset, $contentResource);
		$this->assertInstanceOf('KalturaThumbAsset', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $thumbAsset, $contentResource, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource = null, KalturaThumbAsset $reference)
	{
	}

	/**
	 * Tests thumbAsset->get action
	 * @param string $thumbAssetId 
	 * @param KalturaThumbAsset $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($thumbAssetId, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->get($thumbAssetId);
		$this->assertInstanceOf('KalturaThumbAsset', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($thumbAssetId, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($thumbAssetId, KalturaThumbAsset $reference)
	{
	}

	/**
	 * Tests thumbAsset->listAction action
	 * @param KalturaAssetFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaThumbAssetListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbAssetListResponse $reference)
	{
		$resultObject = $this->client->thumbAsset->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaThumbAssetListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbAssetListResponse $reference)
	{
	}

	/**
	 * Tests thumbAsset->delete action
	 * @param string $thumbAssetId 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($thumbAssetId)
	{
		$resultObject = $this->client->thumbAsset->delete($thumbAssetId);
		$this->validateDelete($thumbAssetId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($thumbAssetId)
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
		return new KalturaTestSuite('ThumbAssetServiceTest');
	}

}
