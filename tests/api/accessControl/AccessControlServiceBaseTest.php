<?php

/**
 * accessControl service base test case.
 */
abstract class AccessControlServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests accessControl->add action
	 * @param KalturaAccessControl $accessControl 
	 * @param KalturaAccessControl $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaAccessControl $accessControl, KalturaAccessControl $reference)
	{
		$resultObject = $this->client->accessControl->add($accessControl);
		$this->assertType('KalturaAccessControl', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($accessControl, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaAccessControl $accessControl, KalturaAccessControl $reference)
	{
	}

	/**
	 * Tests accessControl->get action
	 * @param KalturaAccessControl $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaAccessControl $reference, $id)
	{
		$resultObject = $this->client->accessControl->get($id);
		$this->assertType('KalturaAccessControl', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaAccessControl $reference, $id)
	{
	}

	/**
	 * Tests accessControl->update action
	 * @param KalturaAccessControl $accessControl 
	 * @param KalturaAccessControl $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaAccessControl $accessControl, KalturaAccessControl $reference, $id)
	{
		$resultObject = $this->client->accessControl->update($id, $accessControl);
		$this->assertType('KalturaAccessControl', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($accessControl, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaAccessControl $accessControl, KalturaAccessControl $reference, $id)
	{
	}

	/**
	 * Tests accessControl->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->accessControl->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests accessControl->listAction action
	 * @param KalturaAccessControlFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaAccessControlListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaAccessControlFilter $filter = null, KalturaFilterPager $pager = null, KalturaAccessControlListResponse $reference)
	{
		$resultObject = $this->client->accessControl->listAction($filter, $pager);
		$this->assertType('KalturaAccessControlListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAccessControlFilter $filter = null, KalturaFilterPager $pager = null, KalturaAccessControlListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
