<?php
require_once("tests/bootstrapTests.php");

class BatchPullTests extends PHPUnit_Framework_TestCase 
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
	
	public function testAddPullJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addPullJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$data = new KalturaPullJobData();
		$data->srcFileUrl = 'http://kaldev.kaltura.com/content/zbale/9spkxiz8m4_100007.avi';
		$batchJob = new KalturaBatchJob();
		$batchJob->entryId = $mediaEntry->id;
		$batchJob->partnerId = $mediaEntry->partnerId;
		$newBatchJob = $batchJobService->addPullJobAction(clone $batchJob, $data);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testGetPullStatus()
	{
		$newBatchJob = $this->testAddPullJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getPullStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getPullStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testAbortPendingPull()
	{
		$newBatchJob = $this->testAddPullJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortPull", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortPullAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testGetExclusivePullJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testAddPullJob();
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusivePullJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusivePullJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $listedBatchJob;
		}
		
		$this->fail('New created Pull job not retreived');
		return null;
	}

	public function testAbortQueuedPull()
	{
		$newBatchJob = $this->testGetExclusivePullJobs();
		$this->updateExclusivePullJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortPull", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortPullAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testUpdateExclusivePullJobs()
	{
		$newBatchJob = null;
		
		$newBatchJob = $this->updateExclusivePullJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		$newBatchJob = $this->updateExclusivePullJobs(KalturaBatchJobStatus::PROCESSING, $newBatchJob);
		$newBatchJob = $this->updateExclusivePullJobs(KalturaBatchJobStatus::PROCESSED, $newBatchJob);
		$newBatchJob = $this->updateExclusivePullJobs(KalturaBatchJobStatus::MOVEFILE, $newBatchJob);
		$newBatchJob = $this->updateExclusivePullJobs(KalturaBatchJobStatus::ALMOST_DONE, $newBatchJob);
		$newBatchJob = $this->updateExclusivePullJobs(KalturaBatchJobStatus::FINISHED, $newBatchJob);
		$newBatchJob = $this->updateExclusivePullJobs(KalturaBatchJobStatus::FAILED, $newBatchJob);
	}
	
	private function updateExclusivePullJobs($status, $newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusivePullJobs($newBatchJob);
			
		$newBatchJob->status = $status;
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "updateExclusivePullJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$newBatchJob = $batchJobService->updateExclusivePullJobAction($newBatchJob->id, $this->prepareLockKey(), $newBatchJob);
		
		$this->assertEquals($status, $newBatchJob->status);
		
		return $newBatchJob;
	}
	
	public function testFreeExclusivePullJobs()
	{
		$newBatchJob = $this->testGetExclusivePullJobs();
		if(is_null($newBatchJob))
			return;
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusivePullJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusivePullJobAction($newBatchJob->id, $this->prepareLockKey());
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