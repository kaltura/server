<?php
require_once("tests/bootstrapTests.php");

class BatchExtractMediaTests extends PHPUnit_Framework_TestCase 
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
	
	public function testAddExtractMediaJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addExtractMediaJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "avi";
		$token = "$uniqueId.$ext";
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "avi");
		$fullPath = $uploadPath[0];
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/example.avi", $fullPath);
		
		$flavorAsset = new flavorAsset();
		$flavorAsset->setPartnerId($mediaEntry->partnerId);
		$flavorAsset->setEntryId($mediaEntry->id);
		$flavorAsset->save();
		
		$data = new KalturaExtractMediaJobData();
		$data->flavorAssetId = $flavorAsset->getId();
		$data->srcFileSyncLocalPath = $fullPath;
		
		$batchJob = new KalturaBatchJob();
		$batchJob->entryId = $mediaEntry->id;
		$batchJob->partnerId = $mediaEntry->partnerId;
		
		$newBatchJob = $batchJobService->addExtractMediaJobAction(clone $batchJob, KalturaExtractMediaType::ENTRY_INPUT, $data);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testAddMediaInfo()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "addMediaInfo", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "avi";
		$token = "$uniqueId.$ext";
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "avi");
		$fullPath = $uploadPath[0];
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/example.avi", $fullPath);
		
		$flavorAsset = new flavorAsset();
		$flavorAsset->save();
		
		$mediaInfo = new KalturaMediaInfo();
		$mediaInfo->flavor_asset_id = $flavorAsset->getId();
		$mediaInfo->description = 'test media info';
		
		$newMediaInfo = $batchJobService->addMediaInfoAction(clone $mediaInfo);
		$this->assertNotNull($newMediaInfo);
		
		return $newMediaInfo;
	}
	
	public function testGetExtractMediaStatus()
	{
		$newBatchJob = $this->testAddExtractMediaJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getExtractMediaStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getExtractMediaStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testGetExclusiveExtractMediaJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testAddExtractMediaJob();
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveExtractMediaJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusiveExtractMediaJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $listedBatchJob;
		}
		
		$this->fail('New created ExtractMedia job not retreived');
		return null;
	}

	public function testAbortQueuedExtractMedia()
	{
		$newBatchJob = $this->testGetExclusiveExtractMediaJobs();
		$this->updateExclusiveExtractMediaJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortExtractMedia", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortExtractMediaAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testUpdateExclusiveExtractMediaJobs()
	{
		$newBatchJob = null;
		
		$newBatchJob = $this->updateExclusiveExtractMediaJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveExtractMediaJobs(KalturaBatchJobStatus::PROCESSING, $newBatchJob);
		$newBatchJob = $this->updateExclusiveExtractMediaJobs(KalturaBatchJobStatus::PROCESSED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveExtractMediaJobs(KalturaBatchJobStatus::MOVEFILE, $newBatchJob);
		$newBatchJob = $this->updateExclusiveExtractMediaJobs(KalturaBatchJobStatus::FINISHED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveExtractMediaJobs(KalturaBatchJobStatus::FAILED, $newBatchJob);
	}
	
	private function updateExclusiveExtractMediaJobs($status, $newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusiveExtractMediaJobs($newBatchJob);
			
		$newBatchJob->status = $status;
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "updateExclusiveExtractMediaJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$newBatchJob = $batchJobService->updateExclusiveExtractMediaJobAction($newBatchJob->id, $this->prepareLockKey(), $newBatchJob);
		
		$this->assertEquals($status, $newBatchJob->status);
		
		return $newBatchJob;
	}
	
	public function testFreeExclusiveExtractMediaJobs()
	{
		$newBatchJob = $this->testGetExclusiveExtractMediaJobs();
		if(is_null($newBatchJob))
			return;
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusiveExtractMediaJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusiveExtractMediaJobAction($newBatchJob->id, $this->prepareLockKey());
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