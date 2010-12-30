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
	 * @dataProvider provideData
	 */
	public function testGetExclusiveVirusScanJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveVirusScanJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveVirusScanJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveVirusScanJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveVirusScanJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveVirusScanJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveVirusScanJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveVirusScanJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveImportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveBulkUploadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null)
	{
		$resultObject = $this->client->virusScanBatch->addBulkUploadResult($bulkUploadResult, $pluginDataArray);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId)
	{
		$resultObject = $this->client->virusScanBatch->getBulkUploadLastResult($bulkUploadJobId);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId)
	{
		$resultObject = $this->client->virusScanBatch->updateBulkUploadResults($bulkUploadJobId);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaConvertCollectionFlavorDataArray $flavorsData
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaConvertCollectionFlavorDataArray $flavorsData = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveConvertProfileJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveConvertJobSubType($id, $lockKey, $subType);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusivePostConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveCaptureThumbJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveExtractMediaJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo)
	{
		$resultObject = $this->client->virusScanBatch->addMediaInfo($mediaInfo);
		$this->assertType('KalturaMediaInfo', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveStorageExportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveStorageDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveNotificationJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveMailJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveBulkDownloadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveProvisionProvideJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
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
	}

	/**
	 * Tests virusScanBatch->freeExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->virusScanBatch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter)
	{
		$resultObject = $this->client->virusScanBatch->getQueueSize($workerQueueFilter);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->getExclusiveAlmostDone action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDone(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->virusScanBatch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests virusScanBatch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->virusScanBatch->updateExclusiveJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests virusScanBatch->cleanExclusiveJobs action
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs()
	{
		$resultObject = $this->client->virusScanBatch->cleanExclusiveJobs();
		$this->assertType('int', $resultObject);
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
	}

	/**
	 * Tests virusScanBatch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size)
	{
		$resultObject = $this->client->virusScanBatch->checkFileExists($localPath, $size);
		$this->assertType('KalturaFileExistsResponse', $resultObject);
	}

}
