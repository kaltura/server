<?php
require_once("tests/bootstrapTests.php");

class MediaAddTest extends PHPUnit_Framework_TestCase
{
	protected function setUp ()
	{
		parent::setUp();
	}
	
	protected function tearDown ()
	{
		parent::tearDown();
	}
	
	public function __construct ()
	{
		parent::__construct();	
	}
	
	public function testUserCannotAddToOtherUser() 
	{
	    $mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
		
		$mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		$mediaEntry->userId = "BadUser";

		$url = MediaTestsHelpers::prepareDummyUrl();
		
		try 
		{
			$mediaService->addFromUrlAction($mediaEntry, $url);
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("INVALID_KS", $ex->getCode());
			return;
		}
		
		$this->fail("Expecting exception");
	}
	
	public function testNormalSessionCannotSetAdminTags()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
		
		$mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		$mediaEntry->adminTags = "my_admin_tag";
		
		$url = MediaTestsHelpers::prepareDummyUrl();
		
		try 
		{
			$mediaService->addFromUrlAction($mediaEntry, $url);
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("PROPERTY_VALIDATION_ADMIN_PROPERTY", $ex->getCode());
			return;
		}
		
		$this->fail("Expecting exception");
	}

	public function testAddFromUrl()
	{
        $mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
	    
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		
		$url = MediaTestsHelpers::prepareDummyUrl();
		
		$newMediaEntry = $mediaService->addFromUrlAction(clone $mediaEntry, $url);
		
		MediaTestsHelpers::assertMediaEntry($mediaEntry, $newMediaEntry);
		
		self::assertEquals(KalturaSourceType::URL, $newMediaEntry->sourceType);
		
		$mediaEntryGet = $mediaService->getAction($newMediaEntry->id);
		$this->assertEquals($mediaEntry->name, $mediaEntryGet->name);
		$this->assertEquals($mediaEntry->mediaType, $mediaEntryGet->mediaType);
		
		return $mediaEntry;
	}
	
	public function testAddFromSearchResult()
	{
	    $mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromSearchResult");
	    
	    // media entry
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		
		// search result
		$searchService = KalturaTestsHelpers::getServiceInitializedForAction("search", "search");
	    $search = new KalturaSearch();
	    $search->keyWords = "dog";
	    $search->mediaType = KalturaMediaType::VIDEO;
	    $search->searchSource = KalturaSearchProviderType::YOUTUBE;
	    $searchResponse = $searchService->searchAction($search);
	    $searchResult = $searchResponse->objects[0];
	    
		$newMediaEntry = $mediaService->addFromSearchResultAction(clone $mediaEntry, clone $searchResult);
		
		MediaTestsHelpers::assertMediaEntry($mediaEntry, $newMediaEntry);
		
		self::assertEquals(KalturaSourceType::SEARCH_PROVIDER, $newMediaEntry->sourceType);
		self::assertEquals(KalturaSearchProviderType::YOUTUBE, $newMediaEntry->searchProviderType);
		
		$mediaEntryGet = $mediaService->getAction($newMediaEntry->id);
		$this->assertEquals($mediaEntry->name, $mediaEntryGet->name);
		$this->assertEquals($mediaEntry->mediaType, $mediaEntryGet->mediaType);
	}
	
	public function testAddFromUploadedFile()
	{
		$ks = KalturaTestsHelpers::getNormalKs();
		$ksObj = ks::fromSecureString($ks);
		$ksUnique = $ksObj->getUniqueString();
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "flv";
		
		$token = $ksUnique."_".$uniqueId.".".$ext;
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "flv");
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy($currentPath."/../files/kaltura_logo_animated_black.flv", $fullPath);
		
	    $mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUploadedFile");
	    
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
	    
		$newMediaEntry = $mediaService->addFromUploadedFileAction(clone $mediaEntry, $token);
		
		MediaTestsHelpers::assertMediaEntry($mediaEntry, $newMediaEntry);
		self::assertEquals(KalturaSourceType::FILE, $newMediaEntry->sourceType);
		
		$mediaEntryGet = $mediaService->getAction($newMediaEntry->id);
		$this->assertEquals($mediaEntry->name, $mediaEntryGet->name);
		$this->assertEquals($mediaEntry->mediaType, $mediaEntryGet->mediaType);
	}
}