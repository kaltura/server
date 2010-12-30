<?php

/**
 * accessControl service base test case.
 */
abstract class AccessControlServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests accessControl->add action
	 * @param KalturaAccessControl $accessControl
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaAccessControl $accessControl)
	{
		$resultObject = $this->client->accessControl->add($accessControl);
		$this->assertType('KalturaAccessControl', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests accessControl->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->accessControl->get($id);
		$this->assertType('KalturaAccessControl', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests accessControl->update action
	 * @param KalturaAccessControl $accessControl
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaAccessControl $accessControl, $id)
	{
		$resultObject = $this->client->accessControl->update($id, $accessControl);
		$this->assertType('KalturaAccessControl', $resultObject);
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
	 * Tests accessControl->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->accessControl->delete($id);
	}

	/**
	 * Tests accessControl->list action
	 * @param KalturaAccessControlFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaAccessControlFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->accessControl->listAction($filter, $pager);
		$this->assertType('KalturaAccessControlListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
