<?php

require_once(dirname(__FILE__) . '/../../../bootstrap.php');

/**
 * metadata service test case.
 */
class MetadataServiceTest extends MetadataServiceTestBase
{
	/* (non-PHPdoc)
	 * @see MetadataServiceTestBase::validateAdd()
	 */
	protected function validateAdd(KalturaMetadata $resultObject)
	{
		// TODO - add your own validations here
	}

	/**
	 * Tests metadata->addfromfile action
	 * @param int $metadataProfileId 
	 * @param KalturaMetadataObjectType $objectType 
	 * @param string $objectId 
	 * @param file $xmlFile XML metadata
	 * @param KalturaMetadata $reference
	 * @dataProvider provideData
	 */
	public function testAddfromfile($metadataProfileId, $objectType, $objectId, $xmlFile, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->addfromfile($metadataProfileId, $objectType, $objectId, $xmlFile, $reference);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaMetadata', $resultObject);
		else
			$this->assertType('KalturaMetadata', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		// TODO - add here your own validations
		$this->validateAddfromfile($resultObject);
	}

	/**
	 * Tests metadata->addfromurl action
	 * @param int $metadataProfileId 
	 * @param KalturaMetadataObjectType $objectType 
	 * @param string $objectId 
	 * @param string $url XML metadata remote url
	 * @param KalturaMetadata $reference
	 * @dataProvider provideData
	 */
	public function testAddfromurl($metadataProfileId, $objectType, $objectId, $url, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->addfromurl($metadataProfileId, $objectType, $objectId, $url, $reference);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaMetadata', $resultObject);
		else
			$this->assertType('KalturaMetadata', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		// TODO - add here your own validations
		$this->validateAddfromurl($resultObject);
	}

	/**
	 * Tests metadata->addfrombulk action
	 * @param int $metadataProfileId 
	 * @param KalturaMetadataObjectType $objectType 
	 * @param string $objectId 
	 * @param string $url XML metadata remote url
	 * @param KalturaMetadata $reference
	 * @dataProvider provideData
	 */
	public function testAddfrombulk($metadataProfileId, $objectType, $objectId, $url, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->addfrombulk($metadataProfileId, $objectType, $objectId, $url, $reference);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaMetadata', $resultObject);
		else
			$this->assertType('KalturaMetadata', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		// TODO - add here your own validations
		$this->validateAddfrombulk($resultObject);
	}

	/* (non-PHPdoc)
	 * @see MetadataServiceTestBase::validateGet()
	 */
	protected function validateGet(KalturaMetadata $resultObject)
	{
		// TODO - add your own validations here
	}

	/* (non-PHPdoc)
	 * @see MetadataServiceTestBase::validateUpdate()
	 */
	protected function validateUpdate(KalturaMetadata $resultObject)
	{
		// TODO - add your own validations here
	}

	/**
	 * Tests metadata->updatefromfile action
	 * @param int $id 
	 * @param file $xmlFile XML metadata
	 * @param KalturaMetadata $reference
	 * @dataProvider provideData
	 */
	public function testUpdatefromfile($id, $xmlFile = null, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->updatefromfile($id, $xmlFile, $reference);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaMetadata', $resultObject);
		else
			$this->assertType('KalturaMetadata', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		// TODO - add here your own validations
		$this->validateUpdatefromfile($resultObject);
	}

	/* (non-PHPdoc)
	 * @see MetadataServiceTestBase::validateListAction()
	 */
	protected function validateListAction(KalturaMetadataListResponse $resultObject)
	{
		// TODO - add your own validations here
	}

	/**
	 * Tests metadata->invalidate action
	 * @param int $id 
	 * @param int $version Enable update only if the metadata object version did not change by other process
	 * @dataProvider provideData
	 */
	public function testInvalidate($id, $version = "")
	{
		$resultObject = $this->client->metadata->invalidate($id, $version);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadata->updatefromxsl action
	 * @param file $xslFile 
	 * @param string $schemaFilePath
	 * @param string $metadataFilePath
	 * @param KalturaMetadata $reference
	 * @dataProvider provideData
	 */
	public function testUpdatefromxsl($xslFile, $schemaFilePath, $metadataFilePath)
	{
		//add new entry
		$entry = new KalturaBaseEntry();
		$entry->name = uniqid('metadata_unit_test');
		$entry = $this->client->baseEntry->add($entry);
		
		//Add new metadata profile
		$metadataProfileSchema = file_get_contents($schemaFilePath);	
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->name = uniqid('metadata_unittest');
		$metadataProfile->systemName = uniqid('metadata_unittest');
		$metadataProfile->xsd = $metadataProfileSchema;
		$metadataProfile = $this->client->metadataProfile->add ($metadataProfile);
		
		//add metadata to entry
		$metadataXml = file_get_contents($metadataFilePath);
		$metadata = $this->client->metadata->add ($metadataProfile->id, KalturaMetadataObjectType::ENTRY, $entry->id, $metadataXml);
		//transform metaddata with xsl
		$resultObject = $this->client->metadata->updatefromxsl($id, $xslFile);
		
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaMetadata', $resultObject);
		else
			$this->assertType('KalturaMetadata', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		
		// TODO - add here your own validations
	}

}

