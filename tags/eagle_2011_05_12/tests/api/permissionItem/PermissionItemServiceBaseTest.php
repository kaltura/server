<?php

/**
 * permissionItem service base test case.
 */
abstract class PermissionItemServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests permissionItem->add action
	 * @param KalturaPermissionItem $permissionItem 
	 * @param KalturaPermissionItem $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaPermissionItem $permissionItem, KalturaPermissionItem $reference)
	{
		$resultObject = $this->client->permissionItem->add($permissionItem);
		$this->assertType('KalturaPermissionItem', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($permissionItem, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaPermissionItem $permissionItem, KalturaPermissionItem $reference)
	{
	}

	/**
	 * Tests permissionItem->get action
	 * @param int $permissionItemId 
	 * @param KalturaPermissionItem $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($permissionItemId, KalturaPermissionItem $reference)
	{
		$resultObject = $this->client->permissionItem->get($permissionItemId);
		$this->assertType('KalturaPermissionItem', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($permissionItemId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($permissionItemId, KalturaPermissionItem $reference)
	{
	}

	/**
	 * Tests permissionItem->update action
	 * @param int $permissionItemId 
	 * @param KalturaPermissionItem $permissionItem Id
	 * @param KalturaPermissionItem $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($permissionItemId, KalturaPermissionItem $permissionItem, KalturaPermissionItem $reference)
	{
		$resultObject = $this->client->permissionItem->update($permissionItemId, $permissionItem);
		$this->assertType('KalturaPermissionItem', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($permissionItemId, $permissionItem, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($permissionItemId, KalturaPermissionItem $permissionItem, KalturaPermissionItem $reference)
	{
	}

	/**
	 * Tests permissionItem->delete action
	 * @param int $permissionItemId 
	 * @dataProvider provideData
	 */
	public function testDelete($permissionItemId)
	{
		$resultObject = $this->client->permissionItem->delete($permissionItemId);
		$this->validateDelete($permissionItemId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($permissionItemId)
	{
	}

	/**
	 * Tests permissionItem->listAction action
	 * @param KalturaPermissionItemFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaPermissionItemListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaPermissionItemFilter $filter = null, KalturaFilterPager $pager = null, KalturaPermissionItemListResponse $reference)
	{
		$resultObject = $this->client->permissionItem->listAction($filter, $pager);
		$this->assertType('KalturaPermissionItemListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaPermissionItemFilter $filter = null, KalturaFilterPager $pager = null, KalturaPermissionItemListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
