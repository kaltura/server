<?php
require_once("tests/bootstrapTests.php");

class BatchPostConvertTests extends PHPUnit_Framework_TestCase 
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
	
	public function testAddPostConvertJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addPostConvertJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		$srcPath = "$currentPath/../files/example.avi";
		
		$flavorAsset = new flavorAsset();
		$flavorAsset->setPartnerId($mediaEntry->partnerId);
		$flavorAsset->setEntryId($mediaEntry->id);
		$flavorAsset->save();
		
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::copyFromFile($srcPath, $syncKey);
		
		$partner = PartnerPeer::retrieveByPK($mediaEntry->partnerId);
		$entry = entryPeer::retrieveByPK($mediaEntry->id);
		$partnerOffset = $partner->getDefThumbOffset();
		if(!$partnerOffset)
			$partnerOffset = null;
			
		$entry->setLengthInMsecs(1000);
		$offset = $entry->getBestThumbOffset($partnerOffset);
		
		$data = new KalturaPostConvertJobData();
		$data->srcFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		$data->flavorAssetId = $flavorAsset->getId();
		$data->createThumb = true;
		$data->thumbOffset = $offset;
		
		$batchJob = new KalturaBatchJob();
		$batchJob->entryId = $mediaEntry->id;
		$batchJob->partnerId = $mediaEntry->partnerId;
		$newBatchJob = $batchJobService->addPostConvertJobAction(clone $batchJob, $data);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testGetPostConvertStatus()
	{
		$newBatchJob = $this->testAddPostConvertJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getPostConvertStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getPostConvertStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testAbortPendingPostConvert()
	{
		$newBatchJob = $this->testAddPostConvertJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortPostConvert", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortPostConvertAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testGetExclusivePostConvertJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testAddPostConvertJob();
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusivePostConvertJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusivePostConvertJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $listedBatchJob;
		}
		
		$this->fail('New created PostConvert job not retreived');
		return null;
	}

	public function testAbortQueuedPostConvert()
	{
		$newBatchJob = $this->testGetExclusivePostConvertJobs();
		$this->updateExclusivePostConvertJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortPostConvert", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortPostConvertAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testUpdateExclusivePostConvertJobs()
	{
		$newBatchJob = null;
		
		$newBatchJob = $this->updateExclusivePostConvertJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		$newBatchJob = $this->updateExclusivePostConvertJobs(KalturaBatchJobStatus::PROCESSING, $newBatchJob);
		$newBatchJob = $this->updateExclusivePostConvertJobs(KalturaBatchJobStatus::PROCESSED, $newBatchJob);
		$newBatchJob = $this->updateExclusivePostConvertJobs(KalturaBatchJobStatus::MOVEFILE, $newBatchJob);
		$newBatchJob = $this->updateExclusivePostConvertJobs(KalturaBatchJobStatus::ALMOST_DONE, $newBatchJob);
		$newBatchJob = $this->updateExclusivePostConvertJobs(KalturaBatchJobStatus::FINISHED, $newBatchJob);
		$newBatchJob = $this->updateExclusivePostConvertJobs(KalturaBatchJobStatus::FAILED, $newBatchJob);
	}
	
	private function updateExclusivePostConvertJobs($status, $newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusivePostConvertJobs($newBatchJob);
			
		$newBatchJob->status = $status;
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "updateExclusivePostConvertJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$newBatchJob = $batchJobService->updateExclusivePostConvertJobAction($newBatchJob->id, $this->prepareLockKey(), $newBatchJob);
		
		$this->assertEquals($status, $newBatchJob->status);
		
		return $newBatchJob;
	}
	
	
	public function testFreeExclusivePostConvertJobs()
	{
		$newBatchJob = $this->testGetExclusivePostConvertJobs();
		if(is_null($newBatchJob))
			return;
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusivePostConvertJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusivePostConvertJobAction($newBatchJob->id, $this->prepareLockKey());
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