<?php

/**
 * flavorParamsOutput service base test case.
 */
abstract class FlavorParamsOutputServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests flavorParamsOutput->listAction action
	 * @param KalturaFlavorParamsOutputFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaFlavorParamsOutputListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsOutputListResponse $reference)
	{
		$resultObject = $this->client->flavorParamsOutput->listAction($filter, $pager);
		$this->assertType('KalturaFlavorParamsOutputListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsOutputListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
