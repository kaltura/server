<?php
require_once("tests/bootstrapTests.php");

class MediaUpdateTest extends PHPUnit_Framework_TestCase
{
	private $entryId;
	
	public function setUp ()
	{
		parent::setUp();
	}
	
	public function tearDown ()
	{
		parent::tearDown();
		
		$entry = entryPeer::retrieveByPKNoFilter($this->entryId);
		if ($entry)
			$entry->delete();
	}
	
	public function __construct ()
	{
	}
	
	public function testEntryIdNotFound()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "update");
		
		$mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		
		try
		{
			$mediaService->updateAction("xyz123", clone $mediaEntry);
			$this->fail();
		}
		catch(KalturaAPIException $ex)
		{
			self::assertEquals("ENTRY_ID_NOT_FOUND", $ex->getCode());
			return;			
		}
		$this->fail();
	}
	
	public function testUpdate()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "update");
	    
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		$url = MediaTestsHelpers::prepareDummyUrl();
		$newMediaEntry = $mediaService->addFromUrlAction(clone $mediaEntry, $url);
		$id = $newMediaEntry->id;
		
		$mediaEntry = new KalturaMediaEntry();
		$mediaEntry->name = "New Name";
		$mediaService->updateAction($id, clone $mediaEntry);
		
		$updatedMediaEntry = $mediaService->getAction($id);
		
		$this->assertEquals($mediaEntry->name, $updatedMediaEntry->name);
	}
	
	public function testUpdateOfInvalidAccessControl()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "update");
	    
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		$url = MediaTestsHelpers::prepareDummyUrl();
		$newMediaEntry = $mediaService->addFromUrlAction(clone $mediaEntry, $url);
		$this->entryId = $newMediaEntry->id;
		
		$updateMediaEntry = new KalturaMediaEntry();
		$updateMediaEntry->accessControlId = 0;
		try
		{
			$mediaService->updateAction($this->entryId, $updateMediaEntry);
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("ACCESS_CONTROL_ID_NOT_FOUND", $ex->getCode());
			return;
		}
		$this->fail("Entry was updated with invalid access control");
	}
}

