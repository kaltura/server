<?php

/**
 * permissionItem service base test case.
 */
abstract class PermissionItemServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests permissionItem->add action
	 * @param KalturaPermissionItem $permissionItem
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaPermissionItem $permissionItem)
	{
		$resultObject = $this->client->permissionItem->add($permissionItem);
		$this->assertType('KalturaPermissionItem', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests permissionItem->get action
	 * @param int $permissionItemId
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($permissionItemId)
	{
		$resultObject = $this->client->permissionItem->get($permissionItemId);
		$this->assertType('KalturaPermissionItem', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests permissionItem->update action
	 * @param int $permissionItemId
	 * @param KalturaPermissionItem $permissionItem
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($permissionItemId, KalturaPermissionItem $permissionItem)
	{
		$resultObject = $this->client->permissionItem->update($permissionItemId, $permissionItem);
		$this->assertType('KalturaPermissionItem', $resultObject);
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
	 * Tests permissionItem->delete action
	 * @param int $permissionItemId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($permissionItemId)
	{
		$resultObject = $this->client->permissionItem->delete($permissionItemId);
	}

	/**
	 * Tests permissionItem->list action
	 * @param KalturaPermissionItemFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaPermissionItemFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->permissionItem->listAction($filter, $pager);
		$this->assertType('KalturaPremissionItemListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
