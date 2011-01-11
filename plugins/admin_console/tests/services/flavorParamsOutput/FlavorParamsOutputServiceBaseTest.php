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
	 * @param KalturaFlavorParamsOutputListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsOutputListResponse $reference)
	{
		$resultObject = $this->client->flavorParamsOutput->list($filter, $pager);
		$this->assertType('KalturaFlavorParamsOutputListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsOutputListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
