<?php

/**
 * category service base test case.
 */
abstract class CategoryServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests category->add action
	 * @param KalturaCategory $category
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaCategory $category)
	{
		$resultObject = $this->client->category->add($category);
		$this->assertType('KalturaCategory', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests category->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->category->get($id);
		$this->assertType('KalturaCategory', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests category->update action
	 * @param KalturaCategory $category
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaCategory $category, $id)
	{
		$resultObject = $this->client->category->update($id, $category);
		$this->assertType('KalturaCategory', $resultObject);
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
	 * Tests category->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->category->delete($id);
	}

	/**
	 * Tests category->list action
	 * @param KalturaCategoryFilter $filter
	 * @dataProvider provideData
	 */
	public function testList(KalturaCategoryFilter $filter = null)
	{
		$resultObject = $this->client->category->listAction($filter);
		$this->assertType('KalturaCategoryListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
