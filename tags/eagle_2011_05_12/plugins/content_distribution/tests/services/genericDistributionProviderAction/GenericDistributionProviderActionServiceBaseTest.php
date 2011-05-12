<?php

/**
 * genericDistributionProviderAction service base test case.
 */
abstract class GenericDistributionProviderActionServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests genericDistributionProviderAction->add action
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction 
	 * @param KalturaGenericDistributionProviderAction $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaGenericDistributionProviderAction $genericDistributionProviderAction, KalturaGenericDistributionProviderAction $reference)
	{
		$resultObject = $this->client->genericDistributionProviderAction->add($genericDistributionProviderAction);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($genericDistributionProviderAction, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaGenericDistributionProviderAction $genericDistributionProviderAction, KalturaGenericDistributionProviderAction $reference)
	{
	}

	/**
	 * Tests genericDistributionProviderAction->get action
	 * @param KalturaGenericDistributionProviderAction $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaGenericDistributionProviderAction $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->get($id);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaGenericDistributionProviderAction $reference, $id)
	{
	}

	/**
	 * Tests genericDistributionProviderAction->update action
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction 
	 * @param KalturaGenericDistributionProviderAction $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaGenericDistributionProviderAction $genericDistributionProviderAction, KalturaGenericDistributionProviderAction $reference, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->update($id, $genericDistributionProviderAction);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($genericDistributionProviderAction, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaGenericDistributionProviderAction $genericDistributionProviderAction, KalturaGenericDistributionProviderAction $reference, $id)
	{
	}

	/**
	 * Tests genericDistributionProviderAction->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests genericDistributionProviderAction->listAction action
	 * @param KalturaGenericDistributionProviderActionFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaGenericDistributionProviderActionListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaGenericDistributionProviderActionFilter $filter = null, KalturaFilterPager $pager = null, KalturaGenericDistributionProviderActionListResponse $reference)
	{
		$resultObject = $this->client->genericDistributionProviderAction->listAction($filter, $pager);
		$this->assertType('KalturaGenericDistributionProviderActionListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaGenericDistributionProviderActionFilter $filter = null, KalturaFilterPager $pager = null, KalturaGenericDistributionProviderActionListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
