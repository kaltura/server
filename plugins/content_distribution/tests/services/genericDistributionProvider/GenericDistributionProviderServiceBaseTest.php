<?php

/**
 * genericDistributionProvider service base test case.
 */
abstract class GenericDistributionProviderServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests genericDistributionProvider->add action
	 * @param KalturaGenericDistributionProvider $genericDistributionProvider
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaGenericDistributionProvider $genericDistributionProvider)
	{
		$resultObject = $this->client->genericDistributionProvider->add($genericDistributionProvider);
		$this->assertType('KalturaGenericDistributionProvider', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests genericDistributionProvider->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->genericDistributionProvider->get($id);
		$this->assertType('KalturaGenericDistributionProvider', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests genericDistributionProvider->update action
	 * @param KalturaGenericDistributionProvider $genericDistributionProvider
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaGenericDistributionProvider $genericDistributionProvider, $id)
	{
		$resultObject = $this->client->genericDistributionProvider->update($id, $genericDistributionProvider);
		$this->assertType('KalturaGenericDistributionProvider', $resultObject);
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
	 * Tests genericDistributionProvider->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->genericDistributionProvider->delete($id);
	}

	/**
	 * Tests genericDistributionProvider->list action
	 * @param KalturaGenericDistributionProviderFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaGenericDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->genericDistributionProvider->listAction($filter, $pager);
		$this->assertType('KalturaGenericDistributionProviderListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
