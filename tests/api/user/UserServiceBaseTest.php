<?php

/**
 * user service base test case.
 */
abstract class UserServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests user->add action
	 * @param KalturaUser $user 
	 * @param KalturaUser $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUser $user, KalturaUser $reference)
	{
		$resultObject = $this->client->user->add($user);
		$this->assertType('KalturaUser', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($user, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUser $user, KalturaUser $reference)
	{
	}

	/**
	 * Tests user->update action
	 * @param string $userId 
	 * @param KalturaUser $user Id
	 * @param KalturaUser $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($userId, KalturaUser $user, KalturaUser $reference)
	{
		$resultObject = $this->client->user->update($userId, $user);
		$this->assertType('KalturaUser', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($userId, $user, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($userId, KalturaUser $user, KalturaUser $reference)
	{
	}

	/**
	 * Tests user->get action
	 * @param string $userId 
	 * @param KalturaUser $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($userId, KalturaUser $reference)
	{
		$resultObject = $this->client->user->get($userId);
		$this->assertType('KalturaUser', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($userId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($userId, KalturaUser $reference)
	{
	}

	/**
	 * Tests user->delete action
	 * @param string $userId 
	 * @dataProvider provideData
	 */
	public function testDelete($userId)
	{
		$resultObject = $this->client->user->delete($userId);
		$this->validateDelete($userId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($userId)
	{
	}

	/**
	 * Tests user->listAction action
	 * @param KalturaUserFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaUserListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserListResponse $reference)
	{
		$resultObject = $this->client->user->listAction($filter, $pager);
		$this->assertType('KalturaUserListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null, KalturaUserListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
