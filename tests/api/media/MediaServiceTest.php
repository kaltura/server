<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/MediaServiceBaseTest.php');

/**
 * media service test case.
 */
class MediaServiceTest extends MediaServiceBaseTest
{
	/**
	 * Tests media->addFromBulk action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $url
	 * @param int $bulkUploadId
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromBulk(KalturaMediaEntry $mediaEntry, $url, $bulkUploadId, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addFromBulk($mediaEntry, $url, $bulkUploadId, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addFromUrl action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $url
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromUrl(KalturaMediaEntry $mediaEntry, $url, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addFromUrl($mediaEntry, $url, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addFromSearchResult action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param KalturaSearchResult $searchResult
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromSearchResult(KalturaMediaEntry $mediaEntry = null, KalturaSearchResult $searchResult = null, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addFromSearchResult($mediaEntry, $searchResult, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addFromUploadedFile action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $uploadTokenId
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromUploadedFile(KalturaMediaEntry $mediaEntry, $uploadTokenId, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addFromUploadedFile($mediaEntry, $uploadTokenId, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addFromRecordedWebcam action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $webcamTokenId
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromRecordedWebcam(KalturaMediaEntry $mediaEntry, $webcamTokenId, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addFromRecordedWebcam($mediaEntry, $webcamTokenId, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addFromEntry action
	 * @param string $sourceEntryId
	 * @param KalturaMediaEntry $mediaEntry
	 * @param int $sourceFlavorParamsId
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromEntry($sourceEntryId, KalturaMediaEntry $mediaEntry = null, $sourceFlavorParamsId = null, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addFromEntry($sourceEntryId, $mediaEntry, $sourceFlavorParamsId, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addFromFlavorAsset action
	 * @param string $sourceFlavorAssetId
	 * @param KalturaMediaEntry $mediaEntry
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromFlavorAsset($sourceFlavorAssetId, KalturaMediaEntry $mediaEntry = null, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addFromFlavorAsset($sourceFlavorAssetId, $mediaEntry, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->convert action
	 * @param string $entryId
	 * @param int $conversionProfileId
	 * @param KalturaConversionAttributeArray $dynamicConversionAttributes
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testConvert($entryId, $conversionProfileId = null, KalturaConversionAttributeArray $dynamicConversionAttributes = null, $reference)
	{
		$resultObject = $this->client->media->convert($entryId, $conversionProfileId, $dynamicConversionAttributes, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaMediaEntry $reference)
	{
		parent::validateGet($entryId, $version, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaMediaEntry $mediaEntry, KalturaMediaEntry $reference)
	{
		parent::validateUpdate($entryId, $mediaEntry, $reference);
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
	protected function validateList(KalturaMediaEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests media->count action
	 * @param KalturaMediaEntryFilter $filter
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testCount(KalturaMediaEntryFilter $filter = null, $reference)
	{
		$resultObject = $this->client->media->count($filter, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->upload action
	 * @param file $fileData
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testUpload(file $fileData, $reference)
	{
		$resultObject = $this->client->media->upload($fileData, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->requestConversion action
	 * @param string $entryId
	 * @param string $fileFormat
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testRequestConversion($entryId, $fileFormat, $reference)
	{
		$resultObject = $this->client->media->requestConversion($entryId, $fileFormat, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->flag action
	 * @param KalturaModerationFlag $moderationFlag
	 * @dataProvider provideData
	 */
	public function testFlag(KalturaModerationFlag $moderationFlag)
	{
		$resultObject = $this->client->media->flag($moderationFlag);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->reject action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testReject($entryId)
	{
		$resultObject = $this->client->media->reject($entryId);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->approve action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testApprove($entryId)
	{
		$resultObject = $this->client->media->approve($entryId);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->listFlags action
	 * @param string $entryId
	 * @param KalturaFilterPager $pager
	 * @param KalturaModerationFlagListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListFlags($entryId, KalturaFilterPager $pager = null, KalturaModerationFlagListResponse $reference)
	{
		$resultObject = $this->client->media->listFlags($entryId, $pager, $reference);
		$this->assertType('KalturaModerationFlagListResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->anonymousRank action
	 * @param string $entryId
	 * @param int $rank
	 * @dataProvider provideData
	 */
	public function testAnonymousRank($entryId, $rank)
	{
		$resultObject = $this->client->media->anonymousRank($entryId, $rank);
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
