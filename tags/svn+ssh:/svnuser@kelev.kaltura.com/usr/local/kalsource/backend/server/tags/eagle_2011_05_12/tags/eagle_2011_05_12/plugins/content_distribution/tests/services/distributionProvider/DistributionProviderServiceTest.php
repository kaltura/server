<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/DistributionProviderServiceBaseTest.php');

/**
 * distributionProvider service test case.
 */
class DistributionProviderServiceTest extends DistributionProviderServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null, KalturaDistributionProviderListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
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
