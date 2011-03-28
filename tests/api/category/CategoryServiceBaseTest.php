<?php

/**
 * category service base test case.
 */
abstract class CategoryServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests category->add action
	 * @param KalturaCategory $category 
	 * @param KalturaCategory $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaCategory $category, KalturaCategory $reference)
	{
		$resultObject = $this->client->category->add($category);
		$this->assertType('KalturaCategory', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($category, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaCategory $category, KalturaCategory $reference)
	{
	}

	/**
	 * Tests category->get action
	 * @param KalturaCategory $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaCategory $reference, $id)
	{
		$resultObject = $this->client->category->get($id);
		$this->assertType('KalturaCategory', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaCategory $reference, $id)
	{
	}

	/**
	 * Tests category->update action
	 * @param KalturaCategory $category 
	 * @param KalturaCategory $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaCategory $category, KalturaCategory $reference, $id)
	{
		$resultObject = $this->client->category->update($id, $category);
		$this->assertType('KalturaCategory', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($category, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaCategory $category, KalturaCategory $reference, $id)
	{
	}

	/**
	 * Tests category->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->category->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests category->listAction action
	 * @param KalturaCategoryFilter $filter 
	 * @param KalturaCategoryListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaCategoryFilter $filter = null, KalturaCategoryListResponse $reference)
	{
		$resultObject = $this->client->category->listAction($filter);
		$this->assertType('KalturaCategoryListResponse', $resultObject);
		$this->validateListAction($filter, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaCategoryFilter $filter = null, KalturaCategoryListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
