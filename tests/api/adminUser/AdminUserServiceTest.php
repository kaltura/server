<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/AdminUserServiceBaseTest.php');

/**
 * adminUser service test case.
 */
class AdminUserServiceTest extends AdminUserServiceBaseTest
{
	/**
	 * Tests adminUser->updatePassword action
	 * @param string $email
	 * @param string $password
	 * @param string $newEmail
	 * @param string $newPassword
	 * @dataProvider provideData
	 */
	public function testUpdatePassword($email, $password, $newEmail = null, $newPassword = null)
	{
		$resultObject = $this->client->adminUser->updatePassword($email, $password, $newEmail, $newPassword);
		$this->assertType('KalturaAdminUser', $resultObject);
	}

	/**
	 * Tests adminUser->resetPassword action
	 * @param string $email
	 * @dataProvider provideData
	 */
	public function testResetPassword($email)
	{
		$resultObject = $this->client->adminUser->resetPassword($email);
	}

	/**
	 * Tests adminUser->login action
	 * @param string $email
	 * @param string $password
	 * @param int $partnerId
	 * @dataProvider provideData
	 */
	public function testLogin($email, $password, $partnerId = null)
	{
		$resultObject = $this->client->adminUser->login($email, $password, $partnerId);
		$this->assertType('string', $resultObject);
	}

	/**
	 * Tests adminUser->setInitialPassword action
	 * @param string $hashKey
	 * @param string $newPassword
	 * @dataProvider provideData
	 */
	public function testSetInitialPassword($hashKey, $newPassword)
	{
		$resultObject = $this->client->adminUser->setInitialPassword($hashKey, $newPassword);
	}

}
