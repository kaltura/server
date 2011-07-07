<?php

/**
 * category service base test case.
 */
abstract class CategoryServiceTestBase extends KalturaApiTestCase
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
	 * Tests category->add action
	 * @param KalturaCategory $category 
	 * @param KalturaCategory $reference 
	 * @return KalturaCategory
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaCategory $category, KalturaCategory $reference)
	{
		$resultObject = $this->client->category->add($category);
		$this->assertInstanceOf('KalturaCategory', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($category, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaCategory $category, KalturaCategory $reference)
	{
	}

	/**
	 * Tests category->get action
	 * @param int $id 
	 * @param KalturaCategory $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaCategory $reference)
	{
		$resultObject = $this->client->category->get($id);
		$this->assertInstanceOf('KalturaCategory', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaCategory $reference)
	{
	}

	/**
	 * Tests category->update action
	 * @param int $id 
	 * @param KalturaCategory $category 
	 * @param KalturaCategory $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaCategory $category, KalturaCategory $reference)
	{
		$resultObject = $this->client->category->update($id, $category);
		$this->assertInstanceOf('KalturaCategory', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $category, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaCategory $category, KalturaCategory $reference)
	{
	}

	/**
	 * Tests category->delete action
	 * @param int $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->category->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests category->listAction action
	 * @param KalturaCategoryFilter $filter 
	 * @param KalturaCategoryListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaCategoryFilter $filter = null, KalturaCategoryListResponse $reference)
	{
		$resultObject = $this->client->category->listAction($filter);
		$this->assertInstanceOf('KalturaCategoryListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaCategoryFilter $filter = null, KalturaCategoryListResponse $reference)
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
		return new KalturaTestSuite('CategoryServiceTest');
	}

}
