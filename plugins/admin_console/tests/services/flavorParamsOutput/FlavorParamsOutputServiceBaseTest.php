<?php

/**
 * flavorParamsOutput service base test case.
 */
abstract class FlavorParamsOutputServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests flavorParamsOutput->list action
	 * @param KalturaFlavorParamsOutputFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->flavorParamsOutput->listAction($filter, $pager);
		$this->assertType('KalturaFlavorParamsOutputListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
