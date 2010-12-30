<?php

/**
 * uiConf service base test case.
 */
abstract class UiConfServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests uiConf->add action
	 * @param KalturaUiConf $uiConf
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUiConf $uiConf)
	{
		$resultObject = $this->client->uiConf->add($uiConf);
		$this->assertType('KalturaUiConf', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests uiConf->update action
	 * @param KalturaUiConf $uiConf
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaUiConf $uiConf, $id)
	{
		$resultObject = $this->client->uiConf->update($id, $uiConf);
		$this->assertType('KalturaUiConf', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests uiConf->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->uiConf->get($id);
		$this->assertType('KalturaUiConf', $resultObject);
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
	 * Tests uiConf->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->uiConf->delete($id);
	}

	/**
	 * Tests uiConf->list action
	 * @param KalturaUiConfFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->uiConf->listAction($filter, $pager);
		$this->assertType('KalturaUiConfListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
