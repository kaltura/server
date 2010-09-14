<?php
require_once("tests/bootstrapTests.php");

class playListTest extends PHPUnit_Framework_TestCase 
{
	private $createdEntries;
	private $unique_test_name;
	
	public function setUp() 
	{
		$this->createdEntries = array();
		$this->unique_test_name = 'PlayListTests:' . time();
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
	    $playList = $this->preparePlayList();
	 
	    $playListService = KalturaTestsHelpers::getServiceInitializedForAction("playlist", "add", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());   
		$newPlayList = $playListService->addAction(clone $playList);
		$this->createdEntries[] = $newPlayList->id;
		
		$this->assertType("KalturaPlaylist", $newPlayList);
		$this->assertPlayList($playList, $newPlayList);
		
		return $newPlayList;
	}
	
	public function testGet()
	{
		$playList = $this->testAdd();
		
	    $playListService = KalturaTestsHelpers::getServiceInitializedForAction("playlist", "get");   
		$getPlayList = $playListService->getAction($playList->id);
		
		$this->assertNotNull($getPlayList);
		$this->assertPlayList($playList, $getPlayList);
	}
	
	public function testUpdate()
	{
		$newPlayList = $this->testAdd();
		
	    $playListService = KalturaTestsHelpers::getServiceInitializedForAction("playlist", "update", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
	    
		$playList = $this->preparePlayList($newPlayList);
		
		$updatedPlayList = $playListService->updateAction($newPlayList->id, clone $playList);
		
		$this->assertEquals($expectedPlayList->id, $actualPlayList->id);
		$this->assertEquals($expectedPlayList->partnerId, $actualPlayList->partnerId);
		$this->assertEquals($expectedPlayList->name, $actualPlayList->name);
		
		$this->assertEquals($expectedPlayList->playlistContent, $actualPlayList->playlistContent);
		$this->assertEquals($expectedPlayList->playlistType, $actualPlayList->playlistType);
		$this->assertEquals($expectedPlayList->plays, $actualPlayList->plays);
		$this->assertEquals($expectedPlayList->views, $actualPlayList->views);
		$this->assertEquals($expectedPlayList->duration, $actualPlayList->duration);
		$this->assertEquals($expectedPlayList->version, $actualPlayList->version);
		
		$this->assertNotEquals($updatedPlayList->description, $newPlayList->description);
		$this->assertNotEquals($updatedPlayList->tags, $newPlayList->tags);
		$this->assertNotEquals($updatedPlayList->partnerData, $newPlayList->partnerData);
		$this->assertNotEquals($updatedPlayList->description, $newPlayList->description);
	}
	
	public function testUpdateReadonly()
	{
		$newPlayList = $this->testAdd();
		
	    $playListService = KalturaTestsHelpers::getServiceInitializedForAction("playlist", "update", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
	    
	    // try to update partnerId
		$playList = new KalturaPlaylist();
		$playList->partnerId = KalturaTestsHelpers::getPartner()->getId();
		$exceptioned = false;
		try{
			$playListService->updateAction($newPlayList->id, $playList);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update partnerId should not work');
			
	    // try to update plays
		$playList = new KalturaPlaylist();
		$playList->plays = 10;
		$exceptioned = false;
		try{
			$playListService->updateAction($newPlayList->id, $playList);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update plays should not work');
			
	    // try to update views
		$playList = new KalturaPlaylist();
		$playList->views = 10;
		$exceptioned = false;
		try{
			$playListService->updateAction($newPlayList->id, $playList);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update views should not work');
			
	    // try to update duration
		$playList = new KalturaPlaylist();
		$playList->duration = 10;
		$exceptioned = false;
		try{
			$playListService->updateAction($newPlayList->id, $playList);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update duration should not work');
			
	    // try to update version
		$playList = new KalturaPlaylist();
		$playList->version = 10;
		$exceptioned = false;
		try{
			$playListService->updateAction($newPlayList->id, $playList);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update version should not work');
	}
	
	public function testDelete()
	{
		$newPlayList = $this->testAdd();
		
	    $playListService = KalturaTestsHelpers::getServiceInitializedForAction("playlist", "delete", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
	    
		$playListService->deleteAction($newPlayList->id);
		
		try{
			$playListGet = $playListService->getAction($newPlayList->id);
		}
		catch(KalturaAPIException $e)
		{
			return;
		}
		
		$this->assertNull($playListGet);
	}
	
	public function testExecute()
	{
		$newPlayList = $this->testAdd();
		
	    $playListService = KalturaTestsHelpers::getServiceInitializedForAction("playlist", "execute");
		$playList = $playListService->executeAction($newPlayList->id);
		$this->assertNotNull($playList);
	}
	
	public function testExecuteFromContent()
	{
		$newPlayList = $this->testAdd();
		
	    $playListService = KalturaTestsHelpers::getServiceInitializedForAction("playlist", "executeFromContent", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
		$playList = $playListService->executeFromContentAction($newPlayList->playlistType, $newPlayList->playlistContent);
		$this->assertNotNull($playList);
	}
	
	public function testGetStatsFromContent()
	{
		$newPlayList = $this->testAdd();
		
	    $playListService = KalturaTestsHelpers::getServiceInitializedForAction("playlist", "getStatsFromContent", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
		$playList = $playListService->getStatsFromContentAction($newPlayList->playlistType, $newPlayList->playlistContent);
		$this->assertNotNull($playList);
	}
	
	public function testList()
	{
		$addedItems = array();
		for($i = 0; $i < 5; $i++)
		{
			$newPlayList = $this->testAdd();
			$addedItems[$newPlayList->id] = $newPlayList; 
		}
		
		$filter = new KalturaPlaylistFilter();
		$filter->nameEqual = $this->unique_test_name;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 10;
		$pager->pageIndex = 0;
		
	    $playListService = KalturaTestsHelpers::getServiceInitializedForAction("playlist", "list", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
	    $newList = $playListService->listAction($filter, $pager);
	
		$this->assertEquals(count($addedItems), $newList->totalCount);
		
		foreach($newList->objects as $playList)
			$this->assertArrayHasKey($playList->id, $addedItems);
	}
	
	private function assertPlayList($expectedPlayList, $actualPlayList)
	{
		$this->assertEquals($expectedPlayList->partnerId, $actualPlayList->partnerId);
		$this->assertEquals($expectedPlayList->description, $actualPlayList->description);
		$this->assertEquals($expectedPlayList->partnerData, $actualPlayList->partnerData);
		$this->assertEquals($expectedPlayList->description, $actualPlayList->description);
		$this->assertEquals($expectedPlayList->name, $actualPlayList->name);
		
		$this->assertEquals($expectedPlayList->playlistContent, $actualPlayList->playlistContent);
		$this->assertEquals($expectedPlayList->playlistType, $actualPlayList->playlistType);
		$this->assertEquals($expectedPlayList->plays, $actualPlayList->plays);
		$this->assertEquals($expectedPlayList->views, $actualPlayList->views);
		$this->assertEquals($expectedPlayList->duration, $actualPlayList->duration);
	}
	
	private function preparePlayList($playList = null)
	{
		if(is_null($playList))
		{
			$playList = new KalturaPlaylist();
			$playList->partnerId = KalturaTestsHelpers::getPartner()->getId();
			$playList->type = KalturaEntryType::DATA;
			$playList->createdAt = time();
			
			$playList->plays = 0;
			$playList->views = 0;
			$playList->duration = 0;
			$playList->version = 1;
		}
		else
		{
			$playList = clone $playList;
			
			$playList->id = null;
			$playList->partnerId = null;
			$playList->status = null;
			$playList->type = null;
			$playList->createdAt = null;
			
			$playList->plays = null;
			$playList->views = null;
			$playList->duration = null;
			$playList->version = null;
			
			$playList->rank = null;
			$playList->totalRank = null;
			$playList->votes = null;
			$playList->downloadUrl = null;
			$playList->searchText = null;
			$playList->version = null;
			$playList->thumbnailUrl = null;
			$playList->adminTags = null;
			$playList->moderationCount = null;
		}
		
		$playList->name = $this->unique_test_name;
		$playList->description = KalturaTestsHelpers::getRandomString(30);
		$playList->userId = KalturaTestsHelpers::getUserId();
		$playList->tags = KalturaTestsHelpers::getRandomString(6) . ',' . KalturaTestsHelpers::getRandomString(6);
		$playList->partnerData = KalturaTestsHelpers::getRandomString(30);
		$playList->licenseType = KalturaLicenseType::PUBLIC_DOMAIN;
		
		$playList->playlistContent = KalturaTestsHelpers::getRandomString(30);
		$playList->playlistType = KalturaPlaylistType::STATIC_LIST;
		
		return $playList;
	}
}


?>