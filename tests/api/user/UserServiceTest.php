<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/UserServiceBaseTest.php');

/**
 * user service test case.
 */
class UserServiceTest extends UserServiceBaseTest
{
	/**
	 * Tests user->getByLoginId action
	 * @param string $loginId
	 * @dataProvider provideData
	 */
	public function testGetByLoginId($loginId)
	{
		$resultObject = $this->client->user->getByLoginId($loginId);
		$this->assertType('KalturaUser', $resultObject);
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * Tests user->notifyBan action
	 * @param string $userId
	 * @dataProvider provideData
	 */
	public function testNotifyBan($userId)
	{
		$resultObject = $this->client->user->notifyBan($userId);
	}

	/**
	 * Tests user->login action
	 * @param int $partnerId
	 * @param string $userId
	 * @param string $password
	 * @param int $expiry
	 * @param string $privileges
	 * @dataProvider provideData
	 */
	public function testLogin($partnerId, $userId, $password, $expiry = 86400, $privileges = '*')
	{
		$resultObject = $this->client->user->login($partnerId, $userId, $password, $expiry, $privileges);
		$this->assertType('string', $resultObject);
	}

	/**
	 * Tests user->loginByLoginId action
	 * @param string $loginId
	 * @param string $password
	 * @param int $partnerId
	 * @param int $expiry
	 * @param string $privileges
	 * @dataProvider provideData
	 */
	public function testLoginByLoginId($loginId, $password, $partnerId = null, $expiry = 86400, $privileges = '*')
	{
		$resultObject = $this->client->user->loginByLoginId($loginId, $password, $partnerId, $expiry, $privileges);
		$this->assertType('string', $resultObject);
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
	}

	/**
	 * Tests user->resetPassword action
	 * @param string $email
	 * @dataProvider provideData
	 */
	public function testResetPassword($email)
	{
		$resultObject = $this->client->user->resetPassword($email);
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
	}

	/**
	 * Tests user->enableLogin action
	 * @param string $userId
	 * @param string $loginId
	 * @param string $password
	 * @dataProvider provideData
	 */
	public function testEnableLogin($userId, $loginId, $password = null)
	{
		$resultObject = $this->client->user->enableLogin($userId, $loginId, $password);
		$this->assertType('KalturaUser', $resultObject);
	}

	/**
	 * Tests user->disableLogin action
	 * @param string $userId
	 * @param string $loginId
	 * @dataProvider provideData
	 */
	public function testDisableLogin($userId = null, $loginId = null)
	{
		$resultObject = $this->client->user->disableLogin($userId, $loginId);
		$this->assertType('KalturaUser', $resultObject);
	}

}
