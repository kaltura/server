<?php
require_once("tests/bootstrapTests.php");

class WidgetTest extends PHPUnit_Framework_TestCase 
{
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
	}
	
	public function testAdd()
	{
	    $widget = WidgetTestsHelpers::prepareWidget();
	 
	    $widgetService = KalturaTestsHelpers::getServiceInitializedForAction("widget", "add", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getNormalKs());   
		$newWidget = $widgetService->addAction(clone $widget);
		
		$this->assertType("KalturaWidget", $newWidget);
		WidgetTestsHelpers::assertWidget($widget, $newWidget);
	}
	
	public function testGet()
	{
		$widget = WidgetTestsHelpers::createDummyWidget();
		
	    $widgetService = KalturaTestsHelpers::getServiceInitializedForAction("widget", "get");   
		$getWidget = $widgetService->getAction($widget->id);
		
		$this->assertNotNull($getWidget);
		WidgetTestsHelpers::assertWidget($widget, $getWidget);
	}
	
	public function testClone()
	{
		$newWidget = WidgetTestsHelpers::createDummyWidget();
		$newWidget->sourceWidgetId = $newWidget->id;
		$newWidget->id = null;
		
	    $widgetService = KalturaTestsHelpers::getServiceInitializedForAction("widget", "clone", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
		
		$clonedWidget = $widgetService->cloneAction(clone $newWidget);
		
		WidgetTestsHelpers::assertWidget($newWidget, $clonedWidget);
	}
	
	public function testUpdate()
	{
		$newWidget = WidgetTestsHelpers::createDummyWidget();
		
	    $widgetService = KalturaTestsHelpers::getServiceInitializedForAction("widget", "update", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
	    
		$widget = WidgetTestsHelpers::prepareWidget(null, $newWidget);
		
		$updatedWidget = $widgetService->updateAction($newWidget->id, clone $widget);
		
		$this->assertEquals($expectedWidget->id, $actualWidget->id);
		$this->assertEquals($expectedWidget->partnerId, $actualWidget->partnerId);
		$this->assertEquals($expectedWidget->createdAt, $actualWidget->createdAt);
		$this->assertEquals($expectedWidget->widgetHTML, $actualWidget->widgetHTML);
		
		$this->assertNotEquals($updatedWidget->partnerData, $newWidget->partnerData);
	}
	
	public function testList()
	{
		$addedItems = array();
		$uiConfId = WidgetTestsHelpers::getUiConfId();
		for($i = 0; $i < 5; $i++)
		{
			$newWidget = WidgetTestsHelpers::createDummyWidget($uiConfId);
			$addedItems[$newWidget->id] = $newWidget; 
		}
		
		$filter = new KalturaWidgetFilter();
		$filter->uiConfIdEqual = $uiConfId;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 10;
		$pager->pageIndex = 0;
		
	    $widgetService = KalturaTestsHelpers::getServiceInitializedForAction("widget", "list", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());
	    $newList = $widgetService->listAction($filter, $pager);
	
		$this->assertEquals(count($addedItems), $newList->totalCount);
		
		foreach($newList->objects as $widget)
			$this->assertArrayHasKey($widget->id, $addedItems);
	}
}


?>