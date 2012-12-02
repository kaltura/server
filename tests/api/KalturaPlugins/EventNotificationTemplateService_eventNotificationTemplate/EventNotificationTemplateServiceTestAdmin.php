<?php

require_once(dirname(__FILE__) . '/../../../bootstrap.php');

/**
 * eventNotificationTemplate service test case.
 */
class EventNotificationTemplateServiceAdminTest extends KalturaApiTestCase
{
	/**
	 * Tests eventNotificationTemplate->add action for admin partner
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate overwrite configuration object
	 * @param int $impersonatedPartnerId
	 * @param KalturaEventNotificationTemplate $reference
	 * 
	 * @return int
	 * @dataProvider provideData
	 */	
	public function testAdminAdd($eventNotificationTemplate, $impersonatedPartnerId , $reference)
	{
		//Impersonate partner
		$this->impersonate($impersonatedPartnerId);
		//Perform action
		$eventNotificationTemplate->systemName = uniqid('unit_test');
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
	 * Tests eventNotificationTemplate->get action for admin partner
	 * @param int $id
	 * @param int $impersonatedPartnerId
	 * @param KalturaEventNotificationTemplate $reference
	 * @return int
	 * @depends testAdminAdd with data set #1
	 * @dataProvider provideData
	 */
	public function testAdminGet($id, $impersonatedPartnerId, KalturaEventNotificationTemplate $reference)
	{
		$this->impersonate($impersonatedPartnerId);
		$resultObject = $this->client->eventNotificationTemplate->get($id);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplate', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplate', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($resultObject);
		
		return $resultObject->id;
	}
	
	/**
	 * Validates testGet results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaEventNotificationTemplate $resultObject
	 */
	protected function validateGet(KalturaEventNotificationTemplate $resultObject){}
	
	/**
	 * Tests eventNotificationTemplate->update action for admin partner
	 * @param int $id
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate
	 * @param int $impersonatedPartnerId
	 * @param KalturaEventNotificationTemplate $reference
	 * @return int
	 * @depends testAdminGet with data set #0
	 * @dataProvider provideData
	 */
	public function testAdminUpdate ($id, KalturaEventNotificationTemplate $eventNotificationTemplate, $impersonatedPartnerId, KalturaEventNotificationTemplate $reference)
	{
		$this->impersonate($impersonatedPartnerId);
		$resultObject = $this->client->eventNotificationTemplate->update($id, $eventNotificationTemplate);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplate', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplate', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateUpdate($resultObject);
		
		return $resultObject->id;
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
	 * @param int $impersonatedPartnerId
	 * @depends testAdminGet with data set #0
	 * @dataProvider provideData
	 */
	public function testAdminDelete($id, $impersonatedPartnerId)
	{
		$this->impersonate($impersonatedPartnerId);
		$resultObject = $this->client->eventNotificationTemplate->delete($id);
	}
	

	/**
	 * Tests eventNotificationTemplate->listbypartner action
	 * @param KalturaPartnerFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaEventNotificationTemplateListResponse $reference
	 * @dataProvider provideData
	 */
	public function testAdminListbypartner(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null, KalturaEventNotificationTemplateListResponse $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->listbypartner($filter, $pager, $reference);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplateListResponse', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplateListResponse', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		// TODO - add here your own validations
	}

	/**
	 * Set different partner ID for client config
	 * @param int $impersonatedPartnerId
	 */
	protected function impersonate ($impersonatedPartnerId)
	{
		//Impersonate partner
		$config = $this->client->getConfig();
		$config->partnerId = $impersonatedPartnerId;
		$this->client->setConfig($config);
	}
	
}

