<?php
require_once("tests/bootstrapTests.php");

class BatchBulkUploadTests extends PHPUnit_Framework_TestCase 
{
	private $createdJobs = array();
	
	public function setUp() 
	{
		$this->createdJobs = array();
	}
	
	public function tearDown() 
	{
		parent::tearDown();
		
		foreach($this->createdJobs as $batchJobId)
		{
			$batchJob = BatchJobPeer::retrieveByPK($batchJobId);
			
			if(!$batchJob)
				continue;
				
			$syncKey = $batchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV);
			$fileSync = FileSyncPeer::retreiveByFileSyncKey($syncKey);
			if($fileSync)
				$fileSync->delete();
				
			$syncKey = $batchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD_RESULT);
			$fileSync = FileSyncPeer::retreiveByFileSyncKey($syncKey);
			if($fileSync)
				$fileSync->delete();
				
			$batchJob->delete();
		}
		$this->createdJobs = array();
	}
	
	public function testAddBulkUploadJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addBulkUploadJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "csv";
		
		$token = "$uniqueId.$ext";
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "csv");
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/sample.csv", $fullPath);
		
		$fileData = array(
			'name' => 'sample.csv',
			'tmp_name' => $fullPath,
			'error' => null,
			'size' => filesize($fullPath)
		);
		
		$data = new KalturaBulkUploadJobData();
		$data->uid = KalturaTestsHelpers::getUserId();
		$data->uploadedBy = KalturaTestsHelpers::getRandomString(10);
		
		
		$batchJob = new KalturaBatchJob();
		$batchJob->partnerId = KalturaTestsHelpers::getPartnerId();
		$newBatchJob = $batchJobService->addBulkUploadJobAction(clone $batchJob, $data, $fileData);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testGetBulkUploadStatus()
	{
		$newBatchJob = $this->testAddBulkUploadJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getBulkUploadStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getBulkUploadStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testAbortPendingBulkUpload()
	{
		$newBatchJob = $this->testAddBulkUploadJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortBulkUpload", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortBulkUploadAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testGetExclusiveBulkUploadJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testAddBulkUploadJob();
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveBulkUploadJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusiveBulkUploadJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $listedBatchJob;
		}
		
		$this->fail('New created BulkUpload job not retreived');
		return null;
	}

	public function testAbortQueuedBulkUpload()
	{
		$newBatchJob = $this->testGetExclusiveBulkUploadJobs();
		$this->updateExclusiveBulkUploadJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortBulkUpload", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortBulkUploadAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testUpdateExclusiveBulkUploadJobs()
	{
		$newBatchJob = null;
		
		$newBatchJob = $this->updateExclusiveBulkUploadJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveBulkUploadJobs(KalturaBatchJobStatus::PROCESSING, $newBatchJob);
		$newBatchJob = $this->updateExclusiveBulkUploadJobs(KalturaBatchJobStatus::PROCESSED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveBulkUploadJobs(KalturaBatchJobStatus::MOVEFILE, $newBatchJob);
		$newBatchJob = $this->updateExclusiveBulkUploadJobs(KalturaBatchJobStatus::ALMOST_DONE, $newBatchJob);
		$newBatchJob = $this->updateExclusiveBulkUploadJobs(KalturaBatchJobStatus::FINISHED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveBulkUploadJobs(KalturaBatchJobStatus::FAILED, $newBatchJob);
	}
	
	private function updateExclusiveBulkUploadJobs($status, $newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusiveBulkUploadJobs($newBatchJob);
			
		$newBatchJob->status = $status;
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "updateExclusiveBulkUploadJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$newBatchJob = $batchJobService->updateExclusiveBulkUploadJobAction($newBatchJob->id, $this->prepareLockKey(), $newBatchJob);
		
		$this->assertEquals($status, $newBatchJob->status);
		
		return $newBatchJob;
	}

	public function testGetExclusiveAlmostDoneBulkUploadJobs()
	{
		$newBatchJob = $this->updateExclusiveBulkUploadJobs(KalturaBatchJobStatus::ALMOST_DONE);
		$this->testFreeExclusiveBulkUploadJobs($newBatchJob);
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveAlmostDoneBulkUploadJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusiveAlmostDoneBulkUploadJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $newBatchJob;
		}
		
		$this->fail('New created BulkUpload job not retreived');
		return null;
	}
	
	public function testFreeExclusiveBulkUploadJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusiveBulkUploadJobs();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusiveBulkUploadJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusiveBulkUploadJobAction($newBatchJob->id, $this->prepareLockKey());
	}
	
	private function prepareLockKey()
	{
		$lockKey = new KalturaExclusiveLockKey();
		
		$lockKey->schedulerId = 1;
		$lockKey->workerId = 1;
		$lockKey->batchIndex = 1;
		
		return $lockKey;
	}
}

?>