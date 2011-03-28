<?php

/**
 * thumbParams service base test case.
 */
abstract class ThumbParamsServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests thumbParams->add action
	 * @param KalturaThumbParams $thumbParams 
	 * @param KalturaThumbParams $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaThumbParams $thumbParams, KalturaThumbParams $reference)
	{
		$resultObject = $this->client->thumbParams->add($thumbParams);
		$this->assertType('KalturaThumbParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($thumbParams, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaThumbParams $thumbParams, KalturaThumbParams $reference)
	{
	}

	/**
	 * Tests thumbParams->get action
	 * @param KalturaThumbParams $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaThumbParams $reference, $id)
	{
		$resultObject = $this->client->thumbParams->get($id);
		$this->assertType('KalturaThumbParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaThumbParams $reference, $id)
	{
	}

	/**
	 * Tests thumbParams->update action
	 * @param KalturaThumbParams $thumbParams 
	 * @param KalturaThumbParams $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaThumbParams $thumbParams, KalturaThumbParams $reference, $id)
	{
		$resultObject = $this->client->thumbParams->update($id, $thumbParams);
		$this->assertType('KalturaThumbParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($thumbParams, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaThumbParams $thumbParams, KalturaThumbParams $reference, $id)
	{
	}

	/**
	 * Tests thumbParams->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->thumbParams->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests thumbParams->listAction action
	 * @param KalturaThumbParamsFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaThumbParamsListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaThumbParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsListResponse $reference)
	{
		$resultObject = $this->client->thumbParams->listAction($filter, $pager);
		$this->assertType('KalturaThumbParamsListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaThumbParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
