<?php

/**
 * thumbParams service base test case.
 */
abstract class ThumbParamsServiceTestBase extends KalturaApiTestCase
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
	 * Tests thumbParams->add action
	 * @param KalturaThumbParams $thumbParams 
	 * @param KalturaThumbParams $reference 
	 * @return KalturaThumbParams
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaThumbParams $thumbParams, KalturaThumbParams $reference)
	{
		$resultObject = $this->client->thumbParams->add($thumbParams);
		$this->assertInstanceOf('KalturaThumbParams', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($thumbParams, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaThumbParams $thumbParams, KalturaThumbParams $reference)
	{
	}

	/**
	 * Tests thumbParams->get action
	 * @param int $id 
	 * @param KalturaThumbParams $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaThumbParams $reference)
	{
		$resultObject = $this->client->thumbParams->get($id);
		$this->assertInstanceOf('KalturaThumbParams', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaThumbParams $reference)
	{
	}

	/**
	 * Tests thumbParams->update action
	 * @param int $id 
	 * @param KalturaThumbParams $thumbParams 
	 * @param KalturaThumbParams $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaThumbParams $thumbParams, KalturaThumbParams $reference)
	{
		$resultObject = $this->client->thumbParams->update($id, $thumbParams);
		$this->assertInstanceOf('KalturaThumbParams', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $thumbParams, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaThumbParams $thumbParams, KalturaThumbParams $reference)
	{
	}

	/**
	 * Tests thumbParams->delete action
	 * @param int $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->thumbParams->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests thumbParams->listAction action
	 * @param KalturaThumbParamsFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaThumbParamsListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaThumbParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsListResponse $reference)
	{
		$resultObject = $this->client->thumbParams->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaThumbParamsListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaThumbParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsListResponse $reference)
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
		return new KalturaTestSuite('ThumbParamsServiceTest');
	}

}
