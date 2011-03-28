<?php

/**
 * thumbParamsOutput service base test case.
 */
abstract class ThumbParamsOutputServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests thumbParamsOutput->listAction action
	 * @param KalturaThumbParamsOutputFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaThumbParamsOutputListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsOutputListResponse $reference)
	{
		$resultObject = $this->client->thumbParamsOutput->listAction($filter, $pager);
		$this->assertType('KalturaThumbParamsOutputListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsOutputListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
