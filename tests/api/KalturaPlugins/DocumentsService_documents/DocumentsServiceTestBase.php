<?php

/**
 * documents service base test case.
 */
abstract class DocumentsServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Tests documents->get action
	 * @param string $entryId Document entry id
	 * @param int $version Desired version of the data
	 * @param KalturaDocumentEntry $reference
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->documents->get($entryId, $version);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaDocumentEntry', $resultObject);
		else
			$this->assertNotType('KalturaDocumentEntry', get_class($resultObject));
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($resultObject);
	}

	/**
	 * Validates testGet results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaDocumentEntry $resultObject
	 */
	protected function validateGet(KalturaDocumentEntry $resultObject){}

	/**
	 * Tests documents->update action
	 * @param string $entryId Document entry id to update
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata to update
	 * @param KalturaDocumentEntry $reference
	 * @depends testAdd with data set #1
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaDocumentEntry $documentEntry, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->documents->update($entryId, $documentEntry);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaDocumentEntry', $resultObject);
		else
			$this->assertNotType('KalturaDocumentEntry', get_class($resultObject));
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateUpdate($resultObject);
	}

	/**
	 * Validates testUpdate results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaDocumentEntry $resultObject
	 */
	protected function validateUpdate(KalturaDocumentEntry $resultObject){}

	/**
	 * Tests documents->delete action
	 * @param string $entryId Document entry id to delete
	 * @depends testAdd with data set #2
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->documents->delete($entryId);
	}

	/**
	 * Tests documents->listAction action
	 * @param KalturaDocumentEntryFilter $filter Document entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaDocumentListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaDocumentListResponse $reference)
	{
		$resultObject = $this->client->documents->listAction($filter, $pager);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaDocumentListResponse', $resultObject);
		else
			$this->assertNotType('KalturaDocumentListResponse', get_class($resultObject));
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($resultObject);
	}

	/**
	 * Validates testListAction results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaDocumentListResponse $resultObject
	 */
	protected function validateListAction(KalturaDocumentListResponse $resultObject){}

}
