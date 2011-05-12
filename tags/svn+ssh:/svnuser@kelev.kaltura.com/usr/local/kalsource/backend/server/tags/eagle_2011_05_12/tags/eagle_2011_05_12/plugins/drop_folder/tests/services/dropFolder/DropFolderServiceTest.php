<?php

require_once(dirname(__FILE__) . '/../../../../../../../tests/base/bootstrap.php');

/**
 * dropFolder service test case.
 */
class DropFolderServiceTest extends DropFolderServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaDropFolder $dropFolder, KalturaDropFolder $reference)
	{
		parent::validateAdd($dropFolder, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($dropFolderId, KalturaDropFolder $reference)
	{
		parent::validateGet($dropFolderId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($dropFolderId, KalturaDropFolder $dropFolder, KalturaDropFolder $reference)
	{
		parent::validateUpdate($dropFolderId, $dropFolder, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($dropFolderId)
	{
		parent::validateDelete($dropFolderId);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDropFolderFilter $filter = null, KalturaFilterPager $pager = null, KalturaDropFolderListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
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
