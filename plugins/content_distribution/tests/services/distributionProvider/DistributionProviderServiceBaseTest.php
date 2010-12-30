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
	 * @dataProvider provideData
	 */
	public function testList(KalturaDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->distributionProvider->listAction($filter, $pager);
		$this->assertType('KalturaDistributionProviderListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
