<?php
require_once("tests/bootstrapTests.php");

class BatchNotificationTests extends PHPUnit_Framework_TestCase 
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
	
	public function testAddNotificationJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addNotificationJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$data = new KalturaNotificationJobData();
		$data->type = KalturaNotificationType::ENTRY_UPDATE;
		$data->objectType = KalturaNotificationObjectType::ENTRY;
		$data->objectId = $mediaEntry->id;
			
		$batchJob = new KalturaBatchJob();
		$batchJob->entryId = $mediaEntry->id;
		$batchJob->partnerId = $mediaEntry->partnerId;
		$newBatchJob = $batchJobService->addNotificationJobAction(clone $batchJob, $data);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testGetNotificationStatus()
	{
		$newBatchJob = $this->testAddNotificationJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getNotificationStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getNotificationStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testGetExclusiveNotificationJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testAddNotificationJob();
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveNotificationJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusiveNotificationJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->notifications->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->notifications->count; $i++)
		{
			$listedBatchJob = $jobList->notifications->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $listedBatchJob;
		}
		
		$this->fail('New created Notification job not retreived');
		return null;
	}

	public function testAbortQueuedNotification()
	{
		$newBatchJob = $this->testGetExclusiveNotificationJobs();
		$this->updateExclusiveNotificationJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortNotification", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortNotificationAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testUpdateExclusiveNotificationJobs()
	{
		$newBatchJob = null;
		
		$newBatchJob = $this->updateExclusiveNotificationJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveNotificationJobs(KalturaBatchJobStatus::PROCESSING, $newBatchJob);
		$newBatchJob = $this->updateExclusiveNotificationJobs(KalturaBatchJobStatus::PROCESSED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveNotificationJobs(KalturaBatchJobStatus::MOVEFILE, $newBatchJob);
		$newBatchJob = $this->updateExclusiveNotificationJobs(KalturaBatchJobStatus::ALMOST_DONE, $newBatchJob);
		
		
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "flv";
		$token = "$uniqueId.$ext";
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, $ext);
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/kaltura_logo_animated_black.flv", $fullPath);
		
		$newBatchJob->data->destFileLocalPath = $fullPath;
		
		$newBatchJob = $this->updateExclusiveNotificationJobs(KalturaBatchJobStatus::FINISHED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveNotificationJobs(KalturaBatchJobStatus::FAILED, $newBatchJob);
	}
	
	private function updateExclusiveNotificationJobs($status, $newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusiveNotificationJobs($newBatchJob);
			
		$newBatchJob->status = $status;
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "updateExclusiveNotificationJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$newBatchJob = $batchJobService->updateExclusiveNotificationJobAction($newBatchJob->id, $this->prepareLockKey(), $newBatchJob);
		
		$this->assertEquals($status, $newBatchJob->status);
		
		return $newBatchJob;
	}
	
	
	public function testFreeExclusiveNotificationJobs()
	{
		$newBatchJob = $this->testGetExclusiveNotificationJobs();
		if(is_null($newBatchJob))
			return;
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusiveNotificationJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusiveNotificationJobAction($newBatchJob->id, $this->prepareLockKey());
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