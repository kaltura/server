<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/AuditTrailServiceBaseTest.php');

/**
 * auditTrail service test case.
 */
class AuditTrailServiceTest extends AuditTrailServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaAuditTrailFilter $filter = null, KalturaFilterPager $pager = null, KalturaAuditTrailListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaAuditTrail $auditTrail, KalturaAuditTrail $reference)
	{
		parent::validateAdd($auditTrail, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaAuditTrail $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testGet - TODO: replace testGet with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
