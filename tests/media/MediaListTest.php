<?php
require_once("tests/bootstrapTests.php");

class MediaListTest extends PHPUnit_Framework_TestCase
{
	public function setUp ()
	{
		parent::setUp();
	}
	
	public function tearDown ()
	{
		parent::tearDown();
	}
	
	public function __construct ()
	{
	}
	
	public function testList()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "list");
	    
	    $mediaEntry = MediaTestsHelpers::prepareMediaEntry();
		$url = MediaTestsHelpers::prepareDummyUrl();
		$newMediaEntry = $mediaService->addFromUrlAction(clone $mediaEntry, $url);
		
		$filter = new KalturaMediaEntryFilter();
		$filter->orderBy = KalturaMediaEntryOrderBy::CREATED_AT_DESC;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 10;

		$response = $mediaService->listAction($filter, $pager);
		$this->assertGreaterThan(1, $response->objects->count);
		
		$found = false;
		foreach($response->objects as $object)
		{
			$this->assertType("KalturaMediaEntry", $object);
			if ($object->id == $newMediaEntry->id)
				$found = true;
		}
		
		$this->assertTrue($found, "Entry not found in list");
	}
}

