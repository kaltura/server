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
	 * @dataProvider provideData
	 */
	public function testAddFromBulk(KalturaMediaEntry $mediaEntry, $url, $bulkUploadId)
	{
		$resultObject = $this->client->media->addFromBulk($mediaEntry, $url, $bulkUploadId);
		$this->assertType('KalturaMediaEntry', $resultObject);
	}

	/**
	 * Tests media->addFromUrl action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $url
	 * @dataProvider provideData
	 */
	public function testAddFromUrl(KalturaMediaEntry $mediaEntry, $url)
	{
		$resultObject = $this->client->media->addFromUrl($mediaEntry, $url);
		$this->assertType('KalturaMediaEntry', $resultObject);
	}

	/**
	 * Tests media->addFromSearchResult action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param KalturaSearchResult $searchResult
	 * @dataProvider provideData
	 */
	public function testAddFromSearchResult(KalturaMediaEntry $mediaEntry = null, KalturaSearchResult $searchResult = null)
	{
		$resultObject = $this->client->media->addFromSearchResult($mediaEntry, $searchResult);
		$this->assertType('KalturaMediaEntry', $resultObject);
	}

	/**
	 * Tests media->addFromUploadedFile action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $uploadTokenId
	 * @dataProvider provideData
	 */
	public function testAddFromUploadedFile(KalturaMediaEntry $mediaEntry, $uploadTokenId)
	{
		$resultObject = $this->client->media->addFromUploadedFile($mediaEntry, $uploadTokenId);
		$this->assertType('KalturaMediaEntry', $resultObject);
	}

	/**
	 * Tests media->addFromRecordedWebcam action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $webcamTokenId
	 * @dataProvider provideData
	 */
	public function testAddFromRecordedWebcam(KalturaMediaEntry $mediaEntry, $webcamTokenId)
	{
		$resultObject = $this->client->media->addFromRecordedWebcam($mediaEntry, $webcamTokenId);
		$this->assertType('KalturaMediaEntry', $resultObject);
	}

	/**
	 * Tests media->addFromEntry action
	 * @param string $sourceEntryId
	 * @param KalturaMediaEntry $mediaEntry
	 * @param int $sourceFlavorParamsId
	 * @dataProvider provideData
	 */
	public function testAddFromEntry($sourceEntryId, KalturaMediaEntry $mediaEntry = null, $sourceFlavorParamsId = null)
	{
		$resultObject = $this->client->media->addFromEntry($sourceEntryId, $mediaEntry, $sourceFlavorParamsId);
		$this->assertType('KalturaMediaEntry', $resultObject);
	}

	/**
	 * Tests media->addFromFlavorAsset action
	 * @param string $sourceFlavorAssetId
	 * @param KalturaMediaEntry $mediaEntry
	 * @dataProvider provideData
	 */
	public function testAddFromFlavorAsset($sourceFlavorAssetId, KalturaMediaEntry $mediaEntry = null)
	{
		$resultObject = $this->client->media->addFromFlavorAsset($sourceFlavorAssetId, $mediaEntry);
		$this->assertType('KalturaMediaEntry', $resultObject);
	}

	/**
	 * Tests media->convert action
	 * @param string $entryId
	 * @param int $conversionProfileId
	 * @param KalturaConversionAttributeArray $dynamicConversionAttributes
	 * @dataProvider provideData
	 */
	public function testConvert($entryId, $conversionProfileId = null, KalturaConversionAttributeArray $dynamicConversionAttributes = null)
	{
		$resultObject = $this->client->media->convert($entryId, $conversionProfileId, $dynamicConversionAttributes);
		$this->assertType('int', $resultObject);
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
	 * Tests media->count action
	 * @param KalturaMediaEntryFilter $filter
	 * @dataProvider provideData
	 */
	public function testCount(KalturaMediaEntryFilter $filter = null)
	{
		$resultObject = $this->client->media->count($filter);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests media->upload action
	 * @param file $fileData
	 * @dataProvider provideData
	 */
	public function testUpload(file $fileData)
	{
		$resultObject = $this->client->media->upload($fileData);
		$this->assertType('string', $resultObject);
	}

	/**
	 * Tests media->requestConversion action
	 * @param string $entryId
	 * @param string $fileFormat
	 * @dataProvider provideData
	 */
	public function testRequestConversion($entryId, $fileFormat)
	{
		$resultObject = $this->client->media->requestConversion($entryId, $fileFormat);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests media->flag action
	 * @param KalturaModerationFlag $moderationFlag
	 * @dataProvider provideData
	 */
	public function testFlag(KalturaModerationFlag $moderationFlag)
	{
		$resultObject = $this->client->media->flag($moderationFlag);
	}

	/**
	 * Tests media->reject action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testReject($entryId)
	{
		$resultObject = $this->client->media->reject($entryId);
	}

	/**
	 * Tests media->approve action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testApprove($entryId)
	{
		$resultObject = $this->client->media->approve($entryId);
	}

	/**
	 * Tests media->listFlags action
	 * @param string $entryId
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testListFlags($entryId, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->media->listFlags($entryId, $pager);
		$this->assertType('KalturaModerationFlagListResponse', $resultObject);
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
	}

}
