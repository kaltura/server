<?php

/**
 * userRole service base test case.
 */
abstract class UserRoleServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests userRole->add action
	 * @param KalturaUserRole $userRole 
	 * @param KalturaUserRole $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUserRole $userRole, KalturaUserRole $reference)
	{
		$resultObject = $this->client->userRole->add($userRole);
		$this->assertType('KalturaUserRole', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($userRole, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUserRole $userRole, KalturaUserRole $reference)
	{
	}

	/**
	 * Tests userRole->get action
	 * @param int $userRoleId 
	 * @param KalturaUserRole $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($userRoleId, KalturaUserRole $reference)
	{
		$resultObject = $this->client->userRole->get($userRoleId);
		$this->assertType('KalturaUserRole', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($userRoleId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($userRoleId, KalturaUserRole $reference)
	{
	}

	/**
	 * Tests userRole->update action
	 * @param int $userRoleId 
	 * @param KalturaUserRole $userRole Id
	 * @param KalturaUserRole $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($userRoleId, KalturaUserRole $userRole, KalturaUserRole $reference)
	{
		$resultObject = $this->client->userRole->update($userRoleId, $userRole);
		$this->assertType('KalturaUserRole', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($userRoleId, $userRole, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($userRoleId, KalturaUserRole $userRole, KalturaUserRole $reference)
	{
	}

	/**
	 * Tests userRole->delete action
	 * @param int $userRoleId 
	 * @dataProvider provideData
	 */
	public function testDelete($userRoleId)
	{
		$resultObject = $this->client->userRole->delete($userRoleId);
		$this->validateDelete($userRoleId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($userRoleId)
	{
	}

	/**
	 * Tests userRole->listAction action
	 * @param KalturaUserRoleFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaUserRoleListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaUserRoleFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserRoleListResponse $reference)
	{
		$resultObject = $this->client->userRole->listAction($filter, $pager);
		$this->assertType('KalturaUserRoleListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaUserRoleFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserRoleListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
