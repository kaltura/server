<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/PermissionItemServiceBaseTest.php');

/**
 * permissionItem service test case.
 */
class PermissionItemServiceTest extends PermissionItemServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaPermissionItem $permissionItem, KalturaPermissionItem $reference)
	{
		parent::validateAdd($permissionItem, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($permissionItemId, KalturaPermissionItem $reference)
	{
		parent::validateGet($permissionItemId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($permissionItemId, KalturaPermissionItem $permissionItem, KalturaPermissionItem $reference)
	{
		parent::validateUpdate($permissionItemId, $permissionItem, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($permissionItemId)
	{
		parent::validateDelete($permissionItemId);
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaPermissionItemFilter $filter = null, KalturaFilterPager $pager = null, KalturaPremissionItemListResponse $reference)
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
