<?php

/**
 * externalMedia service base test case.
 */
abstract class ExternalMediaServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Tests externalMedia->add action
	 * @param KalturaExternalMediaEntry $entry 
	 * @param KalturaExternalMediaEntry $reference
	 * @return KalturaExternalMediaEntry
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaExternalMediaEntry $entry, KalturaExternalMediaEntry $reference)
	{
		$resultObject = $this->client->externalMedia->add($entry);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaExternalMediaEntry', $resultObject);
		else
			$this->assertType('KalturaExternalMediaEntry', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($resultObject);
		
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaExternalMediaEntry $resultObject
	 */
	protected function validateAdd(KalturaExternalMediaEntry $resultObject){}

	/**
	 * Tests externalMedia->get action
	 * @param string $id External media entry id
	 * @param KalturaExternalMediaEntry $reference
	 * @return KalturaExternalMediaEntry
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaExternalMediaEntry $reference)
	{
		$resultObject = $this->client->externalMedia->get($id);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaExternalMediaEntry', $resultObject);
		else
			$this->assertType('KalturaExternalMediaEntry', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateGet($resultObject);
		
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaExternalMediaEntry $resultObject
	 */
	protected function validateGet(KalturaExternalMediaEntry $resultObject){}

	/**
	 * Tests externalMedia->update action
	 * @param string $id External media entry id to update
	 * @param KalturaExternalMediaEntry $entry External media entry object to update
	 * @param KalturaExternalMediaEntry $reference
	 * @return KalturaExternalMediaEntry
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaExternalMediaEntry $entry, KalturaExternalMediaEntry $reference)
	{
		$resultObject = $this->client->externalMedia->update($id, $entry);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaExternalMediaEntry', $resultObject);
		else
			$this->assertType('KalturaExternalMediaEntry', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($resultObject);
		
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaExternalMediaEntry $resultObject
	 */
	protected function validateUpdate(KalturaExternalMediaEntry $resultObject){}

	/**
	 * Tests externalMedia->delete action
	 * @param string $id External media entry id to delete
	 * @depends testGet with data set #0
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->externalMedia->delete($id);
	}

	/**
	 * Tests externalMedia->listAction action
	 * @param KalturaExternalMediaEntryFilter $filter External media entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaExternalMediaEntryListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaExternalMediaEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaExternalMediaEntryListResponse $reference)
	{
		$resultObject = $this->client->externalMedia->listAction($filter, $pager);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaExternalMediaEntryListResponse', $resultObject);
		else
			$this->assertType('KalturaExternalMediaEntryListResponse', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($resultObject);
	}

	/**
	 * Validates testListAction results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaExternalMediaEntryListResponse $resultObject
	 */
	protected function validateListAction(KalturaExternalMediaEntryListResponse $resultObject){}

}
