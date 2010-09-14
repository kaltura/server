<?php
require_once("tests/bootstrapTests.php");

class DocumentTests extends PHPUnit_Framework_TestCase 
{
	private $createdEntries;
	private $unique_test_name;
	
	public function setUp() 
	{
		$this->createdEntries = array();
		$this->unique_test_name = 'DocumentEntryTests:' . time();
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
	
	public function testAddFromUploadedFile()
	{
		$ks = KalturaTestsHelpers::getNormalKs();
		$ksObj = ks::fromSecureString($ks);
		$ksUnique = $ksObj->getUniqueString();
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "ppt";
		
		$token = $ksUnique."_".$uniqueId.".".$ext;
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "ppt");
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy($currentPath."/../files/example.ppt", $fullPath);
		
	    $documentEntry = $this->prepareDocumentEntry();
	 
	    $documentService = KalturaTestsHelpers::getServiceInitializedForAction("document", "addFromUploadedFile");   
		$newDocumentEntry = $documentService->addFromUploadedFileAction(clone $documentEntry, $token);
		$this->createdEntries[] = $newDocumentEntry->id;
		
		$this->assertType("KalturaDocumentEntry", $newDocumentEntry);
		$this->assertDocumentEntry($documentEntry, $newDocumentEntry);
		
		return $newDocumentEntry;
	}
	
	public function testGet()
	{
		$documentEntry = $this->testAddFromUploadedFile();
		
	    $documentService = KalturaTestsHelpers::getServiceInitializedForAction("document", "get");   
		$getDocumentEntry = $documentService->getAction($documentEntry->id);
		
		$this->assertNotNull($getDocumentEntry);
		$this->assertDocumentEntry($documentEntry, $getDocumentEntry);
	}
	
	public function testUpdate()
	{
		$newDocumentEntry = $this->testAddFromUploadedFile();
		
	    $documentService = KalturaTestsHelpers::getServiceInitializedForAction("document", "update");
	    
		$documentEntry = $this->prepareDocumentEntry($newDocumentEntry);
		
		$updatedDocumentEntry = $documentService->updateAction($newDocumentEntry->id, clone $documentEntry);
		
		$this->assertEquals($expectedDocumentEntry->id, $actualDocumentEntry->id);
		$this->assertEquals($expectedDocumentEntry->partnerId, $actualDocumentEntry->partnerId);
		$this->assertEquals($expectedDocumentEntry->name, $actualDocumentEntry->name);
		$this->assertEquals($expectedDocumentEntry->documentType, $actualDocumentEntry->documentType);
		
		$this->assertNotEquals($updatedDocumentEntry->description, $newDocumentEntry->description);
		$this->assertNotEquals($updatedDocumentEntry->tags, $newDocumentEntry->tags);
		$this->assertNotEquals($updatedDocumentEntry->partnerData, $newDocumentEntry->partnerData);
		$this->assertNotEquals($updatedDocumentEntry->description, $newDocumentEntry->description);
	}
	
	public function testDelete()
	{
		$newDocumentEntry = $this->testAddFromUploadedFile();
		
	    $documentService = KalturaTestsHelpers::getServiceInitializedForAction("document", "delete");
	    
		$documentService->deleteAction($newDocumentEntry->id);
		
		try{
			$documentEntryGet = $documentService->getAction($newDocumentEntry->id);
		}
		catch(KalturaAPIException $e)
		{
			return;
		}
		
		$this->assertNull($documentEntryGet);
	}
	
	public function testConvertPptToSwf()
	{
		$newDocumentEntry = $this->testAddFromUploadedFile();
		
	    $documentService = KalturaTestsHelpers::getServiceInitializedForAction("document", "convertPptToSwf");
	    
		$documentService->convertPptToSwf($newDocumentEntry->id);
	}
	
	public function testList()
	{
		$addedItems = array();
		for($i = 0; $i < 5; $i++)
		{
			$newDocumentEntry = $this->testAddFromUploadedFile();
			$addedItems[$newDocumentEntry->id] = $newDocumentEntry; 
		}
		
		$filter = new KalturaDocumentEntryFilter();
		$filter->nameEqual = $this->unique_test_name;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 10;
		$pager->pageIndex = 0;
		
	    $documentService = KalturaTestsHelpers::getServiceInitializedForAction("document", "list");
	    $newList = $documentService->listAction($filter, $pager);
	
		$this->assertEquals(count($addedItems), $newList->totalCount);
		
		foreach($newList->objects as $documentEntry)
			$this->assertArrayHasKey($documentEntry->id, $addedItems);
	}

	public function testUpload()
	{
		$newBaseEntry = $this->testAddFromUploadedFile();
		
	    $documentService = KalturaTestsHelpers::getServiceInitializedForAction("document", "upload");
	
		$ks = KalturaTestsHelpers::getNormalKs();
		$ksObj = ks::fromSecureString($ks);
		$ksUnique = $ksObj->getUniqueString();
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "ppt";
		
		$token = "${ksUnique}_$uniqueId.$ext";
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "ppt");
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/example.ppt", $fullPath);
		
		$fileData = array(
			'name' => 'example.ppt',
			'tmp_name' => $fullPath,
			'error' => null,
			'size' => filesize($fullPath)
		);
		
		$documentService->uploadAction($fileData);
	}
	
	private function assertDocumentEntry($expectedDocumentEntry, $actualDocumentEntry)
	{
		$this->assertEquals($expectedDocumentEntry->partnerId, $actualDocumentEntry->partnerId);
		$this->assertEquals($expectedDocumentEntry->description, $actualDocumentEntry->description);
		$this->assertEquals($expectedDocumentEntry->name, $actualDocumentEntry->name);
	}
	
	private function prepareDocumentEntry($documentEntry = null)
	{
		if(is_null($documentEntry))
		{
			$documentEntry = new KalturaDocumentEntry();
			$documentEntry->partnerId = KalturaTestsHelpers::getPartner()->getId();
			$documentEntry->type = KalturaEntryType::DOCUMENT;
			$documentEntry->documentType = KalturaDocumentType::DOCUMENT;
			$documentEntry->createdAt = time();
		}
		else
		{
			$documentEntry = clone $documentEntry;
			
			$documentEntry->id = null;
			$documentEntry->partnerId = null;
			$documentEntry->status = null;
			$documentEntry->type = null;
			$documentEntry->documentType = null;
			$documentEntry->createdAt = null;
			$documentEntry->rank = null;
			$documentEntry->totalRank = null;
			$documentEntry->votes = null;
			$documentEntry->downloadUrl = null;
			$documentEntry->searchText = null;
			$documentEntry->version = null;
			$documentEntry->thumbnailUrl = null;
			$documentEntry->adminTags = null;
			$documentEntry->moderationStatus = null;
			$documentEntry->moderationCount = null;
		}
		
		$documentEntry->name = $this->unique_test_name;
		$documentEntry->description = KalturaTestsHelpers::getRandomString(30);
		$documentEntry->userId = KalturaTestsHelpers::getUserId();
		$documentEntry->tags = KalturaTestsHelpers::getRandomString(6) . ',' . KalturaTestsHelpers::getRandomString(6);
		$documentEntry->partnerData = KalturaTestsHelpers::getRandomString(30);
		$documentEntry->licenseType = KalturaLicenseType::PUBLIC_DOMAIN;
		
		return $documentEntry;
	}
}


?>