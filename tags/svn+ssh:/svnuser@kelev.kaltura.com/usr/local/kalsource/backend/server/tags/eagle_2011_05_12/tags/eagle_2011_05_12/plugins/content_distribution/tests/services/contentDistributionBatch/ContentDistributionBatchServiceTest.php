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
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveDistributionSubmitJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveDistributionSubmitJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveDistributionSubmitJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveDistributionSubmitJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveDistributionSubmitJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveDistributionSubmitJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveDistributionSubmitJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveDistributionSubmitJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneDistributionSubmitJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneDistributionSubmitJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneDistributionSubmitJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveDistributionUpdateJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveDistributionUpdateJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveDistributionUpdateJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveDistributionUpdateJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveDistributionUpdateJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveDistributionUpdateJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveDistributionUpdateJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveDistributionUpdateJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveDistributionUpdateJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneDistributionUpdateJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneDistributionUpdateJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneDistributionUpdateJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveDistributionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveDistributionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveDistributionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveDistributionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveDistributionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveDistributionDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveDistributionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveDistributionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveDistributionDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneDistributionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneDistributionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneDistributionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveDistributionFetchReportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveDistributionFetchReportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveDistributionFetchReportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveDistributionFetchReportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveDistributionFetchReportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveDistributionFetchReportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveDistributionFetchReportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveDistributionFetchReportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveDistributionFetchReportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneDistributionFetchReportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneDistributionFetchReportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneDistributionFetchReportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateSunStatus action
	 * @dataProvider provideData
	 */
	public function testUpdateSunStatus()
	{
		$resultObject = $this->client->contentDistributionBatch->updateSunStatus();
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->createRequiredJobs action
	 * @dataProvider provideData
	 */
	public function testCreateRequiredJobs()
	{
		$resultObject = $this->client->contentDistributionBatch->createRequiredJobs();
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveImportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveBulkUploadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->addBulkUploadResult($bulkUploadResult, $pluginDataArray, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getBulkUploadLastResult($bulkUploadJobId, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId, $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->updateBulkUploadResults($bulkUploadJobId, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveConvertCollectionJob action
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
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveConvertProfileJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveConvertJobSubType($id, $lockKey, $subType, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusivePostConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveCaptureThumbJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveExtractMediaJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @param KalturaMediaInfo $reference
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo, KalturaMediaInfo $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->addMediaInfo($mediaInfo, $reference);
		$this->assertType('KalturaMediaInfo', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveStorageExportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveStorageDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchGetExclusiveNotificationJobsResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchGetExclusiveNotificationJobsResponse $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveNotificationJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveMailJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveBulkDownloadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveProvisionProvideJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
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
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->freeExclusiveJob action
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
		$resultObject = $this->client->contentDistributionBatch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter, $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->getQueueSize($workerQueueFilter, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveJobs action
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
		$resultObject = $this->client->contentDistributionBatch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->getExclusiveAlmostDone action
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
		$resultObject = $this->client->contentDistributionBatch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->contentDistributionBatch->updateExclusiveJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->cleanExclusiveJobs action
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs($reference)
	{
		$resultObject = $this->client->contentDistributionBatch->cleanExclusiveJobs($reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
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
		// TODO - add here your own validations
	}

	/**
	 * Tests contentDistributionBatch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @param KalturaFileExistsResponse $reference
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size, KalturaFileExistsResponse $reference)
	{
		$resultObject = $this->client->contentDistributionBatch->checkFileExists($localPath, $size, $reference);
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
