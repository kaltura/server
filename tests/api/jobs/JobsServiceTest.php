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
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetImportStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getImportStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteImport action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteImport($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteImport($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortImport action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortImport($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortImport($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryImport action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryImport($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryImport($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getProvisionProvideStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetProvisionProvideStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getProvisionProvideStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteProvisionProvide action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteProvisionProvide($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteProvisionProvide($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortProvisionProvide action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortProvisionProvide($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortProvisionProvide($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryProvisionProvide action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryProvisionProvide($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryProvisionProvide($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getProvisionDeleteStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetProvisionDeleteStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getProvisionDeleteStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteProvisionDelete action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteProvisionDelete($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteProvisionDelete($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortProvisionDelete action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortProvisionDelete($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortProvisionDelete($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryProvisionDelete action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryProvisionDelete($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryProvisionDelete($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getBulkUploadStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetBulkUploadStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getBulkUploadStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteBulkUpload action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteBulkUpload($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteBulkUpload($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortBulkUpload action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortBulkUpload($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortBulkUpload($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryBulkUpload action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryBulkUpload($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryBulkUpload($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getConvertStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetConvertStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getConvertStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getConvertCollectionStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetConvertCollectionStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getConvertCollectionStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getConvertProfileStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetConvertProfileStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getConvertProfileStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->addConvertProfileJob action
	 * @param string $entryId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAddConvertProfileJob($entryId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->addConvertProfileJob($entryId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getRemoteConvertStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetRemoteConvertStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getRemoteConvertStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteConvert action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteConvert($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteConvert($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortConvert action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortConvert($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortConvert($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryConvert action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryConvert($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryConvert($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteRemoteConvert action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteRemoteConvert($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteRemoteConvert($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortRemoteConvert action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortRemoteConvert($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortRemoteConvert($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryRemoteConvert action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryRemoteConvert($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryRemoteConvert($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteConvertCollection action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteConvertCollection($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteConvertCollection($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteConvertProfile action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteConvertProfile($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteConvertProfile($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortConvertCollection action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortConvertCollection($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortConvertCollection($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortConvertProfile action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortConvertProfile($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortConvertProfile($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryConvertCollection action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryConvertCollection($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryConvertCollection($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryConvertProfile action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryConvertProfile($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryConvertProfile($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getPostConvertStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetPostConvertStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getPostConvertStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deletePostConvert action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeletePostConvert($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deletePostConvert($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortPostConvert action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortPostConvert($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortPostConvert($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryPostConvert action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryPostConvert($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryPostConvert($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getCaptureThumbStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetCaptureThumbStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getCaptureThumbStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteCaptureThumb action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteCaptureThumb($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteCaptureThumb($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortCaptureThumb action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortCaptureThumb($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortCaptureThumb($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryCaptureThumb action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryCaptureThumb($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryCaptureThumb($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getPullStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetPullStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getPullStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deletePull action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeletePull($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deletePull($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortPull action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortPull($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortPull($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryPull action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryPull($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryPull($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getExtractMediaStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetExtractMediaStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getExtractMediaStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteExtractMedia action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteExtractMedia($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteExtractMedia($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortExtractMedia action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortExtractMedia($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortExtractMedia($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryExtractMedia action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryExtractMedia($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryExtractMedia($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getStorageExportStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetStorageExportStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getStorageExportStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteStorageExport action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteStorageExport($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteStorageExport($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortStorageExport action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortStorageExport($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortStorageExport($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryStorageExport action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryStorageExport($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryStorageExport($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getStorageDeleteStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetStorageDeleteStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getStorageDeleteStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteStorageDelete action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteStorageDelete($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteStorageDelete($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortStorageDelete action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortStorageDelete($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortStorageDelete($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryStorageDelete action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryStorageDelete($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryStorageDelete($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getNotificationStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetNotificationStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getNotificationStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteNotification action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteNotification($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteNotification($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortNotification action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortNotification($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortNotification($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryNotification action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryNotification($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryNotification($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getMailStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetMailStatus($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getMailStatus($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteMail action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteMail($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteMail($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortMail action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortMail($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortMail($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryMail action
	 * @param int $jobId
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryMail($jobId, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryMail($jobId, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->addMailJob action
	 * @param KalturaMailJobData $mailJobData
	 * @dataProvider provideData
	 */
	public function testAddMailJob(KalturaMailJobData $mailJobData)
	{
		$resultObject = $this->client->jobs->addMailJob($mailJobData);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->addBatchJob action
	 * @param KalturaBatchJob $batchJob
	 * @param KalturaBatchJob $reference
	 * @dataProvider provideData
	 */
	public function testAddBatchJob(KalturaBatchJob $batchJob, KalturaBatchJob $reference)
	{
		$resultObject = $this->client->jobs->addBatchJob($batchJob, $reference);
		$this->assertType('KalturaBatchJob', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->getStatus action
	 * @param int $jobId
	 * @param KalturaBatchJobType $jobType
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetStatus($jobId, KalturaBatchJobType $jobType, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->getStatus($jobId, $jobType, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->deleteJob action
	 * @param int $jobId
	 * @param KalturaBatchJobType $jobType
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testDeleteJob($jobId, KalturaBatchJobType $jobType, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->deleteJob($jobId, $jobType, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->abortJob action
	 * @param int $jobId
	 * @param KalturaBatchJobType $jobType
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testAbortJob($jobId, KalturaBatchJobType $jobType, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->abortJob($jobId, $jobType, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->retryJob action
	 * @param int $jobId
	 * @param KalturaBatchJobType $jobType
	 * @param KalturaBatchJobResponse $reference
	 * @dataProvider provideData
	 */
	public function testRetryJob($jobId, KalturaBatchJobType $jobType, KalturaBatchJobResponse $reference)
	{
		$resultObject = $this->client->jobs->retryJob($jobId, $jobType, $reference);
		$this->assertType('KalturaBatchJobResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests jobs->listBatchJobs action
	 * @param KalturaBatchJobFilterExt $filter
	 * @param KalturaFilterPager $pager
	 * @param KalturaBatchJobListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListBatchJobs(KalturaBatchJobFilterExt $filter = null, KalturaFilterPager $pager = null, KalturaBatchJobListResponse $reference)
	{
		$resultObject = $this->client->jobs->listBatchJobs($filter, $pager, $reference);
		$this->assertType('KalturaBatchJobListResponse', $resultObject);
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
