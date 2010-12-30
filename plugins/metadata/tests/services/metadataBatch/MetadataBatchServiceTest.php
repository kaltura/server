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
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportMetadataJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveImportMetadataJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveImportMetadataJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportMetadataJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveImportMetadataJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveImportMetadataJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportMetadataJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveImportMetadataJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveTransformMetadataJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveTransformMetadataJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveTransformMetadataJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveTransformMetadataJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveTransformMetadataJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveTransformMetadataJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveTransformMetadataJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveTransformMetadataJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveTransformMetadataJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getTransformMetadataObjects action
	 * @param int $metadataProfileId
	 * @param int $srcVersion
	 * @param int $destVersion
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testGetTransformMetadataObjects($metadataProfileId, $srcVersion, $destVersion, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->metadataBatch->getTransformMetadataObjects($metadataProfileId, $srcVersion, $destVersion, $pager);
		$this->assertType('KalturaTransformMetadataResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->upgradeMetadataObjects action
	 * @param int $metadataProfileId
	 * @param int $srcVersion
	 * @param int $destVersion
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testUpgradeMetadataObjects($metadataProfileId, $srcVersion, $destVersion, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->metadataBatch->upgradeMetadataObjects($metadataProfileId, $srcVersion, $destVersion, $pager);
		$this->assertType('KalturaUpgradeMetadataResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveImportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveImportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveImportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveImportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveImportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveImportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveImportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneBulkUploadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneBulkUploadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveBulkUploadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveBulkUploadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkUploadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveBulkUploadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->addBulkUploadResult action
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 * @param KalturaBulkUploadPluginDataArray $pluginDataArray
	 * @dataProvider provideData
	 */
	public function testAddBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, KalturaBulkUploadPluginDataArray $pluginDataArray = null)
	{
		$resultObject = $this->client->metadataBatch->addBulkUploadResult($bulkUploadResult, $pluginDataArray);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests metadataBatch->getBulkUploadLastResult action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadLastResult($bulkUploadJobId)
	{
		$resultObject = $this->client->metadataBatch->getBulkUploadLastResult($bulkUploadJobId);
		$this->assertType('KalturaBulkUploadResult', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateBulkUploadResults action
	 * @param int $bulkUploadJobId
	 * @dataProvider provideData
	 */
	public function testUpdateBulkUploadResults($bulkUploadJobId)
	{
		$resultObject = $this->client->metadataBatch->updateBulkUploadResults($bulkUploadJobId);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneConvertProfileJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneConvertProfileJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param KalturaConvertCollectionFlavorDataArray $flavorsData
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, KalturaConvertCollectionFlavorDataArray $flavorsData = null, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveConvertCollectionJob($id, $lockKey, $job, $flavorsData);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveConvertProfileJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveConvertCollectionJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertCollectionJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveConvertCollectionJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveConvertProfileJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertProfileJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveConvertProfileJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveConvertCollectionJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveConvertCollectionJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveConvertJobSubType action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $subType
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveConvertJobSubType(KalturaExclusiveLockKey $lockKey, $subType, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveConvertJobSubType($id, $lockKey, $subType);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusivePostConvertJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusivePostConvertJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusivePostConvertJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusivePostConvertJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusivePostConvertJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusivePostConvertJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveCaptureThumbJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveCaptureThumbJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveCaptureThumbJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveCaptureThumbJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveCaptureThumbJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveCaptureThumbJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveExtractMediaJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveExtractMediaJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveExtractMediaJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->addMediaInfo action
	 * @param KalturaMediaInfo $mediaInfo
	 * @dataProvider provideData
	 */
	public function testAddMediaInfo(KalturaMediaInfo $mediaInfo)
	{
		$resultObject = $this->client->metadataBatch->addMediaInfo($mediaInfo);
		$this->assertType('KalturaMediaInfo', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveExtractMediaJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveExtractMediaJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveExtractMediaJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveStorageExportJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveStorageExportJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveStorageExportJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveStorageExportJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageExportJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveStorageExportJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveStorageDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveStorageDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveStorageDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveStorageDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveStorageDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveStorageDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveNotificationJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveNotificationJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchGetExclusiveNotificationJobsResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveNotificationJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveNotificationJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveNotificationJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveNotificationJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveMailJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveMailJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveMailJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveMailJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveMailJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveMailJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveMailJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneBulkDownloadJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneBulkDownloadJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveBulkDownloadJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveBulkDownloadJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveBulkDownloadJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveBulkDownloadJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneProvisionProvideJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneProvisionProvideJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveProvisionProvideJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveProvisionProvideJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionProvideJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveProvisionProvideJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDoneProvisionDeleteJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDoneProvisionDeleteJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveProvisionDeleteJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->freeExclusiveProvisionDeleteJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveProvisionDeleteJob(KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveProvisionDeleteJob($id, $lockKey, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
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
	}

	/**
	 * Tests metadataBatch->freeExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJobType $jobType
	 * @param bool $resetExecutionAttempts
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testFreeExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJobType $jobType, $resetExecutionAttempts = null, $id)
	{
		$resultObject = $this->client->metadataBatch->freeExclusiveJob($id, $lockKey, $jobType, $resetExecutionAttempts);
		$this->assertType('KalturaFreeJobResponse', $resultObject);
	}

	/**
	 * Tests metadataBatch->getQueueSize action
	 * @param KalturaWorkerQueueFilter $workerQueueFilter
	 * @dataProvider provideData
	 */
	public function testGetQueueSize(KalturaWorkerQueueFilter $workerQueueFilter)
	{
		$resultObject = $this->client->metadataBatch->getQueueSize($workerQueueFilter);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveJobs action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->getExclusiveAlmostDone action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param int $maxExecutionTime
	 * @param int $numberOfJobs
	 * @param KalturaBatchJobFilter $filter
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetExclusiveAlmostDone(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, KalturaBatchJobType $jobType = null)
	{
		$resultObject = $this->client->metadataBatch->getExclusiveAlmostDone($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		$this->assertType('KalturaBatchJobArray', $resultObject);
	}

	/**
	 * Tests metadataBatch->updateExclusiveJob action
	 * @param KalturaExclusiveLockKey $lockKey
	 * @param KalturaBatchJob $job
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateExclusiveJob(KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, $id)
	{
		$resultObject = $this->client->metadataBatch->updateExclusiveJob($id, $lockKey, $job);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests metadataBatch->cleanExclusiveJobs action
	 * @dataProvider provideData
	 */
	public function testCleanExclusiveJobs()
	{
		$resultObject = $this->client->metadataBatch->cleanExclusiveJobs();
		$this->assertType('int', $resultObject);
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
	}

	/**
	 * Tests metadataBatch->checkFileExists action
	 * @param string $localPath
	 * @param int $size
	 * @dataProvider provideData
	 */
	public function testCheckFileExists($localPath, $size)
	{
		$resultObject = $this->client->metadataBatch->checkFileExists($localPath, $size);
		$this->assertType('KalturaFileExistsResponse', $resultObject);
	}

}
