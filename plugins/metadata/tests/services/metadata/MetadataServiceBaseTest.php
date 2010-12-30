<?php

/**
 * metadata service base test case.
 */
abstract class MetadataServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests metadata->list action
	 * @param KalturaMetadataFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->metadata->listAction($filter, $pager);
		$this->assertType('KalturaMetadataListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

	/**
	 * Tests metadata->add action
	 * @param int $metadataProfileId
	 * @param KalturaMetadataObjectType $objectType
	 * @param string $objectId
	 * @param string $xmlData
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd($metadataProfileId, KalturaMetadataObjectType $objectType, $objectId, $xmlData)
	{
		$resultObject = $this->client->metadata->add($metadataProfileId, $objectType, $objectId, $xmlData);
		$this->assertType('KalturaMetadata', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

	/**
	 * Tests metadata->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->metadata->delete($id);
	}

	/**
	 * Tests metadata->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->metadata->get($id);
		$this->assertType('KalturaMetadata', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests metadata->update action
	 * @param string $xmlData
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($xmlData = null, $id)
	{
		$resultObject = $this->client->metadata->update($id, $xmlData);
		$this->assertType('KalturaMetadata', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

}
