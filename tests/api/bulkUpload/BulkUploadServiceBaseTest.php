<?php

/**
 * bulkUpload service base test case.
 */
abstract class BulkUploadServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests bulkUpload->add action
	 * @param int $conversionProfileId
	 * @param file $csvFileData
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd($conversionProfileId, file $csvFileData)
	{
		$resultObject = $this->client->bulkUpload->add($conversionProfileId, $csvFileData);
		$this->assertType('KalturaBulkUpload', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests bulkUpload->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->bulkUpload->get($id);
		$this->assertType('KalturaBulkUpload', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests bulkUpload->list action
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->bulkUpload->listAction($pager);
		$this->assertType('KalturaBulkUploadListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
