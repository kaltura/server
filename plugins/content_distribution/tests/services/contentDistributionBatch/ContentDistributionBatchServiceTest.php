<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/ContentDistributionBatchServiceBaseTest.php');

/**
 * contentDistributionBatch service test case.
 */
class ContentDistributionBatchServiceTest extends ContentDistributionBatchServiceBaseTest
{
	/**
	 * Tests contentDistributionBatch->getExclusiveDistributionSubmitJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveDistributionSubmitJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveDistributionSubmitJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveDistributionSubmitJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveDistributionSubmitJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveDistributionSubmitJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveDistributionSubmitJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveDistributionSubmitJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveDistributionSubmitJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneDistributionSubmitJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneDistributionSubmitJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneDistributionSubmitJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveDistributionUpdateJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveDistributionUpdateJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveDistributionUpdateJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveDistributionUpdateJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveDistributionUpdateJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveDistributionUpdateJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveDistributionUpdateJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveDistributionUpdateJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveDistributionUpdateJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneDistributionUpdateJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneDistributionUpdateJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneDistributionUpdateJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveDistributionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveDistributionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveDistributionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveDistributionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveDistributionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveDistributionDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveDistributionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveDistributionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveDistributionDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneDistributionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneDistributionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneDistributionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveDistributionFetchReportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveDistributionFetchReportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveDistributionFetchReportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveDistributionFetchReportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveDistributionFetchReportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveDistributionFetchReportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveDistributionFetchReportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveDistributionFetchReportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveDistributionFetchReportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneDistributionFetchReportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneDistributionFetchReportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneDistributionFetchReportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->createRequiredJobs action
	 * @dataProvider provideData
	 */
	public function testCreateRequiredJobs()
	{
		$resultObject = $this->client->contentDistributionBatch->createRequiredJobs();
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveImportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveBulkUploadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null)
	{
		$resultObject = $this->client->contentDistributionBatch->addBulkUploadResult($bulkUploadResult, $pluginDataArray);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId)
	{
		$resultObject = $this->client->contentDistributionBatch->getBulkUploadLastResult($bulkUploadJobId);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId)
	{
		$resultObject = $this->client->contentDistributionBatch->updateBulkUploadResults($bulkUploadJobId);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaConvertCollectionFlavorDataArray $flavorsData
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaConvertCollectionFlavorDataArray $flavorsData = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveConvertProfileJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveConvertJobSubType($id, $lockKey, $subType);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusivePostConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveCaptureThumbJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveExtractMediaJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo)
	{
		$resultObject = $this->client->contentDistributionBatch->addMediaInfo($mediaInfo);
		$this->assertType('KalturaMediaInfo', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveStorageExportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveStorageDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveNotificationJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveMailJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveBulkDownloadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveProvisionProvideJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->resetJobExecutionAttempts action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testResetJobExecutionAttempts(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->resetJobExecutionAttempts($id, $lockKey, $jobType);
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter)
	{
		$resultObject = $this->client->contentDistributionBatch->getQueueSize($workerQueueFilter);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDone action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDone(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->cleanExclusiveJobs action
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs()
	{
		$resultObject = $this->client->contentDistributionBatch->cleanExclusiveJobs();
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests contentDistributionBatch->logConversion action
	 * @param string $flavorAssetId
	 * @param string $data
	 * @dataProvider provideData
	 */
	public function testLogConversion($flavorAssetId, $data)
	{
		$resultObject = $this->client->contentDistributionBatch->logConversion($flavorAssetId, $data);
	}

	/**
	 * Tests contentDistributionBatch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size)
	{
		$resultObject = $this->client->contentDistributionBatch->checkFileExists($localPath, $size);
		$this->assertType('KalturaFileExistsResponse', $resultObject);
	}

}
