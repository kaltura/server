<?php

/**
 * documents service base test case.
 */
abstract class DocumentsServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Tests documents->addFromEntry action
	 * @param string $sourceEntryId Document entry id to copy from
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata
	 * @param int $sourceFlavorParamsId The flavor to be used as the new entry source, source flavor will be used if not specified
	 * @param KalturaDocumentEntry $reference
	 * @return KalturaDocumentEntry
	 * @dataProvider provideData
	 */
	public function testAddFromEntry($sourceEntryId, KalturaDocumentEntry $documentEntry = null, $sourceFlavorParamsId = "", KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->documents->addFromEntry($sourceEntryId, $documentEntry, $sourceFlavorParamsId);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaDocumentEntry', $resultObject);
		else
			$this->assertNotType('KalturaDocumentEntry', get_class($resultObject));
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateAddFromEntry($sourceEntryId, $documentEntry, $sourceFlavorParamsId, $reference);
		
		return $resultObject->id;
	}

	/**
	 * Validates testAddFromEntry results
	 */
	abstract protected function validateAddFromEntry($sourceEntryId, KalturaDocumentEntry $documentEntry = null, $sourceFlavorParamsId = "", KalturaDocumentEntry $reference);
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
		$this->validateGet($entryId, $version, $reference);
	}

	/**
	 * Validates testGet results
	 */
	abstract protected function validateGet($entryId, $version = -1, KalturaDocumentEntry $reference);
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
		$this->validateUpdate($entryId, $documentEntry, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	abstract protected function validateUpdate($entryId, KalturaDocumentEntry $documentEntry, KalturaDocumentEntry $reference);
	/**
	 * Tests documents->delete action
	 * @param string $entryId Document entry id to delete
	 * @depends testAdd with data set #2
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->documents->delete($entryId);
		$this->validateDelete($entryId);
	}

	/**
	 * Validates testDelete results
	 */
	abstract protected function validateDelete($entryId);
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
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	abstract protected function validateListAction(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaDocumentListResponse $reference);
}
