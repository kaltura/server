<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/BatchServiceBaseTest.php');

/**
 * batch service test case.
 */
class BatchServiceTest extends BatchServiceBaseTest
{
	/**
	 * Tests batch->getExclusiveImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveImportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveBulkUploadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->batch->addBulkUploadResult($bulkUploadResult, $pluginDataArray, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->batch->getBulkUploadLastResult($bulkUploadJobId, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId, $reference)
	{
		$resultObject = $this->client->batch->updateBulkUploadResults($bulkUploadJobId, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaConvertCollectionFlavorDataArray $flavorsData
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaConvertCollectionFlavorDataArray $flavorsData = null, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveConvertProfileJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveConvertJobSubType($id, $lockKey, $subType, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusivePostConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveCaptureThumbJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveExtractMediaJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @param KalturaMediaInfo $reference
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo, KalturaMediaInfo $reference)
	{
		$resultObject = $this->client->batch->addMediaInfo($mediaInfo, $reference);
		$this->assertType('KalturaMediaInfo', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveStorageExportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveStorageDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchGetExclusiveNotificationJobsResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchGetExclusiveNotificationJobsResponse $reference)
	{
		$resultObject = $this->client->batch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveNotificationJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveMailJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveBulkDownloadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveProvisionProvideJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->resetJobExecutionAttempts action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testResetJobExecutionAttempts(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $id)
	{
		$resultObject = $this->client->batch->resetJobExecutionAttempts($id, $lockKey, $jobType);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->freeExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter, $reference)
	{
		$resultObject = $this->client->batch->getQueueSize($workerQueueFilter, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->getExclusiveAlmostDone action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDone(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->cleanExclusiveJobs action
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs($reference)
	{
		$resultObject = $this->client->batch->cleanExclusiveJobs($reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->logConversion action
	 * @param string $flavorAssetId
	 * @param string $data
	 * @dataProvider provideData
	 */
	public function testLogConversion($flavorAssetId, $data)
	{
		$resultObject = $this->client->batch->logConversion($flavorAssetId, $data);
		// TODO - add here your own validations
	}

	/**
	 * Tests batch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @param KalturaFileExistsResponse $reference
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size, KalturaFileExistsResponse $reference)
	{
		$resultObject = $this->client->batch->checkFileExists($localPath, $size, $reference);
		$this->assertType('KalturaFileExistsResponse', $resultObject);
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
