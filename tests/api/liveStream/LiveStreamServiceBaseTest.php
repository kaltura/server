<?php

/**
 * liveStream service base test case.
 */
abstract class LiveStreamServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests liveStream->add action
	 * @param KalturaLiveStreamAdminEntry $liveStreamEntry Live stream entry metadata  
	 * @param KalturaSourceType $sourceType  Live stream source type
	 * @param KalturaLiveStreamAdminEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaLiveStreamAdminEntry $liveStreamEntry, KalturaSourceType $sourceType = null, KalturaLiveStreamAdminEntry $reference)
	{
		$resultObject = $this->client->liveStream->add($liveStreamEntry, $sourceType);
		$this->assertType('KalturaLiveStreamAdminEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($liveStreamEntry, $sourceType, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaLiveStreamAdminEntry $liveStreamEntry, KalturaSourceType $sourceType = null, KalturaLiveStreamAdminEntry $reference)
	{
	}

	/**
	 * Tests liveStream->get action
	 * @param string $entryId Live stream entry id
	 * @param int $version Desired version of the data
	 * @param KalturaLiveStreamEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaLiveStreamEntry $reference)
	{
		$resultObject = $this->client->liveStream->get($entryId, $version);
		$this->assertType('KalturaLiveStreamEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($entryId, $version, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaLiveStreamEntry $reference)
	{
	}

	/**
	 * Tests liveStream->update action
	 * @param string $entryId Live stream entry id to update
	 * @param KalturaLiveStreamAdminEntry $liveStreamEntry Live stream entry metadata to update
	 * @param KalturaLiveStreamAdminEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaLiveStreamAdminEntry $liveStreamEntry, KalturaLiveStreamAdminEntry $reference)
	{
		$resultObject = $this->client->liveStream->update($entryId, $liveStreamEntry);
		$this->assertType('KalturaLiveStreamAdminEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($entryId, $liveStreamEntry, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaLiveStreamAdminEntry $liveStreamEntry, KalturaLiveStreamAdminEntry $reference)
	{
	}

	/**
	 * Tests liveStream->delete action
	 * @param string $entryId Live stream entry id to delete
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->liveStream->delete($entryId);
		$this->validateDelete($entryId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
	}

	/**
	 * Tests liveStream->listAction action
	 * @param KalturaLiveStreamEntryFilter $filter live stream entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaLiveStreamListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaLiveStreamEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaLiveStreamListResponse $reference)
	{
		$resultObject = $this->client->liveStream->listAction($filter, $pager);
		$this->assertType('KalturaLiveStreamListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaLiveStreamEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaLiveStreamListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
