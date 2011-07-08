<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/UserServiceTestBase.php');

/**
 * user service test case.
 */
class UserServiceTest extends UserServiceTestBase
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
	 * Validates testDelete results
	 */
	protected function validateDelete($userId, KalturaUser $reference)
	{
		parent::validateDelete($userId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

