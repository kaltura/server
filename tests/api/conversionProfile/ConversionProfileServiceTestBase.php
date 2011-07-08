<?php

/**
 * conversionProfile service base test case.
 */
abstract class ConversionProfileServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setSetAsDefaultActionTestData();
		$this->setGetDefaultActionTestData();
		$this->setAddActionTestData();
		$this->setGetActionTestData();
		$this->setUpdateActionTestData();
		$this->setDeleteActionTestData();
		$this->setListActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testSetAsDefaultAction initial data (If needed)
	 */
	protected function setSetAsDefaultActionTestData(){}

	/**
	 * Set up the testGetDefaultAction initial data (If needed)
	 */
	protected function setGetDefaultActionTestData(){}

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
	 * Tests conversionProfile->add action
	 * @param KalturaConversionProfile $conversionProfile 
	 * @param KalturaConversionProfile $reference 
	 * @return KalturaConversionProfile
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference)
	{
		$resultObject = $this->client->conversionProfile->add($conversionProfile);
		$this->assertInstanceOf('KalturaConversionProfile', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($conversionProfile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference)
	{
	}

	/**
	 * Tests conversionProfile->get action
	 * @param int $id 
	 * @param KalturaConversionProfile $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaConversionProfile $reference)
	{
		$resultObject = $this->client->conversionProfile->get($id);
		$this->assertInstanceOf('KalturaConversionProfile', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaConversionProfile $reference)
	{
	}

	/**
	 * Tests conversionProfile->update action
	 * @param int $id 
	 * @param KalturaConversionProfile $conversionProfile 
	 * @param KalturaConversionProfile $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference)
	{
		$resultObject = $this->client->conversionProfile->update($id, $conversionProfile);
		$this->assertInstanceOf('KalturaConversionProfile', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $conversionProfile, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference)
	{
	}

	/**
	 * Tests conversionProfile->delete action
	 * @param int $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->conversionProfile->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests conversionProfile->listAction action
	 * @param KalturaConversionProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaConversionProfileListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaConversionProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaConversionProfileListResponse $reference)
	{
		$resultObject = $this->client->conversionProfile->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaConversionProfileListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaConversionProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaConversionProfileListResponse $reference)
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
		return new KalturaTestSuite('ConversionProfileServiceTest');
	}

}
