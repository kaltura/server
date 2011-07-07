<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/AccessControlServiceTestBase.php');

/**
 * accessControl service test case.
 */
class AccessControlServiceTest extends AccessControlServiceTestBase
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
	protected function validateAdd(KalturaAccessControl $accessControl, KalturaAccessControl $reference)
	{
		parent::validateAdd($accessControl, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaAccessControl $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaAccessControl $accessControl, KalturaAccessControl $reference)
	{
		parent::validateUpdate($id, $accessControl, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete($id);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAccessControlFilter $filter = null, KalturaFilterPager $pager = null, KalturaAccessControlListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

