<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/VirusScanBatchServiceBaseTest.php');

/**
 * virusScanBatch service test case.
 */
class VirusScanBatchServiceTest extends VirusScanBatchServiceBaseTest
{
	/**
	 * Tests virusScanBatch->getExclusiveVirusScanJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveVirusScanJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveVirusScanJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveVirusScanJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveVirusScanJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveVirusScanJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveVirusScanJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveVirusScanJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveVirusScanJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveImportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveBulkUploadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->virusScanBatch->addBulkUploadResult($bulkUploadResult, $pluginDataArray, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->virusScanBatch->getBulkUploadLastResult($bulkUploadJobId, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId, $reference)
	{
		$resultObject = $this->client->virusScanBatch->updateBulkUploadResults($bulkUploadJobId, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveConvertCollectionJob action
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
		$resultObject = $this->client->virusScanBatch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveConvertProfileJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveConvertJobSubType($id, $lockKey, $subType, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusivePostConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveCaptureThumbJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveExtractMediaJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @param KalturaMediaInfo $reference
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo, KalturaMediaInfo $reference)
	{
		$resultObject = $this->client->virusScanBatch->addMediaInfo($mediaInfo, $reference);
		$this->assertType('KalturaMediaInfo', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveStorageExportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveStorageDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchGetExclusiveNotificationJobsResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchGetExclusiveNotificationJobsResponse $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveNotificationJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveMailJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveBulkDownloadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveProvisionProvideJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->resetJobExecutionAttempts action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testResetJobExecutionAttempts(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $id)
	{
		$resultObject = $this->client->virusScanBatch->resetJobExecutionAttempts($id, $lockKey, $jobType);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->freeExclusiveJob action
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
		$resultObject = $this->client->virusScanBatch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter, $reference)
	{
		$resultObject = $this->client->virusScanBatch->getQueueSize($workerQueueFilter, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveJobs action
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
		$resultObject = $this->client->virusScanBatch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDone action
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
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->cleanExclusiveJobs action
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs($reference)
	{
		$resultObject = $this->client->virusScanBatch->cleanExclusiveJobs($reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->logConversion action
	 * @param string $flavorAssetId
	 * @param string $data
	 * @dataProvider provideData
	 */
	public function testLogConversion($flavorAssetId, $data)
	{
		$resultObject = $this->client->virusScanBatch->logConversion($flavorAssetId, $data);
		// TODO - add here your own validations
	}

	/**
	 * Tests virusScanBatch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @param KalturaFileExistsResponse $reference
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size, KalturaFileExistsResponse $reference)
	{
		$resultObject = $this->client->virusScanBatch->checkFileExists($localPath, $size, $reference);
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
