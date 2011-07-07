<?php

/**
 * flavorAsset service base test case.
 */
abstract class FlavorAssetServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setUpdateActionTestData();
		$this->setSetContentActionTestData();
		$this->setGetActionTestData();
		$this->setGetByEntryIdActionTestData();
		$this->setListActionTestData();
		$this->setGetWebPlayableByEntryIdActionTestData();
		$this->setConvertActionTestData();
		$this->setReconvertActionTestData();
		$this->setDeleteActionTestData();
		$this->setGetDownloadUrlActionTestData();
		$this->setGetFlavorAssetsWithParamsActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testAddAction initial data (If needed)
	 */
	protected function setAddActionTestData(){}

	/**
	 * Set up the testUpdateAction initial data (If needed)
	 */
	protected function setUpdateActionTestData(){}

	/**
	 * Set up the testSetContentAction initial data (If needed)
	 */
	protected function setSetContentActionTestData(){}

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
	 * Set up the testGetWebPlayableByEntryIdAction initial data (If needed)
	 */
	protected function setGetWebPlayableByEntryIdActionTestData(){}

	/**
	 * Set up the testConvertAction initial data (If needed)
	 */
	protected function setConvertActionTestData(){}

	/**
	 * Set up the testReconvertAction initial data (If needed)
	 */
	protected function setReconvertActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Set up the testGetDownloadUrlAction initial data (If needed)
	 */
	protected function setGetDownloadUrlActionTestData(){}

	/**
	 * Set up the testGetFlavorAssetsWithParamsAction initial data (If needed)
	 */
	protected function setGetFlavorAssetsWithParamsActionTestData(){}

	/**
	 * Tests flavorAsset->add action
	 * @param string $entryId 
	 * @param KalturaFlavorAsset $flavorAsset 
	 * @param KalturaFlavorAsset $reference 
	 * @return KalturaFlavorAsset
	 * @dataProvider provideData
	 */
	public function testAdd($entryId, KalturaFlavorAsset $flavorAsset, KalturaFlavorAsset $reference)
	{
		$resultObject = $this->client->flavorAsset->add($entryId, $flavorAsset);
		$this->assertInstanceOf('KalturaFlavorAsset', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($entryId, $flavorAsset, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($entryId, KalturaFlavorAsset $flavorAsset, KalturaFlavorAsset $reference)
	{
	}

	/**
	 * Tests flavorAsset->update action
	 * @param string $id 
	 * @param KalturaFlavorAsset $flavorAsset 
	 * @param KalturaFlavorAsset $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaFlavorAsset $flavorAsset, KalturaFlavorAsset $reference)
	{
		$resultObject = $this->client->flavorAsset->update($id, $flavorAsset);
		$this->assertInstanceOf('KalturaFlavorAsset', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $flavorAsset, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaFlavorAsset $flavorAsset, KalturaFlavorAsset $reference)
	{
	}

	/**
	 * Tests flavorAsset->get action
	 * @param string $id 
	 * @param KalturaFlavorAsset $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaFlavorAsset $reference)
	{
		$resultObject = $this->client->flavorAsset->get($id);
		$this->assertInstanceOf('KalturaFlavorAsset', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaFlavorAsset $reference)
	{
	}

	/**
	 * Tests flavorAsset->listAction action
	 * @param KalturaAssetFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaFlavorAssetListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorAssetListResponse $reference)
	{
		$resultObject = $this->client->flavorAsset->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaFlavorAssetListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorAssetListResponse $reference)
	{
	}

	/**
	 * Tests flavorAsset->delete action
	 * @param string $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->flavorAsset->delete($id);
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
		return new KalturaTestSuite('FlavorAssetServiceTest');
	}

}
