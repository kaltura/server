<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/LiveStreamServiceBaseTest.php');

/**
 * liveStream service test case.
 */
class LiveStreamServiceTest extends LiveStreamServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaLiveStreamAdminEntry $liveStreamEntry, $sourceType = null, KalturaLiveStreamAdminEntry $reference)
	{
		parent::validateAdd($liveStreamEntry, $sourceType, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaLiveStreamEntry $reference)
	{
		parent::validateGet($entryId, $version, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaLiveStreamAdminEntry $liveStreamEntry, KalturaLiveStreamAdminEntry $reference)
	{
		parent::validateUpdate($entryId, $liveStreamEntry, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
		parent::validateDelete($entryId);
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaLiveStreamEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaLiveStreamListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests liveStream->updateOfflineThumbnailJpeg action
	 * @param string $entryId
	 * @param file $fileData
	 * @param KalturaLiveStreamEntry $reference
	 * @dataProvider provideData
	 */
	public function testUpdateOfflineThumbnailJpeg($entryId, file $fileData, KalturaLiveStreamEntry $reference)
	{
		$resultObject = $this->client->liveStream->updateOfflineThumbnailJpeg($entryId, $fileData, $reference);
		$this->assertType('KalturaLiveStreamEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests liveStream->updateOfflineThumbnailFromUrl action
	 * @param string $entryId
	 * @param string $url
	 * @param KalturaLiveStreamEntry $reference
	 * @dataProvider provideData
	 */
	public function testUpdateOfflineThumbnailFromUrl($entryId, $url, KalturaLiveStreamEntry $reference)
	{
		$resultObject = $this->client->liveStream->updateOfflineThumbnailFromUrl($entryId, $url, $reference);
		$this->assertType('KalturaLiveStreamEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
