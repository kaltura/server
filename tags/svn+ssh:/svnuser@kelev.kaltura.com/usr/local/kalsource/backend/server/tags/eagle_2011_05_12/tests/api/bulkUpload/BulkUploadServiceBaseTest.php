<?php

/**
 * bulkUpload service base test case.
 */
abstract class BulkUploadServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests bulkUpload->add action
	 * @param int $conversionProfileId Convertion profile id to use for converting the current bulk (-1 to use partner's default)
	 * @param file $fileData bullk upload file
	 * @param KalturaBulkUploadType $bulkUploadType 
	 * @param KalturaBulkUpload $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd($conversionProfileId, $fileData, KalturaBulkUploadType $bulkUploadType = null, KalturaBulkUpload $reference)
	{
		$resultObject = $this->client->bulkUpload->add($conversionProfileId, $fileData, $bulkUploadType);
		$this->assertType('KalturaBulkUpload', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($conversionProfileId, $fileData, $bulkUploadType, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($conversionProfileId, $fileData, KalturaBulkUploadType $bulkUploadType = null, KalturaBulkUpload $reference)
	{
	}

	/**
	 * Tests bulkUpload->get action
	 * @param KalturaBulkUpload $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaBulkUpload $reference, $id)
	{
		$resultObject = $this->client->bulkUpload->get($id);
		$this->assertType('KalturaBulkUpload', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaBulkUpload $reference, $id)
	{
	}

	/**
	 * Tests bulkUpload->listAction action
	 * @param KalturaFilterPager $pager 
	 * @param KalturaBulkUploadListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaFilterPager $pager = null, KalturaBulkUploadListResponse $reference)
	{
		$resultObject = $this->client->bulkUpload->listAction($pager);
		$this->assertType('KalturaBulkUploadListResponse', $resultObject);
		$this->validateListAction($pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaFilterPager $pager = null, KalturaBulkUploadListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
