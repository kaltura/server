<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/FlavorParamsOutputServiceTestBase.php');

/**
 * flavorParamsOutput service test case.
 */
class FlavorParamsOutputServiceTest extends FlavorParamsOutputServiceTestBase
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
	protected function validateGet($id, KalturaFlavorParamsOutput $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsOutputListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

