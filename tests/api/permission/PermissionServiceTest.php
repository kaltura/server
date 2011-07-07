<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/PermissionServiceTestBase.php');

/**
 * permission service test case.
 */
class PermissionServiceTest extends PermissionServiceTestBase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
	}

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
	protected function validateDelete($permissionName, KalturaPermission $reference)
	{
		parent::validateDelete($permissionName, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaPermissionFilter $filter = null, KalturaFilterPager $pager = null, KalturaPermissionListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

