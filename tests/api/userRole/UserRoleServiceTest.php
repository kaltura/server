<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/UserRoleServiceBaseTest.php');

/**
 * userRole service test case.
 */
class UserRoleServiceTest extends UserRoleServiceBaseTest
{
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
	 * Tests userRole->clone action
	 * @param int $userRoleId
	 * @dataProvider provideData
	 */
	public function testClone($userRoleId)
	{
		$resultObject = $this->client->userRole->clone($userRoleId);
		$this->assertType('KalturaUserRole', $resultObject);
	}

}
