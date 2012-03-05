<?php

/**
 * metadata service base test case.
 */
abstract class MetadataServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Tests metadata->add action
	 * @param int $metadataProfileId 
	 * @param KalturaMetadataObjectType $objectType 
	 * @param string $objectId 
	 * @param string $xmlData XML metadata
	 * @param KalturaMetadata $reference
	 * @return KalturaMetadata
	 * @dataProvider provideData
	 */
	public function testAdd($metadataProfileId, $objectType, $objectId, $xmlData, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->add($metadataProfileId, $objectType, $objectId, $xmlData);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaMetadata', $resultObject);
		else
			$this->assertNotType('KalturaMetadata', get_class($resultObject));
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($metadataProfileId, $objectType, $objectId, $xmlData, $reference);
		
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	abstract protected function validateAdd($metadataProfileId, $objectType, $objectId, $xmlData, KalturaMetadata $reference);
	/**
	 * Tests metadata->get action
	 * @param int $id 
	 * @param KalturaMetadata $reference
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->get($id);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaMetadata', $resultObject);
		else
			$this->assertNotType('KalturaMetadata', get_class($resultObject));
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	abstract protected function validateGet($id, KalturaMetadata $reference);
	/**
	 * Tests metadata->update action
	 * @param int $id 
	 * @param string $xmlData XML metadata
	 * @param int $version Enable update only if the metadata object version did not change by other process
	 * @param KalturaMetadata $reference
	 * @depends testAdd with data set #1
	 * @dataProvider provideData
	 */
	public function testUpdate($id, $xmlData = "", $version = "", KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->update($id, $xmlData, $version);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaMetadata', $resultObject);
		else
			$this->assertNotType('KalturaMetadata', get_class($resultObject));
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateUpdate($id, $xmlData, $version, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	abstract protected function validateUpdate($id, $xmlData = "", $version = "", KalturaMetadata $reference);
	/**
	 * Tests metadata->listAction action
	 * @param KalturaMetadataFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaMetadataListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataListResponse $reference)
	{
		$resultObject = $this->client->metadata->listAction($filter, $pager);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaMetadataListResponse', $resultObject);
		else
			$this->assertNotType('KalturaMetadataListResponse', get_class($resultObject));
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	abstract protected function validateListAction(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataListResponse $reference);
	/**
	 * Tests metadata->delete action
	 * @param int $id 
	 * @depends testAdd with data set #2
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->metadata->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	abstract protected function validateDelete($id);
}
