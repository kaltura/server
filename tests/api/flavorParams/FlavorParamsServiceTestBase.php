<?php

/**
 * flavorParams service base test case.
 */
abstract class FlavorParamsServiceTestBase extends KalturaApiTestCase
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
		$this->setGetByConversionProfileIdActionTestData();

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
	 * Set up the testGetByConversionProfileIdAction initial data (If needed)
	 */
	protected function setGetByConversionProfileIdActionTestData(){}

	/**
	 * Tests flavorParams->add action
	 * @param KalturaFlavorParams $flavorParams 
	 * @param KalturaFlavorParams $reference 
	 * @return KalturaFlavorParams
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference)
	{
		$resultObject = $this->client->flavorParams->add($flavorParams);
		$this->assertInstanceOf('KalturaFlavorParams', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($flavorParams, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference)
	{
	}

	/**
	 * Tests flavorParams->get action
	 * @param int $id 
	 * @param KalturaFlavorParams $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaFlavorParams $reference)
	{
		$resultObject = $this->client->flavorParams->get($id);
		$this->assertInstanceOf('KalturaFlavorParams', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaFlavorParams $reference)
	{
	}

	/**
	 * Tests flavorParams->update action
	 * @param int $id 
	 * @param KalturaFlavorParams $flavorParams 
	 * @param KalturaFlavorParams $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference)
	{
		$resultObject = $this->client->flavorParams->update($id, $flavorParams);
		$this->assertInstanceOf('KalturaFlavorParams', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $flavorParams, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference)
	{
	}

	/**
	 * Tests flavorParams->delete action
	 * @param int $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->flavorParams->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests flavorParams->listAction action
	 * @param KalturaFlavorParamsFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaFlavorParamsListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaFlavorParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsListResponse $reference)
	{
		$resultObject = $this->client->flavorParams->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaFlavorParamsListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaFlavorParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsListResponse $reference)
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
		return new KalturaTestSuite('FlavorParamsServiceTest');
	}

}
