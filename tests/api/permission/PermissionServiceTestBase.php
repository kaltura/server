<?php

/**
 * permission service base test case.
 */
abstract class PermissionServiceTestBase extends KalturaApiTestCase
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
		$this->setGetCurrentPermissionsTestData();

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
	 * Set up the testGetCurrentPermissions initial data (If needed)
	 */
	protected function setGetCurrentPermissionsTestData(){}

	/**
	 * Tests permission->add action
	 * @param KalturaPermission $permission 
	 * @param KalturaPermission $reference 
	 * @return KalturaPermission
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaPermission $permission, KalturaPermission $reference)
	{
		$resultObject = $this->client->permission->add($permission);
		$this->assertInstanceOf('KalturaPermission', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($permission, $reference);
		return $resultObject->name;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaPermission $permission, KalturaPermission $reference)
	{
	}

	/**
	 * Tests permission->get action
	 * @param string $permissionName 
	 * @param KalturaPermission $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($permissionName, KalturaPermission $reference)
	{
		$resultObject = $this->client->permission->get($permissionName);
		$this->assertInstanceOf('KalturaPermission', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($permissionName, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($permissionName, KalturaPermission $reference)
	{
	}

	/**
	 * Tests permission->update action
	 * @param string $permissionName 
	 * @param KalturaPermission $permission Name
	 * @param KalturaPermission $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($permissionName, KalturaPermission $permission, KalturaPermission $reference)
	{
		$resultObject = $this->client->permission->update($permissionName, $permission);
		$this->assertInstanceOf('KalturaPermission', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($permissionName, $permission, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($permissionName, KalturaPermission $permission, KalturaPermission $reference)
	{
	}

	/**
	 * Tests permission->delete action
	 * @param string $permissionName 
	 * @param KalturaPermission $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($permissionName, KalturaPermission $reference)
	{
		$resultObject = $this->client->permission->delete($permissionName);
		$this->assertInstanceOf('KalturaPermission', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateDelete($permissionName, $reference);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($permissionName, KalturaPermission $reference)
	{
	}

	/**
	 * Tests permission->listAction action
	 * @param KalturaPermissionFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaPermissionListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaPermissionFilter $filter = null, KalturaFilterPager $pager = null, KalturaPermissionListResponse $reference)
	{
		$resultObject = $this->client->permission->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaPermissionListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaPermissionFilter $filter = null, KalturaFilterPager $pager = null, KalturaPermissionListResponse $reference)
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
		return new KalturaTestSuite('PermissionServiceTest');
	}

}
