<?php

/**
 * distributionProvider service base test case.
 */
abstract class DistributionProviderServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests distributionProvider->list action
	 * @param KalturaDistributionProviderFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaDistributionProviderListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null, KalturaDistributionProviderListResponse $reference)
	{
		$resultObject = $this->client->distributionProvider->list($filter, $pager);
		$this->assertType('KalturaDistributionProviderListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null, KalturaDistributionProviderListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
