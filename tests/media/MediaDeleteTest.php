<?php
require_once("tests/bootstrapTests.php");

class MediaDeleteTest extends PHPUnit_Framework_TestCase
{
	private $_mediaEntryId;
	
	public function setUp ()
	{
		parent::setUp();
		
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
	    
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		$url = MediaTestsHelpers::prepareDummyUrl();
		$newMediaEntry = $mediaService->addFromUrlAction($mediaEntry, $url);
		$this->_mediaEntryId = $newMediaEntry->id;
	}
	
	public function tearDown ()
	{
		parent::tearDown();
		
		$entry = entryPeer::retrieveByPK($this->_mediaEntryId);
		if ($entry)
			$entry->delete();
	}
	
	public function __construct ()
	{
	}
	
	public function testDelete()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "delete");
		$result = $mediaService->deleteAction($this->_mediaEntryId);
		$this->assertNull($result);
		
		// check entry db status status
		$entryDb = entryPeer::retrieveByPKNoFilter($this->_mediaEntryId);
		$this->assertEquals(KalturaEntryStatus::DELETED, $entryDb->getStatus());
		
		// make sure it is not returned in get
		try 
		{
			$mediaEntry = $mediaService->getAction($this->_mediaEntryId);
		}
		catch(KalturaAPIException $ex)
		{
			$this->assertEquals("ENTRY_ID_NOT_FOUND", $ex->getCode());
			
			// make sure is is not returned in list
			$filter = new KalturaMediaEntryFilter();
			$filter->idIn = $this->_mediaEntryId;
			$response = $mediaService->listAction($filter);
			$this->assertEquals(0, $response->totalCount);
			
			return;
		}
		
		$this->fail("Expecting exception");
	}
}

