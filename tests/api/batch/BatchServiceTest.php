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
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveImportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveBulkUploadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null)
	{
		$resultObject = $this->client->batch->addBulkUploadResult($bulkUploadResult, $pluginDataArray);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests batch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId)
	{
		$resultObject = $this->client->batch->getBulkUploadLastResult($bulkUploadJobId);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests batch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId)
	{
		$resultObject = $this->client->batch->updateBulkUploadResults($bulkUploadJobId);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaConvertCollectionFlavorDataArray $flavorsData
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaConvertCollectionFlavorDataArray $flavorsData = null, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveConvertProfileJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveConvertJobSubType($id, $lockKey, $subType);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusivePostConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveCaptureThumbJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveExtractMediaJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo)
	{
		$resultObject = $this->client->batch->addMediaInfo($mediaInfo);
		$this->assertType('KalturaMediaInfo', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveStorageExportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveStorageDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveNotificationJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveMailJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveBulkDownloadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveProvisionProvideJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
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
	}

	/**
	 * Tests batch->freeExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->batch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests batch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter)
	{
		$resultObject = $this->client->batch->getQueueSize($workerQueueFilter);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->batch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->getExclusiveAlmostDone action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDone(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->batch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests batch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->batch->updateExclusiveJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests batch->cleanExclusiveJobs action
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs()
	{
		$resultObject = $this->client->batch->cleanExclusiveJobs();
		$this->assertType('int', $resultObject);
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
	}

	/**
	 * Tests batch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size)
	{
		$resultObject = $this->client->batch->checkFileExists($localPath, $size);
		$this->assertType('KalturaFileExistsResponse', $resultObject);
	}

}
