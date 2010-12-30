<?php

/**
 * userRole service base test case.
 */
abstract class UserRoleServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests userRole->add action
	 * @param KalturaUserRole $userRole
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUserRole $userRole)
	{
		$resultObject = $this->client->userRole->add($userRole);
		$this->assertType('KalturaUserRole', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests userRole->get action
	 * @param int $userRoleId
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($userRoleId)
	{
		$resultObject = $this->client->userRole->get($userRoleId);
		$this->assertType('KalturaUserRole', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests userRole->update action
	 * @param int $userRoleId
	 * @param KalturaUserRole $userRole
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($userRoleId, KalturaUserRole $userRole)
	{
		$resultObject = $this->client->userRole->update($userRoleId, $userRole);
		$this->assertType('KalturaUserRole', $resultObject);
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
	 * Tests userRole->delete action
	 * @param int $userRoleId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($userRoleId)
	{
		$resultObject = $this->client->userRole->delete($userRoleId);
	}

	/**
	 * Tests userRole->list action
	 * @param KalturaUserRoleFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaUserRoleFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->userRole->listAction($filter, $pager);
		$this->assertType('KalturaUserRoleListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
