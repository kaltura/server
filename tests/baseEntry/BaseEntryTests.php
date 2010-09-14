<?php
require_once("tests/bootstrapTests.php");

class BaseEntryTests extends PHPUnit_Framework_TestCase 
{
	private $unique_test_name;
	
	public function setUp() 
	{
		$this->unique_test_name = 'BaseEntryTests:' . time();
	}
	
	public function tearDown() 
	{
		$criteria = new Criteria();
		$criteria->add(BaseentryPeer::NAME, $this->unique_test_name, Criteria::EQUAL);
		$baseEntries = BaseentryPeer::doSelect($criteria);
		foreach($baseEntries as $baseEntry)
			$baseEntry->delete();
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
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "addFromUploadedFile");
	    
	    $baseEntry = $this->prepareBaseEntry();
	    
		$newBaseEntry = $baseEntryService->addFromUploadedFileAction(clone $baseEntry, $token);
		
		$this->assertType("KalturaBaseEntry", $newBaseEntry);
		
		$this->assertEquals($baseEntry->name, $newBaseEntry->name);
		$this->assertEquals(0, $newBaseEntry->moderationCount);
		
		return $newBaseEntry;
	}
	
	public function testGetBaseEntry()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "get");
	    
		$baseEntryGet = $baseEntryService->getAction($newBaseEntry->id);
		$this->assertEquals($newBaseEntry->name, $baseEntryGet->name);
	}
	
	public function testDeleteBaseEntry()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "delete");
	    
		$baseEntryService->deleteAction($newBaseEntry->id);
		
		try{
			$baseEntryGet = $baseEntryService->getAction($newBaseEntry->id);
		}
		catch(KalturaAPIException $e)
		{
			return;
		}
		
		$this->assertNull($baseEntryGet);
	}
	
	public function testUpdate()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "update");
	    
		$baseEntry = $this->prepareBaseEntry($newBaseEntry);
		
		$updatedBaseEntry = $baseEntryService->updateAction($newBaseEntry->id, clone $baseEntry);
		
		$this->assertBaseEntry($newBaseEntry, $updatedBaseEntry);
		
		$this->assertNotEquals($updatedBaseEntry->description, $newBaseEntry->description);
		$this->assertNotEquals($updatedBaseEntry->tags, $newBaseEntry->tags);
		$this->assertNotEquals($updatedBaseEntry->partnerData, $newBaseEntry->partnerData);
		$this->assertNotEquals($updatedBaseEntry->description, $newBaseEntry->description);
	}
	
	public function testGetByIds()
	{
		$addedItems = array();
		for($i = 0; $i < 5; $i++)
		{
			$newBaseEntry = $this->testAddFromUploadedFile();
			$addedItems[$newBaseEntry->id] = $newBaseEntry; 
		}
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "getByIds");
	    $newList = $baseEntryService->getByIdsAction(join(',', array_keys($addedItems)));
	
		$this->assertEquals(count($addedItems), count($newList));
		
		foreach($newList as $baseEntry)
			$this->assertArrayHasKey($baseEntry->id, $addedItems);
	}
	
	public function testList()
	{
		$addedItems = array();
		for($i = 0; $i < 5; $i++)
		{
			$newBaseEntry = $this->testAddFromUploadedFile();
			$addedItems[$newBaseEntry->id] = $newBaseEntry; 
		}
		
		$filter = new KalturaBaseEntryFilter();
		$filter->nameEqual = $this->unique_test_name;
		$filter->statusEqual = KalturaEntryStatus::PRECONVERT; // by default ready status is returned, but the created entries were not converted
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 10;
		$pager->pageIndex = 0;
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "list");
	    $newList = $baseEntryService->listAction($filter, $pager);
	
		$this->assertEquals(count($addedItems), $newList->totalCount);
		
		foreach($newList->objects as $baseEntry)
			$this->assertArrayHasKey($baseEntry->id, $addedItems);
	}
	
	public function testUpload()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "upload");
	
		$ks = KalturaTestsHelpers::getNormalKs();
		$ksObj = ks::fromSecureString($ks);
		$ksUnique = $ksObj->getUniqueString();
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "flv";
		
		$token = "${ksUnique}_$uniqueId.$ext";
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "flv");
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/kaltura_logo_animated_black.flv", $fullPath);
		
		$fileData = array(
			'name' => 'kaltura_logo_animated_black.flv',
			'tmp_name' => $fullPath,
			'error' => null,
			'size' => filesize($fullPath)
		);
		
		$baseEntryService->uploadAction($fileData);
	}
	
	public function testUpdateThumbnailJpeg()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "updateThumbnailJpeg");
	    
		$ks = KalturaTestsHelpers::getNormalKs();
		$ksObj = ks::fromSecureString($ks);
		$ksUnique = $ksObj->getUniqueString();
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "jpg";
		
		$token = "${ksUnique}_$uniqueId.$ext";
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "jpg");
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/thumb.jpg", $fullPath);
		
		$fileData = array(
			'name' => 'thumb.jpg',
			'tmp_name' => $fullPath,
			'error' => null,
			'size' => filesize($fullPath)
		);
		
		$baseEntryService->updateThumbnailJpegAction($newBaseEntry->id, $fileData);
	}
	
	public function testFlag()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "flag");
	    
	    $flag = MediaTestsHelpers::prepareModerationFlagForEntry($newBaseEntry->id);
		$baseEntryService->flagAction($flag);
		
		return $newBaseEntry;
	}
	
	public function testReject()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "reject", null, null, KalturaTestsHelpers::getAdminKs());
	    
		$baseEntryService->rejectAction($newBaseEntry->id);
	}
	
	public function testApprove()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "approve", null, null, KalturaTestsHelpers::getAdminKs());
	    
		$baseEntryService->approveAction($newBaseEntry->id);
	}
	
	public function testListFlags()
	{
		$newBaseEntry = $this->testFlag();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "listFlags", null, null, KalturaTestsHelpers::getAdminKs());
	    
		$flagsList = $baseEntryService->listFlags($newBaseEntry->id);
	}
	
	public function testAnonymousRank()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "anonymousRank");
	    
		$baseEntryService->anonymousRankAction($newBaseEntry->id, 3);
	}
	
	public function testUpdateReadonly()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "update");
	    
	    // try to update partnerId
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->partnerId = KalturaTestsHelpers::getPartner()->getId();
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update partnerId should not work');
		
		
	    // try to update status
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->status = KalturaEntryStatus::READY;
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update status should not work');
		
		
	    // try to update type
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->type = KalturaEntryType::DOCUMENT;
		try{
			$updatedBaseEntry = $baseEntryService->updateAction($newBaseEntry->id, clone $baseEntry);
			$this->assertNotEquals($updatedBaseEntry->type, $baseEntry->type);
		}catch(KalturaAPIException $e){
			$this->fail('Update type should not throw exception');
		}
		
		
	    // try to update createdAt
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->createdAt = time();
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update createdAt should not work');
		
		
	    // try to update rank
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->rank = 0;
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update rank should not work');
		

	    // try to update totalRank
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->totalRank = 0;
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update totalRank should not work');
		
		
	    // try to update votes
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->votes = 0;
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update votes should not work');

	    // try to update downloadUrl
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->downloadUrl = KalturaTestsHelpers::getRandomString(10);
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update downloadUrl should not work');
		
		
	    // try to update searchText
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->searchText = KalturaTestsHelpers::getRandomString(10);
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update searchText should not work');
			
			
	    // try to update version
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->version = 0;
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update version should not work');
		
		
	    // try to update thumbnailUrl
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->thumbnailUrl = KalturaTestsHelpers::getRandomString(10);
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update thumbnailUrl should not work');
			
		
	    // try to update adminTags
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->adminTags = KalturaTestsHelpers::getRandomString(10);
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update adminTags should not work');
			
		// try to update moderationCount
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->moderationCount = KalturaTestsHelpers::getRandomNumber(10, 20);
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update moderationCount should not work');
			
		// try to update moderationStatus
		$baseEntry = new KalturaBaseEntry();
		$baseEntry->moderationStatus = KalturaEntryModerationStatus::REJECTED;
		$exceptioned = false;
		try{
			$baseEntryService->updateAction($newBaseEntry->id, $baseEntry);
		}catch(KalturaAPIException $e){
			$exceptioned = true;
		}
		if(!$exceptioned)
			$this->fail('Update moderationStatus should not work');
	}
	
	public function testMaxCategories()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();

		$baseEntryService = KalturaTestsHelpers::getServiceInitializedForAction("baseEntry", "update");
	    
		$baseEntry = $this->prepareBaseEntry($newBaseEntry);
		$baseEntry->categories = "cat1, cat2, cat3, cat4, cat5, cat6, cat7, cat8";
		$updatedBaseEntry = $baseEntryService->updateAction($newBaseEntry->id, clone $baseEntry);
		$this->assertBaseEntry($newBaseEntry, $updatedBaseEntry);
		
		
		$baseEntry->categories .= ", cat9";
		try
		{
			$baseEntryService->updateAction($newBaseEntry->id, clone $baseEntry);
			$this->fail();
		}
		catch (KalturaAPIException $ex)
		{
			$this->assertEquals("MAX_CATEGORIES_FOR_ENTRY_REACHED", $ex->getCode()); 
		}
	}
	private function assertBaseEntry($expectedBaseEntry, $actualBaseEntry)
	{
		$this->assertEquals($expectedBaseEntry->partnerId, $actualBaseEntry->partnerId);
		$this->assertEquals($expectedBaseEntry->type, $actualBaseEntry->type);
		$this->assertEquals($expectedBaseEntry->createdAt, $actualBaseEntry->createdAt);
		$this->assertEquals($expectedBaseEntry->downloadUrl, $actualBaseEntry->downloadUrl);
		$this->assertEquals($expectedBaseEntry->version, $actualBaseEntry->version);
		$this->assertEquals($expectedBaseEntry->thumbnailUrl, $actualBaseEntry->thumbnailUrl);
		$this->assertEquals($expectedBaseEntry->adminTags, $actualBaseEntry->adminTags);
	}
	
	private function prepareBaseEntry($mediaEntry = null)
	{
		if(is_null($mediaEntry))
		{
			$mediaEntry = new KalturaBaseEntry();
			$mediaEntry->partnerId = KalturaTestsHelpers::getPartner()->getId();
			$mediaEntry->type = KalturaEntryType::DATA;
			$mediaEntry->createdAt = time();
		}
		else
		{
			$mediaEntry = clone $mediaEntry;
			
			$mediaEntry->id = null;
			$mediaEntry->partnerId = null;
			$mediaEntry->status = null;
			$mediaEntry->type = null;
			$mediaEntry->createdAt = null;
			$mediaEntry->rank = null;
			$mediaEntry->totalRank = null;
			$mediaEntry->votes = null;
			$mediaEntry->downloadUrl = null;
			$mediaEntry->searchText = null;
			$mediaEntry->version = null;
			$mediaEntry->thumbnailUrl = null;
			$mediaEntry->adminTags = null; // not updatable with user session
			$mediaEntry->moderationStatus = null;
			$mediaEntry->moderationCount = null;
		}
		
		$mediaEntry->name = $this->unique_test_name;
		$mediaEntry->description = KalturaTestsHelpers::getRandomString(30);
		$mediaEntry->userId = KalturaTestsHelpers::getUserId();
		$mediaEntry->tags = KalturaTestsHelpers::getRandomString(6) . ',' . KalturaTestsHelpers::getRandomString(6);
		$mediaEntry->partnerData = KalturaTestsHelpers::getRandomString(30);
		$mediaEntry->licenseType = KalturaLicenseType::PUBLIC_DOMAIN;
		return $mediaEntry;
	}
}


?>