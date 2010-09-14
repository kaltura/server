<?php
require_once("tests/bootstrapTests.php");

class DataTests extends PHPUnit_Framework_TestCase 
{
	private $createdEntries;
	private $unique_test_name;
	
	public function setUp() 
	{
		$this->createdEntries = array();
		$this->unique_test_name = 'DataEntryTests:' . time();
	}
	
	public function tearDown() 
	{
		parent::tearDown();
		
		foreach($this->createdEntries as $entryId)
		{
			$entry = entryPeer::retrieveByPKNoFilter($entryId);
				
			if ($entry)
				$entry->delete();
		}
		$this->createdEntries = array();
	}
	
	public function testAdd()
	{
	    $dataEntry = $this->prepareDataEntry();
	 
	    $dataService = KalturaTestsHelpers::getServiceInitializedForAction("data", "add");   
		$newDataEntry = $dataService->addAction(clone $dataEntry);
		$this->createdEntries[] = $newDataEntry->id;
		
		$this->assertType("KalturaDataEntry", $newDataEntry);
		$this->assertDataEntry($dataEntry, $newDataEntry);
		
		return $newDataEntry;
	}
	
	public function testGet()
	{
		$dataEntry = $this->testAdd();
		
	    $dataService = KalturaTestsHelpers::getServiceInitializedForAction("data", "get");   
		$getDataEntry = $dataService->getAction($dataEntry->id);
		
		$this->assertNotNull($getDataEntry);
		$this->assertDataEntry($dataEntry, $getDataEntry);
	}
	
	public function testUpdate()
	{
		$newDataEntry = $this->testAdd();
		
	    $dataService = KalturaTestsHelpers::getServiceInitializedForAction("data", "update");
	    
		$dataEntry = $this->prepareDataEntry($newDataEntry);
		
		$updatedDataEntry = $dataService->updateAction($newDataEntry->id, clone $dataEntry);
		
		$this->assertEquals($expectedDataEntry->id, $actualDataEntry->id);
		$this->assertEquals($expectedDataEntry->partnerId, $actualDataEntry->partnerId);
		$this->assertEquals($expectedDataEntry->name, $actualDataEntry->name);
		
		$this->assertNotEquals($updatedDataEntry->description, $newDataEntry->description);
		$this->assertNotEquals($updatedDataEntry->tags, $newDataEntry->tags);
		$this->assertNotEquals($updatedDataEntry->partnerData, $newDataEntry->partnerData);
		$this->assertNotEquals($updatedDataEntry->description, $newDataEntry->description);
		$this->assertNotEquals($updatedDataEntry->dataContent, $newDataEntry->dataContent);
	}
	
	public function testDelete()
	{
		$newDataEntry = $this->testAdd();
		
	    $dataService = KalturaTestsHelpers::getServiceInitializedForAction("data", "delete");
	    
		$dataService->deleteAction($newDataEntry->id);
		
		try{
			$dataEntryGet = $dataService->getAction($newDataEntry->id);
		}
		catch(KalturaAPIException $e)
		{
			return;
		}
		
		$this->assertNull($dataEntryGet);
	}
	
	public function testList()
	{
		$addedItems = array();
		for($i = 0; $i < 5; $i++)
		{
			$newDataEntry = $this->testAdd();
			$addedItems[$newDataEntry->id] = $newDataEntry; 
		}
		
		$filter = new KalturaDataEntryFilter();
		$filter->nameEqual = $this->unique_test_name;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 10;
		$pager->pageIndex = 0;
		
	    $dataService = KalturaTestsHelpers::getServiceInitializedForAction("data", "list");
	    $newList = $dataService->listAction($filter, $pager);
	
		$this->assertEquals(count($addedItems), $newList->totalCount);
		
		foreach($newList->objects as $dataEntry)
			$this->assertArrayHasKey($dataEntry->id, $addedItems);
	}
	
	private function assertDataEntry($expectedDataEntry, $actualDataEntry)
	{
		$this->assertEquals($expectedDataEntry->partnerId, $actualDataEntry->partnerId);
		$this->assertEquals($expectedDataEntry->description, $actualDataEntry->description);
		$this->assertEquals($expectedDataEntry->partnerData, $actualDataEntry->partnerData);
		$this->assertEquals($expectedDataEntry->description, $actualDataEntry->description);
		$this->assertEquals($expectedDataEntry->name, $actualDataEntry->name);
		$this->assertEquals($expectedDataEntry->dataContent, $actualDataEntry->dataContent);
	}
	
	private function prepareDataEntry($dataEntry = null)
	{
		if(is_null($dataEntry))
		{
			$dataEntry = new KalturaDataEntry();
			$dataEntry->partnerId = KalturaTestsHelpers::getPartner()->getId();
			$dataEntry->type = KalturaEntryType::DATA;
			$dataEntry->createdAt = time();
		}
		else
		{
			$dataEntry = clone $dataEntry;
			
			$dataEntry->id = null;
			$dataEntry->partnerId = null;
			$dataEntry->status = null;
			$dataEntry->type = null;
			$dataEntry->createdAt = null;
			$dataEntry->rank = null;
			$dataEntry->totalRank = null;
			$dataEntry->votes = null;
			$dataEntry->downloadUrl = null;
			$dataEntry->searchText = null;
			$dataEntry->version = null;
			$dataEntry->thumbnailUrl = null;
			$dataEntry->adminTags = null;
			$dataEntry->moderationCount = null;
		}
		
		$dataEntry->name = $this->unique_test_name;
		$dataEntry->description = KalturaTestsHelpers::getRandomString(30);
		$dataEntry->userId = KalturaTestsHelpers::getUserId();
		$dataEntry->tags = KalturaTestsHelpers::getRandomString(6) . ',' . KalturaTestsHelpers::getRandomString(6);
		$dataEntry->partnerData = KalturaTestsHelpers::getRandomString(30);
		$dataEntry->licenseType = KalturaLicenseType::PUBLIC_DOMAIN;
		$dataEntry->dataContent = KalturaTestsHelpers::getRandomString(30);
		
		return $dataEntry;
	}
}


?>