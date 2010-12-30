<?php

/**
 * data service base test case.
 */
abstract class DataServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests data->add action
	 * @param KalturaDataEntry $dataEntry
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaDataEntry $dataEntry)
	{
		$resultObject = $this->client->data->add($dataEntry);
		$this->assertType('KalturaDataEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests data->get action
	 * @param string $entryId
	 * @param int $version
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($entryId, $version = -1)
	{
		$resultObject = $this->client->data->get($entryId, $version);
		$this->assertType('KalturaDataEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests data->update action
	 * @param string $entryId
	 * @param KalturaDataEntry $documentEntry
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaDataEntry $documentEntry)
	{
		$resultObject = $this->client->data->update($entryId, $documentEntry);
		$this->assertType('KalturaDataEntry', $resultObject);
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
	 * Tests data->delete action
	 * @param string $entryId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->data->delete($entryId);
	}

	/**
	 * Tests data->list action
	 * @param KalturaDataEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaDataEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->data->listAction($filter, $pager);
		$this->assertType('KalturaDataListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
