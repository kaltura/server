<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/UserRoleServiceTestBase.php');

/**
 * userRole service test case.
 */
class UserRoleServiceTest extends UserRoleServiceTestBase
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
	protected function validateDelete($userRoleId, KalturaUserRole $reference)
	{
		parent::validateDelete($userRoleId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaUserRoleFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserRoleListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

