<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/BulkUploadServiceBaseTest.php');

/**
 * bulkUpload service test case.
 */
class BulkUploadServiceTest extends BulkUploadServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($conversionProfileId, file $csvFileData, KalturaBulkUpload $reference)
	{
		parent::validateAdd($conversionProfileId, $csvFileData, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaBulkUpload $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaFilterPager $pager = null, KalturaBulkUploadListResponse $reference)
	{
		parent::validateList($pager, $reference);
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
