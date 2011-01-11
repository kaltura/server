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
	 * @param KalturaAdminUser $reference
	 * @dataProvider provideData
	 */
	public function testUpdatePassword($email, $password, $newEmail = null, $newPassword = null, KalturaAdminUser $reference)
	{
		$resultObject = $this->client->adminUser->updatePassword($email, $password, $newEmail, $newPassword, $reference);
		$this->assertType('KalturaAdminUser', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests adminUser->resetPassword action
	 * @param string $email
	 * @dataProvider provideData
	 */
	public function testResetPassword($email)
	{
		$resultObject = $this->client->adminUser->resetPassword($email);
		// TODO - add here your own validations
	}

	/**
	 * Tests adminUser->login action
	 * @param string $email
	 * @param string $password
	 * @param int $partnerId
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testLogin($email, $password, $partnerId = null, $reference)
	{
		$resultObject = $this->client->adminUser->login($email, $password, $partnerId, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
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
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
