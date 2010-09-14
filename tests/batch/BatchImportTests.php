<?php
require_once("tests/bootstrapTests.php");

class BatchImportTests extends PHPUnit_Framework_TestCase 
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
			if ($batchJob)
				$batchJob->delete();
		}
		$this->createdJobs = array();
	}
	
	public function testAddImportJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addImportJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$data = new KalturaImportJobData();
		$data->srcFileUrl = 'http://kaldev.kaltura.com/content/zbale/9spkxiz8m4_100007.avi';
		$batchJob = new KalturaBatchJob();
		$batchJob->entryId = $mediaEntry->id;
		$batchJob->partnerId = $mediaEntry->partnerId;
		$newBatchJob = $batchJobService->addImportJobAction(clone $batchJob, $data);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testGetImportStatus()
	{
		$newBatchJob = $this->testAddImportJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getImportStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getImportStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testGetExclusiveImportJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testAddImportJob();
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveImportJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusiveImportJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $listedBatchJob;
		}
		
		$this->fail('New created Import job not retreived');
		return null;
	}

	public function testAbortQueuedImport()
	{
		$newBatchJob = $this->testGetExclusiveImportJobs();
		$this->updateExclusiveImportJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortImport", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortImportAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testUpdateExclusiveImportJobs()
	{
		$newBatchJob = null;
		
		$newBatchJob = $this->updateExclusiveImportJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveImportJobs(KalturaBatchJobStatus::PROCESSING, $newBatchJob);
		$newBatchJob = $this->updateExclusiveImportJobs(KalturaBatchJobStatus::PROCESSED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveImportJobs(KalturaBatchJobStatus::MOVEFILE, $newBatchJob);
		$newBatchJob = $this->updateExclusiveImportJobs(KalturaBatchJobStatus::ALMOST_DONE, $newBatchJob);
		
		
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "flv";
		$token = "$uniqueId.$ext";
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, $ext);
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/kaltura_logo_animated_black.flv", $fullPath);
		
		$newBatchJob->data->destFileLocalPath = $fullPath;
		
		$newBatchJob = $this->updateExclusiveImportJobs(KalturaBatchJobStatus::FINISHED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveImportJobs(KalturaBatchJobStatus::FAILED, $newBatchJob);
	}
	
	private function updateExclusiveImportJobs($status, $newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusiveImportJobs($newBatchJob);
			
		$newBatchJob->status = $status;
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "updateExclusiveImportJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$newBatchJob = $batchJobService->updateExclusiveImportJobAction($newBatchJob->id, $this->prepareLockKey(), $newBatchJob);
		
		$this->assertEquals($status, $newBatchJob->status);
		
		return $newBatchJob;
	}
	
	
	public function testFreeExclusiveImportJobs()
	{
		$newBatchJob = $this->testGetExclusiveImportJobs();
		if(is_null($newBatchJob))
			return;
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusiveImportJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusiveImportJobAction($newBatchJob->id, $this->prepareLockKey());
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