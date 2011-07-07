<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/CategoryServiceTestBase.php');

/**
 * category service test case.
 */
class CategoryServiceTest extends CategoryServiceTestBase
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
	protected function validateAdd(KalturaCategory $category, KalturaCategory $reference)
	{
		parent::validateAdd($category, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaCategory $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaCategory $category, KalturaCategory $reference)
	{
		parent::validateUpdate($id, $category, $reference);
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
	protected function validateListAction(KalturaCategoryFilter $filter = null, KalturaCategoryListResponse $reference)
	{
		parent::validateListAction($filter, $reference);
		// TODO - add your own validations here
	}

}

