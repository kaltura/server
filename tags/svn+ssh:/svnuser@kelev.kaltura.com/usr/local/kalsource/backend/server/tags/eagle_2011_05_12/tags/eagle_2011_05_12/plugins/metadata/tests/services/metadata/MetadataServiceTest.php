<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/MetadataServiceBaseTest.php');

/**
 * metadata service test case.
 */
class MetadataServiceTest extends MetadataServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($metadataProfileId, $objectType, $objectId, $xmlData, KalturaMetadata $reference)
	{
		parent::validateAdd($metadataProfileId, $objectType, $objectId, $xmlData, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests metadata->addFromFile action
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param file $xmlFile
	 * @param KalturaMetadata $reference
	 * @dataProvider provideData
	 */
	public function testAddFromFile($metadataProfileId, $objectType, $objectId, file $xmlFile, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->addFromFile($metadataProfileId, $objectType, $objectId, $xmlFile, $reference);
		$this->assertType('KalturaMetadata', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadata->addFromUrl action
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param string $url
	 * @param KalturaMetadata $reference
	 * @dataProvider provideData
	 */
	public function testAddFromUrl($metadataProfileId, $objectType, $objectId, $url, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->addFromUrl($metadataProfileId, $objectType, $objectId, $url, $reference);
		$this->assertType('KalturaMetadata', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadata->addFromBulk action
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param string $url
	 * @param KalturaMetadata $reference
	 * @dataProvider provideData
	 */
	public function testAddFromBulk($metadataProfileId, $objectType, $objectId, $url, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->addFromBulk($metadataProfileId, $objectType, $objectId, $url, $reference);
		$this->assertType('KalturaMetadata', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete();
		// TODO - add your own validations here
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
		// TODO - add here your own validations
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaMetadata $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($xmlData = null, KalturaMetadata $reference, $id)
	{
		parent::validateUpdate($xmlData, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests metadata->updateFromFile action
	 * @param file $xmlFile
	 * @param KalturaMetadata $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateFromFile(file $xmlFile = null, KalturaMetadata $reference, $id)
	{
		$resultObject = $this->client->metadata->updateFromFile($id, $xmlFile, $reference);
		$this->assertType('KalturaMetadata', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
