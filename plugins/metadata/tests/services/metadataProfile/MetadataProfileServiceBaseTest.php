<?php

/**
 * metadataProfile service base test case.
 */
abstract class MetadataProfileServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests metadataProfile->list action
	 * @param KalturaMetadataProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaMetadataProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->metadataProfile->listAction($filter, $pager);
		$this->assertType('KalturaMetadataProfileListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

	/**
	 * Tests metadataProfile->add action
	 * @param KalturaMetadataProfile $metadataProfile
	 * @param string $xsdData
	 * @param string $viewsData
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaMetadataProfile $metadataProfile, $xsdData, $viewsData = null)
	{
		$resultObject = $this->client->metadataProfile->add($metadataProfile, $xsdData, $viewsData);
		$this->assertType('KalturaMetadataProfile', $resultObject);
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
	 * Tests metadataProfile->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->metadataProfile->delete($id);
	}

	/**
	 * Tests metadataProfile->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->metadataProfile->get($id);
		$this->assertType('KalturaMetadataProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests metadataProfile->update action
	 * @param KalturaMetadataProfile $metadataProfile
	 * @param string $xsdData
	 * @param string $viewsData
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaMetadataProfile $metadataProfile, $xsdData = null, $viewsData = null, $id)
	{
		$resultObject = $this->client->metadataProfile->update($id, $metadataProfile, $xsdData, $viewsData);
		$this->assertType('KalturaMetadataProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

}
