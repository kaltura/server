<?php

require_once(dirname(__FILE__) . '/../../../bootstrap.php');

/**
 * eventNotificationTemplate service test case.
 */
class EventNotificationTemplateServiceAdminTest extends EventNotificationTemplateServiceTestBase
{
	/**
	 * Tests eventNotificationTemplate->cloneAction action
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate overwrite configuration object
	 * @param int $impersonatedPartnerId
	 * @param KalturaEventNotificationTemplate $reference
	 * @dataProvider provideData
	 */	
	public function testAdminAdd($eventNotificationTemplate, $impersonatedPartnerId , $reference)
	{
		//Impersonate partner
		$config = $this->client->getConfig();
		$config->partnerId = $impersonatedPartnerId;
		$this->client->setConfig($config);
		//Perform action
		$this->testAdd($eventNotificationTemplate, $reference);
	}
	
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
	
	/* (non-PHPdoc)
	 * @see EventNotificationTemplateServiceTestBase::validateAdd()
	 */
	protected function validateAdd(KalturaEventNotificationTemplate $resultObject)
	{
		// TODO - add your own validations here
	}

	/**
	 * Tests eventNotificationTemplate->cloneAction action
	 * @param int $id source template to clone
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate overwrite configuration object
	 * @param KalturaEventNotificationTemplate $reference
	 * @dataProvider provideData
	 */
	public function testCloneAction($id, KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationTemplate $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->cloneAction($id, $eventNotificationTemplate, $reference);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplate', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplate', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		// TODO - add here your own validations
		$this->validateCloneAction($resultObject);
	}

	/* (non-PHPdoc)
	 * @see EventNotificationTemplateServiceTestBase::validateGet()
	 */
	protected function validateGet(KalturaEventNotificationTemplate $resultObject)
	{
		// TODO - add your own validations here
	}

	/* (non-PHPdoc)
	 * @see EventNotificationTemplateServiceTestBase::validateUpdate()
	 */
	protected function validateUpdate(KalturaEventNotificationTemplate $resultObject)
	{
		// TODO - add your own validations here
	}

	/**
	 * Tests eventNotificationTemplate->updatestatus action
	 * @param int $id 
	 * @param KalturaEventNotificationTemplateStatus $status 
	 * @param KalturaEventNotificationTemplate $reference
	 * @dataProvider provideData
	 */
	public function testUpdatestatus($id, $status, KalturaEventNotificationTemplate $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->updatestatus($id, $status, $reference);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplate', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplate', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		// TODO - add here your own validations
		$this->validateUpdatestatus($resultObject);
	}

	/* (non-PHPdoc)
	 * @see EventNotificationTemplateServiceTestBase::validateDelete()
	 */
	protected function validateDelete(KalturaEventNotificationTemplate $resultObject)
	{
		// TODO - add your own validations here
	}

	/* (non-PHPdoc)
	 * @see EventNotificationTemplateServiceTestBase::validateListAction()
	 */
	protected function validateListAction(KalturaEventNotificationTemplateListResponse $resultObject)
	{
		// TODO - add your own validations here
	}

	/**
	 * Tests eventNotificationTemplate->listbypartner action
	 * @param KalturaPartnerFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaEventNotificationTemplateListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListbypartner(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null, KalturaEventNotificationTemplateListResponse $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->listbypartner($filter, $pager, $reference);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaEventNotificationTemplateListResponse', $resultObject);
		else
			$this->assertType('KalturaEventNotificationTemplateListResponse', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		// TODO - add here your own validations
		$this->validateListbypartner($resultObject);
	}

	/**
	 * Tests eventNotificationTemplate->dispatch action
	 * @param int $id 
	 * @param KalturaEventNotificationDispatchJobData $data
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testDispatch($id, KalturaEventNotificationDispatchJobData $data, $reference)
	{
		$resultObject = $this->client->eventNotificationTemplate->dispatch($id, $data, $reference);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('int', $resultObject);
		else
			$this->assertType('int', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		// TODO - add here your own validations
		$this->validateDispatch($resultObject);
	}

}

