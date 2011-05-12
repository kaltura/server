<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/UserServiceBaseTest.php');

/**
 * user service test case.
 */
class UserServiceTest extends UserServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUser $user, KalturaUser $reference)
	{
		parent::validateAdd($user, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($userId, KalturaUser $user, KalturaUser $reference)
	{
		parent::validateUpdate($userId, $user, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($userId, KalturaUser $reference)
	{
		parent::validateGet($userId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests user->getByLoginId action
	 * @param string $loginId
	 * @param KalturaUser $reference
	 * @dataProvider provideData
	 */
	public function testGetByLoginId($loginId, KalturaUser $reference)
	{
		$resultObject = $this->client->user->getByLoginId($loginId, $reference);
		$this->assertType('KalturaUser', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($userId)
	{
		parent::validateDelete($userId);
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests user->notifyBan action
	 * @param string $userId
	 * @dataProvider provideData
	 */
	public function testNotifyBan($userId)
	{
		$resultObject = $this->client->user->notifyBan($userId);
		// TODO - add here your own validations
	}

	/**
	 * Tests user->login action
	 * @param int $partnerId
	 * @param string $userId
	 * @param string $password
	 * @param int $expiry
	 * @param string $privileges
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testLogin($partnerId, $userId, $password, $expiry = 86400, $privileges = '*', $reference)
	{
		$resultObject = $this->client->user->login($partnerId, $userId, $password, $expiry, $privileges, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests user->loginByLoginId action
	 * @param string $loginId
	 * @param string $password
	 * @param int $partnerId
	 * @param int $expiry
	 * @param string $privileges
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testLoginByLoginId($loginId, $password, $partnerId = null, $expiry = 86400, $privileges = '*', $reference)
	{
		$resultObject = $this->client->user->loginByLoginId($loginId, $password, $partnerId, $expiry, $privileges, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests user->updateLoginData action
	 * @param string $oldLoginId
	 * @param string $password
	 * @param string $newLoginId
	 * @param string $newPassword
	 * @dataProvider provideData
	 */
	public function testUpdateLoginData($oldLoginId, $password, $newLoginId = null, $newPassword = null)
	{
		$resultObject = $this->client->user->updateLoginData($oldLoginId, $password, $newLoginId, $newPassword);
		// TODO - add here your own validations
	}

	/**
	 * Tests user->resetPassword action
	 * @param string $email
	 * @dataProvider provideData
	 */
	public function testResetPassword($email)
	{
		$resultObject = $this->client->user->resetPassword($email);
		// TODO - add here your own validations
	}

	/**
	 * Tests user->setInitialPassword action
	 * @param string $hashKey
	 * @param string $newPassword
	 * @dataProvider provideData
	 */
	public function testSetInitialPassword($hashKey, $newPassword)
	{
		$resultObject = $this->client->user->setInitialPassword($hashKey, $newPassword);
		// TODO - add here your own validations
	}

	/**
	 * Tests user->enableLogin action
	 * @param string $userId
	 * @param string $loginId
	 * @param string $password
	 * @param KalturaUser $reference
	 * @dataProvider provideData
	 */
	public function testEnableLogin($userId, $loginId, $password = null, KalturaUser $reference)
	{
		$resultObject = $this->client->user->enableLogin($userId, $loginId, $password, $reference);
		$this->assertType('KalturaUser', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests user->disableLogin action
	 * @param string $userId
	 * @param string $loginId
	 * @param KalturaUser $reference
	 * @dataProvider provideData
	 */
	public function testDisableLogin($userId = null, $loginId = null, KalturaUser $reference)
	{
		$resultObject = $this->client->user->disableLogin($userId, $loginId, $reference);
		$this->assertType('KalturaUser', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testGet - TODO: replace testGet with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
