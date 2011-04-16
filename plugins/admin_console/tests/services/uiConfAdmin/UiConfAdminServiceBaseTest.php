<?php

/**
 * uiConfAdmin service base test case.
 */
abstract class UiConfAdminServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests uiConfAdmin->add action
	 * @param KalturaUiConfAdmin $uiConf 
	 * @param KalturaUiConfAdmin $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUiConfAdmin $uiConf, KalturaUiConfAdmin $reference)
	{
		$resultObject = $this->client->uiConfAdmin->add($uiConf);
		$this->assertType('KalturaUiConfAdmin', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($uiConf, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUiConfAdmin $uiConf, KalturaUiConfAdmin $reference)
	{
	}

	/**
	 * Tests uiConfAdmin->update action
	 * @param KalturaUiConfAdmin $uiConf 
	 * @param KalturaUiConfAdmin $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaUiConfAdmin $uiConf, KalturaUiConfAdmin $reference, $id)
	{
		$resultObject = $this->client->uiConfAdmin->update($id, $uiConf);
		$this->assertType('KalturaUiConfAdmin', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($uiConf, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaUiConfAdmin $uiConf, KalturaUiConfAdmin $reference, $id)
	{
	}

	/**
	 * Tests uiConfAdmin->get action
	 * @param KalturaUiConfAdmin $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaUiConfAdmin $reference, $id)
	{
		$resultObject = $this->client->uiConfAdmin->get($id);
		$this->assertType('KalturaUiConfAdmin', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaUiConfAdmin $reference, $id)
	{
	}

	/**
	 * Tests uiConfAdmin->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->uiConfAdmin->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests uiConfAdmin->listAction action
	 * @param KalturaUiConfFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaUiConfAdminListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null, KalturaUiConfAdminListResponse $reference)
	{
		$resultObject = $this->client->uiConfAdmin->listAction($filter, $pager);
		$this->assertType('KalturaUiConfAdminListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null, KalturaUiConfAdminListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
