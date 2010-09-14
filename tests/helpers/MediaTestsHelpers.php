<?php
class MediaTestsHelpers
{
	static function getDummyEntryId()
	{
		return self::createDummyEntry()->id;
	}
	
	static function createDummyEntry()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
		$mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		$url = MediaTestsHelpers::prepareDummyUrl();
		return $mediaService->addFromUrlAction($mediaEntry, $url);
	}
	
	static function prepareMediaEntry()
	{
		$mediaEntry = new KalturaMediaEntry();
		$mediaEntry->name = "Test Name";
		$mediaEntry->creditUserName = KalturaTestsHelpers::getRandomString(10);
		$mediaEntry->creditUrl = KalturaTestsHelpers::getRandomString(20);
		$mediaEntry->mediaType = KalturaMediaType::VIDEO;
		//$mediaEntry->conversionQuality = "";
		return $mediaEntry;
	}
	
	static function prepareDummyUrl()
	{
		return "http://kaldev.kaltura.com/content/zbale/9spkxiz8m4_100007.avi";		
	}
	
	static function assertMediaEntry(KalturaMediaEntry $expected, $actual)
	{
		PHPUnit_Framework_Assert::assertType("KalturaMediaEntry", $actual);
		PHPUnit_Framework_Assert::assertEquals($expected->name, $actual->name);
		PHPUnit_Framework_Assert::assertEquals($expected->creditUserName, $actual->creditUserName);
		PHPUnit_Framework_Assert::assertEquals($expected->creditUrl, $actual->creditUrl);
		PHPUnit_Framework_Assert::assertEquals($expected->mediaType, $actual->mediaType);
		PHPUnit_Framework_Assert::assertEquals(0, $actual->plays);
		PHPUnit_Framework_Assert::assertEquals(0, $actual->views);
		PHPUnit_Framework_Assert::assertEquals(0, $actual->rank);
		PHPUnit_Framework_Assert::assertEquals(0, $actual->totalRank);
		PHPUnit_Framework_Assert::assertNull($actual->width);
		PHPUnit_Framework_Assert::assertNull($actual->height);
		PHPUnit_Framework_Assert::assertEquals(0, $actual->duration);		
	} 
	
	static function prepareModerationFlagForEntry($entryId)
	{
		$moderationFlag = new KalturaModerationFlag();
		$moderationFlag->flaggedEntryId = $entryId;
		$moderationFlag->flagType = KalturaModerationFlagType::HARMFUL_DANGEROUS;
		$moderationFlag->comments = KalturaTestsHelpers::getRandomText(5, 3, 8);
		return $moderationFlag;
	}
}