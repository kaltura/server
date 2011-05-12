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
	 * @param KalturaBaseEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromUploadedFile(KalturaBaseEntry $entry, $uploadTokenId, KalturaEntryType $type = -1, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->baseEntry->addFromUploadedFile($entry, $uploadTokenId, $type, $reference);
		$this->assertType('KalturaBaseEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaBaseEntry $reference)
	{
		parent::validateGet($entryId, $version, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaBaseEntry $baseEntry, KalturaBaseEntry $reference)
	{
		parent::validateUpdate($entryId, $baseEntry, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests baseEntry->getByIds action
	 * @param string $entryIds
	 * @param KalturaBaseEntryArray $reference
	 * @dataProvider provideData
	 */
	public function testGetByIds($entryIds, KalturaBaseEntryArray $reference)
	{
		$resultObject = $this->client->baseEntry->getByIds($entryIds, $reference);
		$this->assertType('KalturaBaseEntryArray', $resultObject);
		// TODO - add here your own validations
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
	protected function validateList(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseEntryListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests baseEntry->count action
	 * @param KalturaBaseEntryFilter $filter
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testCount(KalturaBaseEntryFilter $filter = null, $reference)
	{
		$resultObject = $this->client->baseEntry->count($filter, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests baseEntry->upload action
	 * @param file $fileData
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testUpload(file $fileData, $reference)
	{
		$resultObject = $this->client->baseEntry->upload($fileData, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests baseEntry->updateThumbnailJpeg action
	 * @param string $entryId
	 * @param file $fileData
	 * @param KalturaBaseEntry $reference
	 * @dataProvider provideData
	 */
	public function testUpdateThumbnailJpeg($entryId, file $fileData, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->baseEntry->updateThumbnailJpeg($entryId, $fileData, $reference);
		$this->assertType('KalturaBaseEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests baseEntry->updateThumbnailFromUrl action
	 * @param string $entryId
	 * @param string $url
	 * @param KalturaBaseEntry $reference
	 * @dataProvider provideData
	 */
	public function testUpdateThumbnailFromUrl($entryId, $url, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->baseEntry->updateThumbnailFromUrl($entryId, $url, $reference);
		$this->assertType('KalturaBaseEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests baseEntry->updateThumbnailFromSourceEntry action
	 * @param string $entryId
	 * @param string $sourceEntryId
	 * @param int $timeOffset
	 * @param KalturaBaseEntry $reference
	 * @dataProvider provideData
	 */
	public function testUpdateThumbnailFromSourceEntry($entryId, $sourceEntryId, $timeOffset, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->baseEntry->updateThumbnailFromSourceEntry($entryId, $sourceEntryId, $timeOffset, $reference);
		$this->assertType('KalturaBaseEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests baseEntry->flag action
	 * @param KalturaModerationFlag $moderationFlag
	 * @dataProvider provideData
	 */
	public function testFlag(KalturaModerationFlag $moderationFlag)
	{
		$resultObject = $this->client->baseEntry->flag($moderationFlag);
		// TODO - add here your own validations
	}

	/**
	 * Tests baseEntry->reject action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testReject($entryId)
	{
		$resultObject = $this->client->baseEntry->reject($entryId);
		// TODO - add here your own validations
	}

	/**
	 * Tests baseEntry->approve action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testApprove($entryId)
	{
		$resultObject = $this->client->baseEntry->approve($entryId);
		// TODO - add here your own validations
	}

	/**
	 * Tests baseEntry->listFlags action
	 * @param string $entryId
	 * @param KalturaFilterPager $pager
	 * @param KalturaModerationFlagListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListFlags($entryId, KalturaFilterPager $pager = null, KalturaModerationFlagListResponse $reference)
	{
		$resultObject = $this->client->baseEntry->listFlags($entryId, $pager, $reference);
		$this->assertType('KalturaModerationFlagListResponse', $resultObject);
		// TODO - add here your own validations
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
		// TODO - add here your own validations
	}

	/**
	 * Tests baseEntry->getContextData action
	 * @param string $entryId
	 * @param KalturaEntryContextDataParams $contextDataParams
	 * @param KalturaEntryContextDataResult $reference
	 * @dataProvider provideData
	 */
	public function testGetContextData($entryId, KalturaEntryContextDataParams $contextDataParams, KalturaEntryContextDataResult $reference)
	{
		$resultObject = $this->client->baseEntry->getContextData($entryId, $contextDataParams, $reference);
		$this->assertType('KalturaEntryContextDataResult', $resultObject);
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
