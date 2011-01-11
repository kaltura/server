<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/MetadataBatchServiceBaseTest.php');

/**
 * metadataBatch service test case.
 */
class MetadataBatchServiceTest extends MetadataBatchServiceBaseTest
{
	/**
	 * Tests metadataBatch->getExclusiveImportMetadataJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportMetadataJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveImportMetadataJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveImportMetadataJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportMetadataJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveImportMetadataJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveImportMetadataJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportMetadataJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveImportMetadataJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveTransformMetadataJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveTransformMetadataJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveTransformMetadataJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveTransformMetadataJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveTransformMetadataJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveTransformMetadataJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveTransformMetadataJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveTransformMetadataJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveTransformMetadataJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getTransformMetadataObjects action
	 * @param int $metadataProfileId
	 * @param int $srcVersion
	 * @param int $destVersion
	 * @param KalturaFilterPager $pager
	 * @param KalturaTransformMetadataResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetTransformMetadataObjects($metadataProfileId, $srcVersion, $destVersion, KalturaFilterPager $pager = null, KalturaTransformMetadataResponse $reference)
	{
		$resultObject = $this->client->metadataBatch->getTransformMetadataObjects($metadataProfileId, $srcVersion, $destVersion, $pager, $reference);
		$this->assertType('KalturaTransformMetadataResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->upgradeMetadataObjects action
	 * @param int $metadataProfileId
	 * @param int $srcVersion
	 * @param int $destVersion
	 * @param KalturaFilterPager $pager
	 * @param KalturaUpgradeMetadataResponse $reference
	 * @dataProvider provideData
	 */
	public function testUpgradeMetadataObjects($metadataProfileId, $srcVersion, $destVersion, KalturaFilterPager $pager = null, KalturaUpgradeMetadataResponse $reference)
	{
		$resultObject = $this->client->metadataBatch->upgradeMetadataObjects($metadataProfileId, $srcVersion, $destVersion, $pager, $reference);
		$this->assertType('KalturaUpgradeMetadataResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveImportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveBulkUploadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->metadataBatch->addBulkUploadResult($bulkUploadResult, $pluginDataArray, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @param KalturaBulkUploadResult $reference
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId, KalturaBulkUploadResult $reference)
	{
		$resultObject = $this->client->metadataBatch->getBulkUploadLastResult($bulkUploadJobId, $reference);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId, $reference)
	{
		$resultObject = $this->client->metadataBatch->updateBulkUploadResults($bulkUploadJobId, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveConvertCollectionJob action
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
		$resultObject = $this->client->metadataBatch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveConvertProfileJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveConvertJobSubType($id, $lockKey, $subType, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusivePostConvertJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveCaptureThumbJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveExtractMediaJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @param KalturaMediaInfo $reference
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo, KalturaMediaInfo $reference)
	{
		$resultObject = $this->client->metadataBatch->addMediaInfo($mediaInfo, $reference);
		$this->assertType('KalturaMediaInfo', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveStorageExportJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveStorageDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchGetExclusiveNotificationJobsResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchGetExclusiveNotificationJobsResponse $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveNotificationJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveMailJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveBulkDownloadJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveProvisionProvideJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobArray $reference
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobArray $reference)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param KalturaFreeJobResponse $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, KalturaFreeJobResponse $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->resetJobExecutionAttempts action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testResetJobExecutionAttempts(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $id)
	{
		$resultObject = $this->client->metadataBatch->resetJobExecutionAttempts($id, $lockKey, $jobType);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->freeExclusiveJob action
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
		$resultObject = $this->client->metadataBatch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts, $reference);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter, $reference)
	{
		$resultObject = $this->client->metadataBatch->getQueueSize($workerQueueFilter, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveJobs action
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
		$resultObject = $this->client->metadataBatch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDone action
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
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType, $reference);
		$this->assertType('KalturaBatchJobArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaBatchJob $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaBatchJob $reference, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveJob($id, $lockKey, $job, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->cleanExclusiveJobs action
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs($reference)
	{
		$resultObject = $this->client->metadataBatch->cleanExclusiveJobs($reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->logConversion action
	 * @param string $flavorAssetId
	 * @param string $data
	 * @dataProvider provideData
	 */
	public function testLogConversion($flavorAssetId, $data)
	{
		$resultObject = $this->client->metadataBatch->logConversion($flavorAssetId, $data);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataBatch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @param KalturaFileExistsResponse $reference
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size, KalturaFileExistsResponse $reference)
	{
		$resultObject = $this->client->metadataBatch->checkFileExists($localPath, $size, $reference);
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
