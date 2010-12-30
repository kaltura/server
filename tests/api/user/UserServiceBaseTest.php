<?php

/**
 * user service base test case.
 */
abstract class UserServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests user->add action
	 * @param KalturaUser $user
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUser $user)
	{
		$resultObject = $this->client->user->add($user);
		$this->assertType('KalturaUser', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests user->update action
	 * @param string $userId
	 * @param KalturaUser $user
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($userId, KalturaUser $user)
	{
		$resultObject = $this->client->user->update($userId, $user);
		$this->assertType('KalturaUser', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests user->get action
	 * @param string $userId
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($userId)
	{
		$resultObject = $this->client->user->get($userId);
		$this->assertType('KalturaUser', $resultObject);
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
	 * Tests user->delete action
	 * @param string $userId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($userId)
	{
		$resultObject = $this->client->user->delete($userId);
	}

	/**
	 * Tests user->list action
	 * @param KalturaUserFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->user->listAction($filter, $pager);
		$this->assertType('KalturaUserListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
