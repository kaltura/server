<?php

/**
 * liveStream service base test case.
 */
abstract class LiveStreamServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests liveStream->add action
	 * @param KalturaLiveStreamAdminEntry $liveStreamEntry
	 * @param KalturaSourceType $sourceType
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaLiveStreamAdminEntry $liveStreamEntry, KalturaSourceType $sourceType = null)
	{
		$resultObject = $this->client->liveStream->add($liveStreamEntry, $sourceType);
		$this->assertType('KalturaLiveStreamAdminEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests liveStream->get action
	 * @param string $entryId
	 * @param int $version
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($entryId, $version = -1)
	{
		$resultObject = $this->client->liveStream->get($entryId, $version);
		$this->assertType('KalturaLiveStreamEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests liveStream->update action
	 * @param string $entryId
	 * @param KalturaLiveStreamAdminEntry $liveStreamEntry
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaLiveStreamAdminEntry $liveStreamEntry)
	{
		$resultObject = $this->client->liveStream->update($entryId, $liveStreamEntry);
		$this->assertType('KalturaLiveStreamAdminEntry', $resultObject);
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
	 * Tests liveStream->delete action
	 * @param string $entryId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->liveStream->delete($entryId);
	}

	/**
	 * Tests liveStream->list action
	 * @param KalturaLiveStreamEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaLiveStreamEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->liveStream->listAction($filter, $pager);
		$this->assertType('KalturaLiveStreamListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
