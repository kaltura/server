<?php

/**
 * accessControl service base test case.
 */
abstract class AccessControlServiceTestBase extends KalturaApiTestCase
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
	 * Tests accessControl->add action
	 * @param KalturaAccessControl $accessControl 
	 * @param KalturaAccessControl $reference 
	 * @return KalturaAccessControl
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaAccessControl $accessControl, KalturaAccessControl $reference)
	{
		$resultObject = $this->client->accessControl->add($accessControl);
		$this->assertInstanceOf('KalturaAccessControl', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($accessControl, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaAccessControl $accessControl, KalturaAccessControl $reference)
	{
	}

	/**
	 * Tests accessControl->get action
	 * @param int $id 
	 * @param KalturaAccessControl $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaAccessControl $reference)
	{
		$resultObject = $this->client->accessControl->get($id);
		$this->assertInstanceOf('KalturaAccessControl', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaAccessControl $reference)
	{
	}

	/**
	 * Tests accessControl->update action
	 * @param int $id 
	 * @param KalturaAccessControl $accessControl 
	 * @param KalturaAccessControl $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaAccessControl $accessControl, KalturaAccessControl $reference)
	{
		$resultObject = $this->client->accessControl->update($id, $accessControl);
		$this->assertInstanceOf('KalturaAccessControl', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $accessControl, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaAccessControl $accessControl, KalturaAccessControl $reference)
	{
	}

	/**
	 * Tests accessControl->delete action
	 * @param int $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->accessControl->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests accessControl->listAction action
	 * @param KalturaAccessControlFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaAccessControlListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaAccessControlFilter $filter = null, KalturaFilterPager $pager = null, KalturaAccessControlListResponse $reference)
	{
		$resultObject = $this->client->accessControl->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaAccessControlListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAccessControlFilter $filter = null, KalturaFilterPager $pager = null, KalturaAccessControlListResponse $reference)
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
		return new KalturaTestSuite('AccessControlServiceTest');
	}

}
