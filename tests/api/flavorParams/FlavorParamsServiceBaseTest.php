<?php

/**
 * flavorParams service base test case.
 */
abstract class FlavorParamsServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests flavorParams->add action
	 * @param KalturaFlavorParams $flavorParams
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaFlavorParams $flavorParams)
	{
		$resultObject = $this->client->flavorParams->add($flavorParams);
		$this->assertType('KalturaFlavorParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests flavorParams->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->flavorParams->get($id);
		$this->assertType('KalturaFlavorParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests flavorParams->update action
	 * @param KalturaFlavorParams $flavorParams
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaFlavorParams $flavorParams, $id)
	{
		$resultObject = $this->client->flavorParams->update($id, $flavorParams);
		$this->assertType('KalturaFlavorParams', $resultObject);
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
	 * Tests flavorParams->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->flavorParams->delete($id);
	}

	/**
	 * Tests flavorParams->list action
	 * @param KalturaFlavorParamsFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaFlavorParamsFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->flavorParams->listAction($filter, $pager);
		$this->assertType('KalturaFlavorParamsListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
