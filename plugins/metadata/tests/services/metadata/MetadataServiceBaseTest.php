<?php

/**
 * metadata service base test case.
 */
abstract class MetadataServiceBaseTest extends KalturaApiTestCase
{
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
		$this->assertType('KalturaMetadataListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataListResponse $reference)
	{
	}

	/**
	 * Tests metadata->add action
	 * @param int $metadataProfileId 
	 * @param KalturaMetadataObjectType $objectType 
	 * @param string $objectId 
	 * @param string $xmlData XML metadata
	 * @param KalturaMetadata $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd($metadataProfileId, KalturaMetadataObjectType $objectType, $objectId, $xmlData, KalturaMetadata $reference)
	{
		$resultObject = $this->client->metadata->add($metadataProfileId, $objectType, $objectId, $xmlData);
		$this->assertType('KalturaMetadata', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($metadataProfileId, $objectType, $objectId, $xmlData, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($metadataProfileId, KalturaMetadataObjectType $objectType, $objectId, $xmlData, KalturaMetadata $reference)
	{
	}

	/**
	 * Tests metadata->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->metadata->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests metadata->get action
	 * @param KalturaMetadata $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaMetadata $reference, $id)
	{
		$resultObject = $this->client->metadata->get($id);
		$this->assertType('KalturaMetadata', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaMetadata $reference, $id)
	{
	}

	/**
	 * Tests metadata->update action
	 * @param string $xmlData XML metadata
	 * @param KalturaMetadata $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($xmlData = null, KalturaMetadata $reference, $id)
	{
		$resultObject = $this->client->metadata->update($id, $xmlData);
		$this->assertType('KalturaMetadata', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($xmlData, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($xmlData = null, KalturaMetadata $reference, $id)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
