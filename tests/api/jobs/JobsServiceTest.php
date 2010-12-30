<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/JobsServiceBaseTest.php');

/**
 * jobs service test case.
 */
class JobsServiceTest extends JobsServiceBaseTest
{
	/**
	 * Tests jobs->getImportStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetImportStatus($jobId)
	{
		$resultObject = $this->client->jobs->getImportStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteImport action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteImport($jobId)
	{
		$resultObject = $this->client->jobs->deleteImport($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortImport action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortImport($jobId)
	{
		$resultObject = $this->client->jobs->abortImport($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryImport action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryImport($jobId)
	{
		$resultObject = $this->client->jobs->retryImport($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getProvisionProvideStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetProvisionProvideStatus($jobId)
	{
		$resultObject = $this->client->jobs->getProvisionProvideStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteProvisionProvide action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteProvisionProvide($jobId)
	{
		$resultObject = $this->client->jobs->deleteProvisionProvide($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortProvisionProvide action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortProvisionProvide($jobId)
	{
		$resultObject = $this->client->jobs->abortProvisionProvide($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryProvisionProvide action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryProvisionProvide($jobId)
	{
		$resultObject = $this->client->jobs->retryProvisionProvide($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getProvisionDeleteStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetProvisionDeleteStatus($jobId)
	{
		$resultObject = $this->client->jobs->getProvisionDeleteStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteProvisionDelete action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteProvisionDelete($jobId)
	{
		$resultObject = $this->client->jobs->deleteProvisionDelete($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortProvisionDelete action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortProvisionDelete($jobId)
	{
		$resultObject = $this->client->jobs->abortProvisionDelete($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryProvisionDelete action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryProvisionDelete($jobId)
	{
		$resultObject = $this->client->jobs->retryProvisionDelete($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getBulkUploadStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadStatus($jobId)
	{
		$resultObject = $this->client->jobs->getBulkUploadStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteBulkUpload action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteBulkUpload($jobId)
	{
		$resultObject = $this->client->jobs->deleteBulkUpload($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortBulkUpload action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortBulkUpload($jobId)
	{
		$resultObject = $this->client->jobs->abortBulkUpload($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryBulkUpload action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryBulkUpload($jobId)
	{
		$resultObject = $this->client->jobs->retryBulkUpload($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getConvertStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetConvertStatus($jobId)
	{
		$resultObject = $this->client->jobs->getConvertStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getConvertCollectionStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetConvertCollectionStatus($jobId)
	{
		$resultObject = $this->client->jobs->getConvertCollectionStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getConvertProfileStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetConvertProfileStatus($jobId)
	{
		$resultObject = $this->client->jobs->getConvertProfileStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->addConvertProfileJob action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testAddConvertProfileJob($entryId)
	{
		$resultObject = $this->client->jobs->addConvertProfileJob($entryId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getRemoteConvertStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetRemoteConvertStatus($jobId)
	{
		$resultObject = $this->client->jobs->getRemoteConvertStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteConvert action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteConvert($jobId)
	{
		$resultObject = $this->client->jobs->deleteConvert($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortConvert action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortConvert($jobId)
	{
		$resultObject = $this->client->jobs->abortConvert($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryConvert action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryConvert($jobId)
	{
		$resultObject = $this->client->jobs->retryConvert($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteRemoteConvert action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteRemoteConvert($jobId)
	{
		$resultObject = $this->client->jobs->deleteRemoteConvert($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortRemoteConvert action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortRemoteConvert($jobId)
	{
		$resultObject = $this->client->jobs->abortRemoteConvert($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryRemoteConvert action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryRemoteConvert($jobId)
	{
		$resultObject = $this->client->jobs->retryRemoteConvert($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteConvertCollection action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteConvertCollection($jobId)
	{
		$resultObject = $this->client->jobs->deleteConvertCollection($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteConvertProfile action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteConvertProfile($jobId)
	{
		$resultObject = $this->client->jobs->deleteConvertProfile($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortConvertCollection action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortConvertCollection($jobId)
	{
		$resultObject = $this->client->jobs->abortConvertCollection($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortConvertProfile action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortConvertProfile($jobId)
	{
		$resultObject = $this->client->jobs->abortConvertProfile($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryConvertCollection action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryConvertCollection($jobId)
	{
		$resultObject = $this->client->jobs->retryConvertCollection($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryConvertProfile action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryConvertProfile($jobId)
	{
		$resultObject = $this->client->jobs->retryConvertProfile($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getPostConvertStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetPostConvertStatus($jobId)
	{
		$resultObject = $this->client->jobs->getPostConvertStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deletePostConvert action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeletePostConvert($jobId)
	{
		$resultObject = $this->client->jobs->deletePostConvert($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortPostConvert action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortPostConvert($jobId)
	{
		$resultObject = $this->client->jobs->abortPostConvert($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryPostConvert action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryPostConvert($jobId)
	{
		$resultObject = $this->client->jobs->retryPostConvert($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getCaptureThumbStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetCaptureThumbStatus($jobId)
	{
		$resultObject = $this->client->jobs->getCaptureThumbStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteCaptureThumb action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteCaptureThumb($jobId)
	{
		$resultObject = $this->client->jobs->deleteCaptureThumb($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortCaptureThumb action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortCaptureThumb($jobId)
	{
		$resultObject = $this->client->jobs->abortCaptureThumb($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryCaptureThumb action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryCaptureThumb($jobId)
	{
		$resultObject = $this->client->jobs->retryCaptureThumb($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getPullStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetPullStatus($jobId)
	{
		$resultObject = $this->client->jobs->getPullStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deletePull action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeletePull($jobId)
	{
		$resultObject = $this->client->jobs->deletePull($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortPull action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortPull($jobId)
	{
		$resultObject = $this->client->jobs->abortPull($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryPull action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryPull($jobId)
	{
		$resultObject = $this->client->jobs->retryPull($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getExtractMediaStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetExtractMediaStatus($jobId)
	{
		$resultObject = $this->client->jobs->getExtractMediaStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteExtractMedia action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteExtractMedia($jobId)
	{
		$resultObject = $this->client->jobs->deleteExtractMedia($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortExtractMedia action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortExtractMedia($jobId)
	{
		$resultObject = $this->client->jobs->abortExtractMedia($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryExtractMedia action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryExtractMedia($jobId)
	{
		$resultObject = $this->client->jobs->retryExtractMedia($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getStorageExportStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetStorageExportStatus($jobId)
	{
		$resultObject = $this->client->jobs->getStorageExportStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteStorageExport action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteStorageExport($jobId)
	{
		$resultObject = $this->client->jobs->deleteStorageExport($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortStorageExport action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortStorageExport($jobId)
	{
		$resultObject = $this->client->jobs->abortStorageExport($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryStorageExport action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryStorageExport($jobId)
	{
		$resultObject = $this->client->jobs->retryStorageExport($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getStorageDeleteStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetStorageDeleteStatus($jobId)
	{
		$resultObject = $this->client->jobs->getStorageDeleteStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteStorageDelete action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteStorageDelete($jobId)
	{
		$resultObject = $this->client->jobs->deleteStorageDelete($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortStorageDelete action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortStorageDelete($jobId)
	{
		$resultObject = $this->client->jobs->abortStorageDelete($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryStorageDelete action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryStorageDelete($jobId)
	{
		$resultObject = $this->client->jobs->retryStorageDelete($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getNotificationStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetNotificationStatus($jobId)
	{
		$resultObject = $this->client->jobs->getNotificationStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteNotification action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteNotification($jobId)
	{
		$resultObject = $this->client->jobs->deleteNotification($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortNotification action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortNotification($jobId)
	{
		$resultObject = $this->client->jobs->abortNotification($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryNotification action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryNotification($jobId)
	{
		$resultObject = $this->client->jobs->retryNotification($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->getMailStatus action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testGetMailStatus($jobId)
	{
		$resultObject = $this->client->jobs->getMailStatus($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteMail action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testDeleteMail($jobId)
	{
		$resultObject = $this->client->jobs->deleteMail($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortMail action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testAbortMail($jobId)
	{
		$resultObject = $this->client->jobs->abortMail($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryMail action
	 * @param int $jobId
	 * @dataProvider provideData
	 */
	public function testRetryMail($jobId)
	{
		$resultObject = $this->client->jobs->retryMail($jobId);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->addMailJob action
	 * @param KalturaMailJobData $mailJobData
	 * @dataProvider provideData
	 */
	public function testAddMailJob(KalturaMailJobData $mailJobData)
	{
		$resultObject = $this->client->jobs->addMailJob($mailJobData);
	}

	/**
	 * Tests jobs->addBatchJob action
	 * @param KalturaBatchJob $batchJob
	 * @dataProvider provideData
	 */
	public function testAddBatchJob(KalturaBatchJob $batchJob)
	{
		$resultObject = $this->client->jobs->addBatchJob($batchJob);
		$this->assertType('KalturaBatchJob', $resultObject);
	}

	/**
	 * Tests jobs->getStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testGetStatus($jobId, KalturaBatchJobType $jobType)
	{
		$resultObject = $this->client->jobs->getStatus($jobId, $jobType);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->deleteJob action
	 * @param int $jobId
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testDeleteJob($jobId, KalturaBatchJobType $jobType)
	{
		$resultObject = $this->client->jobs->deleteJob($jobId, $jobType);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->abortJob action
	 * @param int $jobId
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testAbortJob($jobId, KalturaBatchJobType $jobType)
	{
		$resultObject = $this->client->jobs->abortJob($jobId, $jobType);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->retryJob action
	 * @param int $jobId
	 * @param KalturaBatchJobType $jobType
	 * @dataProvider provideData
	 */
	public function testRetryJob($jobId, KalturaBatchJobType $jobType)
	{
		$resultObject = $this->client->jobs->retryJob($jobId, $jobType);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
	}

	/**
	 * Tests jobs->listBatchJobs action
	 * @param KalturaBatchJobFilterExt $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testListBatchJobs(KalturaBatchJobFilterExt $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->jobs->listBatchJobs($filter, $pager);
		$this->assertType('KalturaBatchJobListResponse', $resultObject);
	}

}
