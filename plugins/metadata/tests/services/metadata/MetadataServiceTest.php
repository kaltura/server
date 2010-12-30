<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/MetadataServiceBaseTest.php');

/**
 * metadata service test case.
 */
class MetadataServiceTest extends MetadataServiceBaseTest
{
	/**
	 * Tests metadata->addFromFile action
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param file $xmlFile
	 * @dataProvider provideData
	 */
	public function testAddFromFile($metadataProfileId, KalturaMetadataObjectType $objectType, $objectId, file $xmlFile)
	{
		$resultObject = $this->client->metadata->addFromFile($metadataProfileId, $objectType, $objectId, $xmlFile);
		$this->assertType('KalturaMetadata', $resultObject);
	}

	/**
	 * Tests metadata->addFromUrl action
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param string $url
	 * @dataProvider provideData
	 */
	public function testAddFromUrl($metadataProfileId, KalturaMetadataObjectType $objectType, $objectId, $url)
	{
		$resultObject = $this->client->metadata->addFromUrl($metadataProfileId, $objectType, $objectId, $url);
		$this->assertType('KalturaMetadata', $resultObject);
	}

	/**
	 * Tests metadata->addFromBulk action
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param string $url
	 * @dataProvider provideData
	 */
	public function testAddFromBulk($metadataProfileId, KalturaMetadataObjectType $objectType, $objectId, $url)
	{
		$resultObject = $this->client->metadata->addFromBulk($metadataProfileId, $objectType, $objectId, $url);
		$this->assertType('KalturaMetadata', $resultObject);
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * Tests metadata->invalidate action
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testInvalidate($id)
	{
		$resultObject = $this->client->metadata->invalidate($id);
	}

	/**
	 * Tests metadata->updateFromFile action
	 * @param file $xmlFile
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateFromFile(file $xmlFile = null, $id)
	{
		$resultObject = $this->client->metadata->updateFromFile($id, $xmlFile);
		$this->assertType('KalturaMetadata', $resultObject);
	}

}
