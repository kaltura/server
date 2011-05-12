<?php

/**
 * flavorParams service base test case.
 */
abstract class FlavorParamsServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests flavorParams->add action
	 * @param KalturaFlavorParams $flavorParams 
	 * @param KalturaFlavorParams $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference)
	{
		$resultObject = $this->client->flavorParams->add($flavorParams);
		$this->assertType('KalturaFlavorParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($flavorParams, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference)
	{
	}

	/**
	 * Tests flavorParams->get action
	 * @param KalturaFlavorParams $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaFlavorParams $reference, $id)
	{
		$resultObject = $this->client->flavorParams->get($id);
		$this->assertType('KalturaFlavorParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaFlavorParams $reference, $id)
	{
	}

	/**
	 * Tests flavorParams->update action
	 * @param KalturaFlavorParams $flavorParams 
	 * @param KalturaFlavorParams $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference, $id)
	{
		$resultObject = $this->client->flavorParams->update($id, $flavorParams);
		$this->assertType('KalturaFlavorParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($flavorParams, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference, $id)
	{
	}

	/**
	 * Tests flavorParams->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->flavorParams->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests flavorParams->listAction action
	 * @param KalturaFlavorParamsFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaFlavorParamsListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaFlavorParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsListResponse $reference)
	{
		$resultObject = $this->client->flavorParams->listAction($filter, $pager);
		$this->assertType('KalturaFlavorParamsListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaFlavorParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
