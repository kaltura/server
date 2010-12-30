<?php

/**
 * permission service base test case.
 */
abstract class PermissionServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests permission->add action
	 * @param KalturaPermission $permission
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaPermission $permission)
	{
		$resultObject = $this->client->permission->add($permission);
		$this->assertType('KalturaPermission', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests permission->get action
	 * @param string $permissionName
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($permissionName)
	{
		$resultObject = $this->client->permission->get($permissionName);
		$this->assertType('KalturaPermission', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests permission->update action
	 * @param string $permissionName
	 * @param KalturaPermission $permission
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($permissionName, KalturaPermission $permission)
	{
		$resultObject = $this->client->permission->update($permissionName, $permission);
		$this->assertType('KalturaPermission', $resultObject);
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
	 * Tests permission->delete action
	 * @param string $permissionName
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($permissionName)
	{
		$resultObject = $this->client->permission->delete($permissionName);
	}

	/**
	 * Tests permission->list action
	 * @param KalturaPermissionFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaPermissionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->permission->listAction($filter, $pager);
		$this->assertType('KalturaPermissionListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
