<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/FlavorParamsOutputServiceBaseTest.php');

/**
 * flavorParamsOutput service test case.
 */
class FlavorParamsOutputServiceTest extends FlavorParamsOutputServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsOutputListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
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
