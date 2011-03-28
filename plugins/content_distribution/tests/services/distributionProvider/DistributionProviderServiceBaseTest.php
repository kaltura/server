<?php

/**
 * distributionProvider service base test case.
 */
abstract class DistributionProviderServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests distributionProvider->listAction action
	 * @param KalturaDistributionProviderFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaDistributionProviderListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null, KalturaDistributionProviderListResponse $reference)
	{
		$resultObject = $this->client->distributionProvider->listAction($filter, $pager);
		$this->assertType('KalturaDistributionProviderListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null, KalturaDistributionProviderListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
