<?php

/**
 * eventNotificationTemplate service base test case.
 */
abstract class EventNotificationTemplateServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Tests eventNotificationTemplate->add action
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate 
	 * @param KalturaEventNotificationTemplate $reference
	 * @return KalturaEventNotificationTemplate
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationTemplate $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->add($eventNotificationTemplate);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplate', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplate', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($resultObject);
		
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaEventNotificationTemplate $resultObject
	 */
	protected function validateAdd(KalturaEventNotificationTemplate $resultObject){}

	/**
	 * Tests eventNotificationTemplate->get action
	 * @param int $id 
	 * @param KalturaEventNotificationTemplate $reference
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaEventNotificationTemplate $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->get($id);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplate', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplate', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($resultObject);
	}

	/**
	 * Validates testGet results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaEventNotificationTemplate $resultObject
	 */
	protected function validateGet(KalturaEventNotificationTemplate $resultObject){}

	/**
	 * Tests eventNotificationTemplate->update action
	 * @param int $id 
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate 
	 * @param KalturaEventNotificationTemplate $reference
	 * @depends testAdd with data set #1
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationTemplate $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->update($id, $eventNotificationTemplate);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplate', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplate', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateUpdate($resultObject);
	}

	/**
	 * Validates testUpdate results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaEventNotificationTemplate $resultObject
	 */
	protected function validateUpdate(KalturaEventNotificationTemplate $resultObject){}

	/**
	 * Tests eventNotificationTemplate->delete action
	 * @param int $id 
	 * @param KalturaEventNotificationTemplate $reference
	 * @depends testAdd with data set #2
	 * @dataProvider provideData
	 */
	public function testDelete($id, KalturaEventNotificationTemplate $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->delete($id);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplate', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplate', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateDelete($resultObject);
	}

	/**
	 * Validates testDelete results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaEventNotificationTemplate $resultObject
	 */
	protected function validateDelete(KalturaEventNotificationTemplate $resultObject){}

	/**
	 * Tests eventNotificationTemplate->listAction action
	 * @param KalturaEventNotificationTemplateFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaEventNotificationTemplateListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaEventNotificationTemplateFilter $filter = null, KalturaFilterPager $pager = null, KalturaEventNotificationTemplateListResponse $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->listAction($filter, $pager);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplateListResponse', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplateListResponse', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($resultObject);
	}

	/**
	 * Validates testListAction results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaEventNotificationTemplateListResponse $resultObject
	 */
	protected function validateListAction(KalturaEventNotificationTemplateListResponse $resultObject){}

}
