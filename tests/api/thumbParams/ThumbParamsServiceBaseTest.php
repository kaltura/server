<?php

/**
 * thumbParams service base test case.
 */
abstract class ThumbParamsServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests thumbParams->add action
	 * @param KalturaThumbParams $thumbParams
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaThumbParams $thumbParams)
	{
		$resultObject = $this->client->thumbParams->add($thumbParams);
		$this->assertType('KalturaThumbParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests thumbParams->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->thumbParams->get($id);
		$this->assertType('KalturaThumbParams', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests thumbParams->update action
	 * @param KalturaThumbParams $thumbParams
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaThumbParams $thumbParams, $id)
	{
		$resultObject = $this->client->thumbParams->update($id, $thumbParams);
		$this->assertType('KalturaThumbParams', $resultObject);
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
	 * Tests thumbParams->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->thumbParams->delete($id);
	}

	/**
	 * Tests thumbParams->list action
	 * @param KalturaThumbParamsFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaThumbParamsFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->thumbParams->listAction($filter, $pager);
		$this->assertType('KalturaThumbParamsListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
