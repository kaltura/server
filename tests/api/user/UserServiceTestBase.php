<?php

/**
 * user service base test case.
 */
abstract class UserServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setUpdateActionTestData();
		$this->setGetActionTestData();
		$this->setGetByLoginIdActionTestData();
		$this->setDeleteActionTestData();
		$this->setListActionTestData();
		$this->setNotifyBanTestData();
		$this->setLoginActionTestData();
		$this->setLoginByLoginIdActionTestData();
		$this->setUpdateLoginDataActionTestData();
		$this->setResetPasswordActionTestData();
		$this->setSetInitialPasswordActionTestData();
		$this->setEnableLoginActionTestData();
		$this->setDisableLoginActionTestData();

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
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testGetByLoginIdAction initial data (If needed)
	 */
	protected function setGetByLoginIdActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Set up the testNotifyBan initial data (If needed)
	 */
	protected function setNotifyBanTestData(){}

	/**
	 * Set up the testLoginAction initial data (If needed)
	 */
	protected function setLoginActionTestData(){}

	/**
	 * Set up the testLoginByLoginIdAction initial data (If needed)
	 */
	protected function setLoginByLoginIdActionTestData(){}

	/**
	 * Set up the testUpdateLoginDataAction initial data (If needed)
	 */
	protected function setUpdateLoginDataActionTestData(){}

	/**
	 * Set up the testResetPasswordAction initial data (If needed)
	 */
	protected function setResetPasswordActionTestData(){}

	/**
	 * Set up the testSetInitialPasswordAction initial data (If needed)
	 */
	protected function setSetInitialPasswordActionTestData(){}

	/**
	 * Set up the testEnableLoginAction initial data (If needed)
	 */
	protected function setEnableLoginActionTestData(){}

	/**
	 * Set up the testDisableLoginAction initial data (If needed)
	 */
	protected function setDisableLoginActionTestData(){}

	/**
	 * Tests user->add action
	 * @param KalturaUser $user 
	 * @param KalturaUser $reference 
	 * @return KalturaUser
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUser $user, KalturaUser $reference)
	{
		$resultObject = $this->client->user->add($user);
		$this->assertInstanceOf('KalturaUser', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($user, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUser $user, KalturaUser $reference)
	{
	}

	/**
	 * Tests user->update action
	 * @param string $userId 
	 * @param KalturaUser $user Id
	 * @param KalturaUser $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($userId, KalturaUser $user, KalturaUser $reference)
	{
		$resultObject = $this->client->user->update($userId, $user);
		$this->assertInstanceOf('KalturaUser', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($userId, $user, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($userId, KalturaUser $user, KalturaUser $reference)
	{
	}

	/**
	 * Tests user->get action
	 * @param string $userId 
	 * @param KalturaUser $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($userId, KalturaUser $reference)
	{
		$resultObject = $this->client->user->get($userId);
		$this->assertInstanceOf('KalturaUser', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($userId, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($userId, KalturaUser $reference)
	{
	}

	/**
	 * Tests user->delete action
	 * @param string $userId 
	 * @param KalturaUser $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($userId, KalturaUser $reference)
	{
		$resultObject = $this->client->user->delete($userId);
		$this->assertInstanceOf('KalturaUser', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateDelete($userId, $reference);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($userId, KalturaUser $reference)
	{
	}

	/**
	 * Tests user->listAction action
	 * @param KalturaUserFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaUserListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserListResponse $reference)
	{
		$resultObject = $this->client->user->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaUserListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserListResponse $reference)
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
		return new KalturaTestSuite('UserServiceTest');
	}

}
