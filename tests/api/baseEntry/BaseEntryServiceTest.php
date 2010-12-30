<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/BaseEntryServiceBaseTest.php');

/**
 * baseEntry service test case.
 */
class BaseEntryServiceTest extends BaseEntryServiceBaseTest
{
	/**
	 * Tests baseEntry->addFromUploadedFile action
	 * @param KalturaBaseEntry $entry
	 * @param string $uploadTokenId
	 * @param KalturaEntryType $type
	 * @dataProvider provideData
	 */
	public function testAddFromUploadedFile(KalturaBaseEntry $entry, $uploadTokenId, KalturaEntryType $type = -1)
	{
		$resultObject = $this->client->baseEntry->addFromUploadedFile($entry, $uploadTokenId, $type);
		$this->assertType('KalturaBaseEntry', $resultObject);
	}

	/**
	 * Tests baseEntry->getByIds action
	 * @param string $entryIds
	 * @dataProvider provideData
	 */
	public function testGetByIds($entryIds)
	{
		$resultObject = $this->client->baseEntry->getByIds($entryIds);
		$this->assertType('KalturaBaseEntryArray', $resultObject);
	}

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
	 * Tests baseEntry->count action
	 * @param KalturaBaseEntryFilter $filter
	 * @dataProvider provideData
	 */
	public function testCount(KalturaBaseEntryFilter $filter = null)
	{
		$resultObject = $this->client->baseEntry->count($filter);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests baseEntry->upload action
	 * @param file $fileData
	 * @dataProvider provideData
	 */
	public function testUpload(file $fileData)
	{
		$resultObject = $this->client->baseEntry->upload($fileData);
		$this->assertType('string', $resultObject);
	}

	/**
	 * Tests baseEntry->updateThumbnailJpeg action
	 * @param string $entryId
	 * @param file $fileData
	 * @dataProvider provideData
	 */
	public function testUpdateThumbnailJpeg($entryId, file $fileData)
	{
		$resultObject = $this->client->baseEntry->updateThumbnailJpeg($entryId, $fileData);
		$this->assertType('KalturaBaseEntry', $resultObject);
	}

	/**
	 * Tests baseEntry->updateThumbnailFromUrl action
	 * @param string $entryId
	 * @param string $url
	 * @dataProvider provideData
	 */
	public function testUpdateThumbnailFromUrl($entryId, $url)
	{
		$resultObject = $this->client->baseEntry->updateThumbnailFromUrl($entryId, $url);
		$this->assertType('KalturaBaseEntry', $resultObject);
	}

	/**
	 * Tests baseEntry->updateThumbnailFromSourceEntry action
	 * @param string $entryId
	 * @param string $sourceEntryId
	 * @param int $timeOffset
	 * @dataProvider provideData
	 */
	public function testUpdateThumbnailFromSourceEntry($entryId, $sourceEntryId, $timeOffset)
	{
		$resultObject = $this->client->baseEntry->updateThumbnailFromSourceEntry($entryId, $sourceEntryId, $timeOffset);
		$this->assertType('KalturaBaseEntry', $resultObject);
	}

	/**
	 * Tests baseEntry->flag action
	 * @param KalturaModerationFlag $moderationFlag
	 * @dataProvider provideData
	 */
	public function testFlag(KalturaModerationFlag $moderationFlag)
	{
		$resultObject = $this->client->baseEntry->flag($moderationFlag);
	}

	/**
	 * Tests baseEntry->reject action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testReject($entryId)
	{
		$resultObject = $this->client->baseEntry->reject($entryId);
	}

	/**
	 * Tests baseEntry->approve action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testApprove($entryId)
	{
		$resultObject = $this->client->baseEntry->approve($entryId);
	}

	/**
	 * Tests baseEntry->listFlags action
	 * @param string $entryId
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testListFlags($entryId, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->baseEntry->listFlags($entryId, $pager);
		$this->assertType('KalturaModerationFlagListResponse', $resultObject);
	}

	/**
	 * Tests baseEntry->anonymousRank action
	 * @param string $entryId
	 * @param int $rank
	 * @dataProvider provideData
	 */
	public function testAnonymousRank($entryId, $rank)
	{
		$resultObject = $this->client->baseEntry->anonymousRank($entryId, $rank);
	}

	/**
	 * Tests baseEntry->getContextData action
	 * @param string $entryId
	 * @param KalturaEntryContextDataParams $contextDataParams
	 * @dataProvider provideData
	 */
	public function testGetContextData($entryId, KalturaEntryContextDataParams $contextDataParams)
	{
		$resultObject = $this->client->baseEntry->getContextData($entryId, $contextDataParams);
		$this->assertType('KalturaEntryContextDataResult', $resultObject);
	}

}
