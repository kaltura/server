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
	 * @param KalturaThumbParamsOutputListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsOutputListResponse $reference)
	{
		$resultObject = $this->client->thumbParamsOutput->list($filter, $pager);
		$this->assertType('KalturaThumbParamsOutputListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsOutputListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
