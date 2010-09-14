<?php
require_once("tests/bootstrapTests.php");

class UiConfTest extends PHPUnit_Framework_TestCase 
{
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
	}
	
	public function testAdd()
	{
	    $uiConf = WidgetTestsHelpers::prepareUiConf();
	 
	    $uiConfService = KalturaTestsHelpers::getServiceInitializedForAction("uiconf", "add", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());   
		$newUiConf = $uiConfService->addAction(clone $uiConf);
		
		$this->assertType("KalturaUiConf", $newUiConf);
		WidgetTestsHelpers::assertUiConf($uiConf, $newUiConf);
	}
	
	public function testGet()
	{
		$uiConf = WidgetTestsHelpers::createUiConf();
		
	    $uiConfService = KalturaTestsHelpers::getServiceInitializedForAction("uiconf", "get");   
		$getUiConf = $uiConfService->getAction($uiConf->id);
		
		$this->assertNotNull($getUiConf);
		WidgetTestsHelpers::assertUiConf($uiConf, $getUiConf);
	}
	
	public function testUpdate()
	{
		$newUiConf = WidgetTestsHelpers::createUiConf();
		
	    $uiConfService = KalturaTestsHelpers::getServiceInitializedForAction("uiconf", "update", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
	    
		$uiConf = WidgetTestsHelpers::prepareUiConf($newUiConf);
		
		$updatedUiConf = $uiConfService->updateAction($newUiConf->id, clone $uiConf);
		
		$this->assertEquals($expectedUiConf->id, $actualUiConf->id);
		$this->assertEquals($expectedUiConf->partnerId, $actualUiConf->partnerId);
		$this->assertEquals($expectedUiConf->createdAt, $actualUiConf->createdAt);
	}
	
	public function testClone()
	{
		$uiConf = WidgetTestsHelpers::createUiConf();
		
		$uiConfService = KalturaTestsHelpers::getServiceInitializedForAction("uiconf", "clone", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
		$cloneUiConf = $uiConfService->cloneAction($uiConf->id);
		
		WidgetTestsHelpers::assertUiConf($uiConf, $cloneUiConf);
	}
	
	public function testDelete()
	{
		$uiConf = WidgetTestsHelpers::createUiConf();
		
		$uiConfService = KalturaTestsHelpers::getServiceInitializedForAction("uiconf", "delete", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
		$uiConfService->deleteAction($uiConf->id);
		
	    $uiConfService = KalturaTestsHelpers::getServiceInitializedForAction("uiconf", "get");
	    $getUiConf = null;  
	    try{
			$getUiConf = $uiConfService->getAction($uiConf->id);
	    }catch(KalturaAPIException $e){
	    	return;
	    }
		
		$this->assertNull($getUiConf);
	}
	
	public function testList()
	{
	    $uiConfService = KalturaTestsHelpers::getServiceInitializedForAction("uiconf", "list", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
	    $originalList = $uiConfService->listAction($filter, $pager);
	    
		$addedItems = array();
		for($i = 0; $i < 5; $i++)
		{
			$newUiConf = WidgetTestsHelpers::createUiConf();
			$addedItems[$newUiConf->id] = $newUiConf; 
		}
		
		$filter = new KalturaUiConfFilter();
		$filter->nameEqual = $this->unique_test_name;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 10;
		$pager->pageIndex = 0;
		
	    $newList = $uiConfService->listAction($filter, $pager);
	
		$this->assertEquals(count($addedItems) + $originalList->totalCount, $newList->totalCount);
		
		foreach($newList->objects as $uiConf)
			$this->assertArrayHasKey($uiConf->id, $addedItems);
	}
}


?>