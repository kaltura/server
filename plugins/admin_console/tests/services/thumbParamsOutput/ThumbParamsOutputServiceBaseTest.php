<?php

/**
 * thumbParamsOutput service base test case.
 */
abstract class ThumbParamsOutputServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests thumbParamsOutput->list action
	 * @param KalturaThumbParamsOutputFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->thumbParamsOutput->listAction($filter, $pager);
		$this->assertType('KalturaThumbParamsOutputListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
