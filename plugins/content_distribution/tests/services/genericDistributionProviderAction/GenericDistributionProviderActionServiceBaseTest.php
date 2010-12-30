<?php

/**
 * genericDistributionProviderAction service base test case.
 */
abstract class GenericDistributionProviderActionServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests genericDistributionProviderAction->add action
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaGenericDistributionProviderAction $genericDistributionProviderAction)
	{
		$resultObject = $this->client->genericDistributionProviderAction->add($genericDistributionProviderAction);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests genericDistributionProviderAction->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->get($id);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests genericDistributionProviderAction->update action
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaGenericDistributionProviderAction $genericDistributionProviderAction, $id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->update($id, $genericDistributionProviderAction);
		$this->assertType('KalturaGenericDistributionProviderAction', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

	/**
	 * Tests genericDistributionProviderAction->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->genericDistributionProviderAction->delete($id);
	}

	/**
	 * Tests genericDistributionProviderAction->list action
	 * @param KalturaGenericDistributionProviderActionFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaGenericDistributionProviderActionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->genericDistributionProviderAction->listAction($filter, $pager);
		$this->assertType('KalturaGenericDistributionProviderActionListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
