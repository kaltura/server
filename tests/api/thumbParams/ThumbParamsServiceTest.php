<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/ThumbParamsServiceTestBase.php');

/**
 * thumbParams service test case.
 */
class ThumbParamsServiceTest extends ThumbParamsServiceTestBase
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
	protected function validateAdd(KalturaThumbParams $thumbParams, KalturaThumbParams $reference)
	{
		parent::validateAdd($thumbParams, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaThumbParams $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaThumbParams $thumbParams, KalturaThumbParams $reference)
	{
		parent::validateUpdate($id, $thumbParams, $reference);
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
	protected function validateListAction(KalturaThumbParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

