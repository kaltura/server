<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/CategoryServiceBaseTest.php');

/**
 * category service test case.
 */
class CategoryServiceTest extends CategoryServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaCategory $category, KalturaCategory $reference)
	{
		parent::validateAdd($category, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaCategory $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaCategory $category, KalturaCategory $reference, $id)
	{
		parent::validateUpdate($category, $reference);
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
	protected function validateList(KalturaCategoryFilter $filter = null, KalturaCategoryListResponse $reference)
	{
		parent::validateList($filter, $reference);
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
