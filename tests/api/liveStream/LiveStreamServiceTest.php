<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/LiveStreamServiceBaseTest.php');

/**
 * liveStream service test case.
 */
class LiveStreamServiceTest extends LiveStreamServiceBaseTest
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * Tests liveStream->updateOfflineThumbnailJpeg action
	 * @param string $entryId
	 * @param file $fileData
	 * @dataProvider provideData
	 */
	public function testUpdateOfflineThumbnailJpeg($entryId, file $fileData)
	{
		$resultObject = $this->client->liveStream->updateOfflineThumbnailJpeg($entryId, $fileData);
		$this->assertType('KalturaLiveStreamEntry', $resultObject);
	}

	/**
	 * Tests liveStream->updateOfflineThumbnailFromUrl action
	 * @param string $entryId
	 * @param string $url
	 * @dataProvider provideData
	 */
	public function testUpdateOfflineThumbnailFromUrl($entryId, $url)
	{
		$resultObject = $this->client->liveStream->updateOfflineThumbnailFromUrl($entryId, $url);
		$this->assertType('KalturaLiveStreamEntry', $resultObject);
	}

}
