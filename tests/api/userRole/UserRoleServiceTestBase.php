<?php

/**
 * userRole service base test case.
 */
abstract class UserRoleServiceTestBase extends KalturaApiTestCase
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
		$this->setCloneActionTestData();

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
	 * Set up the testCloneAction initial data (If needed)
	 */
	protected function setCloneActionTestData(){}

	/**
	 * Tests userRole->add action
	 * @param KalturaUserRole $userRole 
	 * @param KalturaUserRole $reference 
	 * @return KalturaUserRole
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUserRole $userRole, KalturaUserRole $reference)
	{
		$resultObject = $this->client->userRole->add($userRole);
		$this->assertInstanceOf('KalturaUserRole', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($userRole, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUserRole $userRole, KalturaUserRole $reference)
	{
	}

	/**
	 * Tests userRole->get action
	 * @param int $userRoleId 
	 * @param KalturaUserRole $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($userRoleId, KalturaUserRole $reference)
	{
		$resultObject = $this->client->userRole->get($userRoleId);
		$this->assertInstanceOf('KalturaUserRole', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($userRoleId, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($userRoleId, KalturaUserRole $reference)
	{
	}

	/**
	 * Tests userRole->update action
	 * @param int $userRoleId 
	 * @param KalturaUserRole $userRole Id
	 * @param KalturaUserRole $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($userRoleId, KalturaUserRole $userRole, KalturaUserRole $reference)
	{
		$resultObject = $this->client->userRole->update($userRoleId, $userRole);
		$this->assertInstanceOf('KalturaUserRole', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($userRoleId, $userRole, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($userRoleId, KalturaUserRole $userRole, KalturaUserRole $reference)
	{
	}

	/**
	 * Tests userRole->delete action
	 * @param int $userRoleId 
	 * @param KalturaUserRole $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($userRoleId, KalturaUserRole $reference)
	{
		$resultObject = $this->client->userRole->delete($userRoleId);
		$this->assertInstanceOf('KalturaUserRole', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateDelete($userRoleId, $reference);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($userRoleId, KalturaUserRole $reference)
	{
	}

	/**
	 * Tests userRole->listAction action
	 * @param KalturaUserRoleFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaUserRoleListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaUserRoleFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserRoleListResponse $reference)
	{
		$resultObject = $this->client->userRole->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaUserRoleListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaUserRoleFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserRoleListResponse $reference)
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
		return new KalturaTestSuite('UserRoleServiceTest');
	}

}
