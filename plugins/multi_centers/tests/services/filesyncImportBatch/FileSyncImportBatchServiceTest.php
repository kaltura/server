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
	 * @dataProvider provideData
	 */
	public function testGetExclusiveFileSyncImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveFileSyncImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveFileSyncImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveFileSyncImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveFileSyncImportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveFileSyncImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveFileSyncImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveFileSyncImportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneFileSyncImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneFileSyncImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneFileSyncImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveImportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveBulkUploadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null)
	{
		$resultObject = $this->client->filesyncImportBatch->addBulkUploadResult($bulkUploadResult, $pluginDataArray);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId)
	{
		$resultObject = $this->client->filesyncImportBatch->getBulkUploadLastResult($bulkUploadJobId);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId)
	{
		$resultObject = $this->client->filesyncImportBatch->updateBulkUploadResults($bulkUploadJobId);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaConvertCollectionFlavorDataArray $flavorsData
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaConvertCollectionFlavorDataArray $flavorsData = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveConvertProfileJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveConvertJobSubType($id, $lockKey, $subType);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusivePostConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveCaptureThumbJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveExtractMediaJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo)
	{
		$resultObject = $this->client->filesyncImportBatch->addMediaInfo($mediaInfo);
		$this->assertType('KalturaMediaInfo', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveStorageExportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveStorageDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveNotificationJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveMailJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveBulkDownloadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveProvisionProvideJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
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
	}

	/**
	 * Tests filesyncImportBatch->freeExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter)
	{
		$resultObject = $this->client->filesyncImportBatch->getQueueSize($workerQueueFilter);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->getExclusiveAlmostDone action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDone(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->filesyncImportBatch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->filesyncImportBatch->updateExclusiveJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests filesyncImportBatch->cleanExclusiveJobs action
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs()
	{
		$resultObject = $this->client->filesyncImportBatch->cleanExclusiveJobs();
		$this->assertType('int', $resultObject);
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
	}

	/**
	 * Tests filesyncImportBatch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size)
	{
		$resultObject = $this->client->filesyncImportBatch->checkFileExists($localPath, $size);
		$this->assertType('KalturaFileExistsResponse', $resultObject);
	}

}
