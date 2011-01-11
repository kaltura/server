<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/PermissionServiceBaseTest.php');

/**
 * permission service test case.
 */
class PermissionServiceTest extends PermissionServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaPermission $permission, KalturaPermission $reference)
	{
		parent::validateAdd($permission, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($permissionName, KalturaPermission $reference)
	{
		parent::validateGet($permissionName, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($permissionName, KalturaPermission $permission, KalturaPermission $reference)
	{
		parent::validateUpdate($permissionName, $permission, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($permissionName)
	{
		parent::validateDelete($permissionName);
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaPermissionFilter $filter = null, KalturaFilterPager $pager = null, KalturaPermissionListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
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
