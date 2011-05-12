<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/FileSyncImportBatchServiceBaseTest.php');

/**
 * filesyncImportBatch service test case.
 */
class FileSyncImportBatchServiceTest extends FileSyncImportBatchServiceBaseTest
{
	/**
	 * Tests filesyncImportBatch->getExclusiveFileSyncImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveFileSyncImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveFileSyncImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveFileSyncImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveFileSyncImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveFileSyncImportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveFileSyncImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveFileSyncImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveFileSyncImportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneFileSyncImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneFileSyncImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneFileSyncImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveImportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveBulkUploadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->addBulkUploadResult($bulkUploadResult, $pluginDataArray, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getBulkUploadLastResult($bulkUploadJobId, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId, $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->updateBulkUploadResults($bulkUploadJobId, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveConvertCollectionJob action
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
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveConvertProfileJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveConvertJobSubType($id, $lockKey, $subType, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusivePostConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveCaptureThumbJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveExtractMediaJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @param KalturaMediaInfo $reference
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo, KalturaMediaInfo $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->addMediaInfo($mediaInfo, $reference);
		$this->assertType('KalturaMediaInfo', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveStorageExportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveStorageDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchGetExclusiveNotificationJobsResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchGetExclusiveNotificationJobsResponse $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveNotificationJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveMailJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveBulkDownloadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveProvisionProvideJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->resetJobExecutionAttempts action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testResetJobExecutionAttempts(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->resetJobExecutionAttempts($id, $lockKey, $jobType);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveJob action
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
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter, $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->getQueueSize($workerQueueFilter, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveJobs action
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
		$resultObject = $this->client->filesyncImportBatch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDone action
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
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->cleanExclusiveJobs action
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs($reference)
	{
		$resultObject = $this->client->filesyncImportBatch->cleanExclusiveJobs($reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->logConversion action
	 * @param string $flavorAssetId
	 * @param string $data
	 * @dataProvider provideData
	 */
	public function testLogConversion($flavorAssetId, $data)
	{
		$resultObject = $this->client->filesyncImportBatch->logConversion($flavorAssetId, $data);
		// TODO - add here your own validations
	}

	/**
	 * Tests filesyncImportBatch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @param KalturaFileExistsResponse $reference
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size, KalturaFileExistsResponse $reference)
	{
		$resultObject = $this->client->filesyncImportBatch->checkFileExists($localPath, $size, $reference);
		$this->assertType('KalturaFileExistsResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testGet - TODO: replace testGet with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
