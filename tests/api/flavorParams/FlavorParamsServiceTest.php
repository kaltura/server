<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/FlavorParamsServiceTestBase.php');

/**
 * flavorParams service test case.
 */
class FlavorParamsServiceTest extends FlavorParamsServiceTestBase
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
	protected function validateAdd(KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference)
	{
		parent::validateAdd($flavorParams, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaFlavorParams $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference)
	{
		parent::validateUpdate($id, $flavorParams, $reference);
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
	protected function validateListAction(KalturaFlavorParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

