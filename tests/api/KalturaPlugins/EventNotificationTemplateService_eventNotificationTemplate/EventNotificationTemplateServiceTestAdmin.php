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
	 * Tests eventNotificationTemplate->add action
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate 
	 * @param KalturaEventNotificationTemplate $reference
	 * @return KalturaEventNotificationTemplate
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationTemplate $reference)
	{
		
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

