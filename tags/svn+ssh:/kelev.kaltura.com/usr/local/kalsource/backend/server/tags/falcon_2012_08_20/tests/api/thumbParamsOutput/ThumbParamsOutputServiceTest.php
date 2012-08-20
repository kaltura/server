<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/ThumbParamsOutputServiceTestBase.php');

/**
 * thumbParamsOutput service test case.
 */
class ThumbParamsOutputServiceTest extends ThumbParamsOutputServiceTestBase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaThumbParamsOutput $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsOutputListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

