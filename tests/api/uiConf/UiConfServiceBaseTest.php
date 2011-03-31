<?php

/**
 * uiConf service base test case.
 */
abstract class UiConfServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests uiConf->add action
	 * @param KalturaUiConf $uiConf Mandatory input parameter of type KalturaUiConf
	 * @param KalturaUiConf $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUiConf $uiConf, KalturaUiConf $reference)
	{
		$resultObject = $this->client->uiConf->add($uiConf);
		$this->assertType('KalturaUiConf', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($uiConf, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUiConf $uiConf, KalturaUiConf $reference)
	{
	}

	/**
	 * Tests uiConf->update action
	 * @param KalturaUiConf $uiConf 
	 * @param KalturaUiConf $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaUiConf $uiConf, KalturaUiConf $reference, $id)
	{
		$resultObject = $this->client->uiConf->update($id, $uiConf);
		$this->assertType('KalturaUiConf', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($uiConf, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaUiConf $uiConf, KalturaUiConf $reference, $id)
	{
	}

	/**
	 * Tests uiConf->get action
	 * @param KalturaUiConf $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaUiConf $reference, $id)
	{
		$resultObject = $this->client->uiConf->get($id);
		$this->assertType('KalturaUiConf', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaUiConf $reference, $id)
	{
	}

	/**
	 * Tests uiConf->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->uiConf->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests uiConf->listAction action
	 * @param KalturaUiConfFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaUiConfListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null, KalturaUiConfListResponse $reference)
	{
		$resultObject = $this->client->uiConf->listAction($filter, $pager);
		$this->assertType('KalturaUiConfListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null, KalturaUiConfListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
