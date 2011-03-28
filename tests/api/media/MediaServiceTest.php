<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * media service test case.
 */
class MediaServiceTest extends MediaServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaMediaEntry $entry, KalturaResource $resource = null, KalturaMediaEntry $reference)
	{
		parent::validateAdd($entry, $resource, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests media->addfromurl action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $url
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddfromurl(KalturaMediaEntry $mediaEntry, $url, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addfromurl($mediaEntry, $url, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addfromsearchresult action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param KalturaSearchResult $searchResult
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddfromsearchresult(KalturaMediaEntry $mediaEntry = null, KalturaSearchResult $searchResult = null, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addfromsearchresult($mediaEntry, $searchResult, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addfromuploadedfile action
	 * @param string $uploadTokenId
	 * @param KalturaMediaEntry $mediaEntry
	 * @param KalturaMediaEntry $reference
	 * @depends testUpload with data set #0
	 * @dataProvider provideData
	 */
	public function testAddfromuploadedfile($uploadTokenId, KalturaMediaEntry $mediaEntry, KalturaMediaEntry $reference)
	{
		KalturaLog::debug("Upload token [$uploadTokenId]");
		$resultObject = $this->client->media->addfromuploadedfile($mediaEntry, $uploadTokenId, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addfromrecordedwebcam action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $webcamTokenId
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddfromrecordedwebcam(KalturaMediaEntry $mediaEntry, $webcamTokenId, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addfromrecordedwebcam($mediaEntry, $webcamTokenId, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addfromentry action
	 * @param string $sourceEntryId
	 * @param KalturaMediaEntry $mediaEntry
	 * @param int $sourceFlavorParamsId
	 * @param KalturaMediaEntry $reference
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddfromentry($sourceEntryId, KalturaMediaEntry $mediaEntry = null, $sourceFlavorParamsId = null, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->media->addfromentry($sourceEntryId, $mediaEntry, $sourceFlavorParamsId, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->addfromflavorasset action
	 * @param string $entryId
	 * @param string $sourceFlavorAssetId
	 * @param KalturaMediaEntry $mediaEntry
	 * @param KalturaMediaEntry $reference
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAddfromflavorasset($entryId, $sourceFlavorAssetId, KalturaMediaEntry $mediaEntry = null, KalturaMediaEntry $reference)
	{
		if(is_null($sourceFlavorAssetId))
		{
			$flavorAssets = $this->client->flavorAsset->getByEntryId($entryId);
			$flavorAsset = reset($flavorAssets);
			$sourceFlavorAssetId = $flavorAsset->id;
		}
		
		$resultObject = $this->client->media->addfromflavorasset($sourceFlavorAssetId, $mediaEntry, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->convert action
	 * @param string $entryId
	 * @param int $conversionProfileId
	 * @param KalturaConversionAttributeArray $dynamicConversionAttributes
	 * @param int $reference
	 * @depends testAdd with data set #0
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
	public function testUpload($fileData, $reference)
	{
		$resultObject = $this->client->media->upload($fileData, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
		
		KalturaLog::debug("Returned token [$resultObject]");
		return $resultObject;
	}

	/**
	 * Tests media->requestconversion action
	 * @param string $entryId
	 * @param string $fileFormat
	 * @param int $reference
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testRequestconversion($entryId, $fileFormat, $reference)
	{
		$resultObject = $this->client->media->requestconversion($entryId, $fileFormat, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->flag action
	 * @param string $entryId
	 * @param KalturaModerationFlag $moderationFlag
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFlag($entryId, KalturaModerationFlag $moderationFlag)
	{
		$moderationFlag->flaggedEntryId = $entryId;
		$resultObject = $this->client->media->flag($moderationFlag);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->reject action
	 * @param string $entryId
	 * @depends testAdd with data set #0
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
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testApprove($entryId)
	{
		$resultObject = $this->client->media->approve($entryId);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->listflags action
	 * @param string $entryId
	 * @param KalturaFilterPager $pager
	 * @param KalturaModerationFlagListResponse $reference
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testListflags($entryId, KalturaFilterPager $pager = null, KalturaModerationFlagListResponse $reference)
	{
		$resultObject = $this->client->media->listflags($entryId, $pager, $reference);
		$this->assertType('KalturaModerationFlagListResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests media->anonymousrank action
	 * @param string $entryId
	 * @param int $rank
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testAnonymousrank($entryId, $rank)
	{
		$resultObject = $this->client->media->anonymousrank($entryId, $rank);
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
