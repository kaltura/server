<?php
require_once("tests/bootstrapTests.php");

class BatchMailTests extends PHPUnit_Framework_TestCase 
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
	
	public function testAddMailJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addMailJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$data = new KalturaMailJobData();
		$data->mailType = KalturaMailType::MAIL_TYPE_CLIP_ADDED;
		$data->status = KalturaMailJobStatus::PENDING;
			
		$batchJob = new KalturaBatchJob();
		$batchJob->entryId = $mediaEntry->id;
		$batchJob->partnerId = $mediaEntry->partnerId;
		$newBatchJob = $batchJobService->addMailJobAction(clone $batchJob, $data);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testGetMailStatus()
	{
		$newBatchJob = $this->testAddMailJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getMailStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getMailStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testGetExclusiveMailJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testAddMailJob();
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveMailJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusiveMailJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $listedBatchJob;
		}
		
		$this->fail('New created Mail job not retreived');
		return null;
	}

	public function testAbortQueuedMail()
	{
		$newBatchJob = $this->testGetExclusiveMailJobs();
		$this->updateExclusiveMailJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortMail", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortMailAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testUpdateExclusiveMailJobs()
	{
		$newBatchJob = null;
		
		$newBatchJob = $this->updateExclusiveMailJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveMailJobs(KalturaBatchJobStatus::PROCESSING, $newBatchJob);
		$newBatchJob = $this->updateExclusiveMailJobs(KalturaBatchJobStatus::PROCESSED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveMailJobs(KalturaBatchJobStatus::MOVEFILE, $newBatchJob);
		$newBatchJob = $this->updateExclusiveMailJobs(KalturaBatchJobStatus::ALMOST_DONE, $newBatchJob);
		
		
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "flv";
		$token = "$uniqueId.$ext";
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, $ext);
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/kaltura_logo_animated_black.flv", $fullPath);
		
		$newBatchJob->data->destFileLocalPath = $fullPath;
		
		$newBatchJob = $this->updateExclusiveMailJobs(KalturaBatchJobStatus::FINISHED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveMailJobs(KalturaBatchJobStatus::FAILED, $newBatchJob);
	}
	
	private function updateExclusiveMailJobs($status, $newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusiveMailJobs($newBatchJob);
			
		$newBatchJob->status = $status;
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "updateExclusiveMailJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$newBatchJob = $batchJobService->updateExclusiveMailJobAction($newBatchJob->id, $this->prepareLockKey(), $newBatchJob);
		
		$this->assertEquals($status, $newBatchJob->status);
		
		return $newBatchJob;
	}
	
	
	public function testFreeExclusiveMailJobs()
	{
		$newBatchJob = $this->testGetExclusiveMailJobs();
		if(is_null($newBatchJob))
			return;
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusiveMailJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusiveMailJobAction($newBatchJob->id, $this->prepareLockKey());
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
