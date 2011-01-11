<?php

/**
 * permission service base test case.
 */
abstract class PermissionServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests permission->add action
	 * @param KalturaPermission $permission 
	 * @param KalturaPermission $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaPermission $permission, KalturaPermission $reference)
	{
		$resultObject = $this->client->permission->add($permission);
		$this->assertType('KalturaPermission', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($permission, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaPermission $permission, KalturaPermission $reference)
	{
	}

	/**
	 * Tests permission->get action
	 * @param string $permissionName 
	 * @param KalturaPermission $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($permissionName, KalturaPermission $reference)
	{
		$resultObject = $this->client->permission->get($permissionName);
		$this->assertType('KalturaPermission', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($permissionName, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($permissionName, KalturaPermission $reference)
	{
	}

	/**
	 * Tests permission->update action
	 * @param string $permissionName 
	 * @param KalturaPermission $permission Name
	 * @param KalturaPermission $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($permissionName, KalturaPermission $permission, KalturaPermission $reference)
	{
		$resultObject = $this->client->permission->update($permissionName, $permission);
		$this->assertType('KalturaPermission', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($permissionName, $permission, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($permissionName, KalturaPermission $permission, KalturaPermission $reference)
	{
	}

	/**
	 * Tests permission->delete action
	 * @param string $permissionName 
	 * @dataProvider provideData
	 */
	public function testDelete($permissionName)
	{
		$resultObject = $this->client->permission->delete($permissionName);
		$this->validateDelete($permissionName);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($permissionName)
	{
	}

	/**
	 * Tests permission->list action
	 * @param KalturaPermissionFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaPermissionListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaPermissionFilter $filter = null, KalturaFilterPager $pager = null, KalturaPermissionListResponse $reference)
	{
		$resultObject = $this->client->permission->list($filter, $pager);
		$this->assertType('KalturaPermissionListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaPermissionFilter $filter = null, KalturaFilterPager $pager = null, KalturaPermissionListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
