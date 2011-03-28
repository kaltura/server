<?php

/**
 * genericDistributionProvider service base test case.
 */
abstract class GenericDistributionProviderServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests genericDistributionProvider->add action
	 * @param KalturaGenericDistributionProvider $genericDistributionProvider 
	 * @param KalturaGenericDistributionProvider $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaGenericDistributionProvider $genericDistributionProvider, KalturaGenericDistributionProvider $reference)
	{
		$resultObject = $this->client->genericDistributionProvider->add($genericDistributionProvider);
		$this->assertType('KalturaGenericDistributionProvider', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($genericDistributionProvider, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaGenericDistributionProvider $genericDistributionProvider, KalturaGenericDistributionProvider $reference)
	{
	}

	/**
	 * Tests genericDistributionProvider->get action
	 * @param KalturaGenericDistributionProvider $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaGenericDistributionProvider $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProvider->get($id);
		$this->assertType('KalturaGenericDistributionProvider', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaGenericDistributionProvider $reference, $id)
	{
	}

	/**
	 * Tests genericDistributionProvider->update action
	 * @param KalturaGenericDistributionProvider $genericDistributionProvider 
	 * @param KalturaGenericDistributionProvider $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaGenericDistributionProvider $genericDistributionProvider, KalturaGenericDistributionProvider $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProvider->update($id, $genericDistributionProvider);
		$this->assertType('KalturaGenericDistributionProvider', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($genericDistributionProvider, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaGenericDistributionProvider $genericDistributionProvider, KalturaGenericDistributionProvider $reference, $id)
	{
	}

	/**
	 * Tests genericDistributionProvider->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->genericDistributionProvider->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests genericDistributionProvider->listAction action
	 * @param KalturaGenericDistributionProviderFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaGenericDistributionProviderListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaGenericDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null, KalturaGenericDistributionProviderListResponse $reference)
	{
		$resultObject = $this->client->genericDistributionProvider->listAction($filter, $pager);
		$this->assertType('KalturaGenericDistributionProviderListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaGenericDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null, KalturaGenericDistributionProviderListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
