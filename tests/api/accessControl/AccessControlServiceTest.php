<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/AccessControlServiceBaseTest.php');

/**
 * accessControl service test case.
 */
class AccessControlServiceTest extends AccessControlServiceBaseTest
{
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
	protected function validateGet(KalturaAccessControl $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaAccessControl $accessControl, KalturaAccessControl $reference, $id)
	{
		parent::validateUpdate($accessControl, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete();
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaAccessControlFilter $filter = null, KalturaFilterPager $pager = null, KalturaAccessControlListResponse $reference)
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
