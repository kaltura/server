<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/UserRoleServiceBaseTest.php');

/**
 * userRole service test case.
 */
class UserRoleServiceTest extends UserRoleServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUserRole $userRole, KalturaUserRole $reference)
	{
		parent::validateAdd($userRole, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($userRoleId, KalturaUserRole $reference)
	{
		parent::validateGet($userRoleId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($userRoleId, KalturaUserRole $userRole, KalturaUserRole $reference)
	{
		parent::validateUpdate($userRoleId, $userRole, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($userRoleId)
	{
		parent::validateDelete($userRoleId);
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaUserRoleFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserRoleListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests userRole->clone action
	 * @param int $userRoleId
	 * @param KalturaUserRole $reference
	 * @dataProvider provideData
	 */
	public function testClone($userRoleId, KalturaUserRole $reference)
	{
		$resultObject = $this->client->userRole->clone($userRoleId, $reference);
		$this->assertType('KalturaUserRole', $resultObject);
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
