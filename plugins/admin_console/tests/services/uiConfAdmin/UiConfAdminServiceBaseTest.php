<?php

/**
 * uiConfAdmin service base test case.
 */
abstract class UiConfAdminServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests uiConfAdmin->add action
	 * @param KalturaUiConf $uiConf 
	 * @param KalturaUiConf $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUiConf $uiConf, KalturaUiConf $reference)
	{
		$resultObject = $this->client->uiConfAdmin->add($uiConf);
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
	 * Tests uiConfAdmin->update action
	 * @param KalturaUiConf $uiConf 
	 * @param KalturaUiConf $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaUiConf $uiConf, KalturaUiConf $reference, $id)
	{
		$resultObject = $this->client->uiConfAdmin->update($id, $uiConf);
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
	 * Tests uiConfAdmin->get action
	 * @param KalturaUiConf $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaUiConf $reference, $id)
	{
		$resultObject = $this->client->uiConfAdmin->get($id);
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
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
