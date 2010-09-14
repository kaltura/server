<?php
require_once("tests/bootstrapTests.php");

class BatchConvertTests extends PHPUnit_Framework_TestCase 
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
	
	public function testAddConvertJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addConvertJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "avi";
		$token = "$uniqueId.$ext";
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "avi");
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/example.avi", $fullPath);
		
		$data = new KalturaConvertJobData();
		$data->srcFileSyncLocalPath = $fullPath;
		$data->flavorParams = new KalturaFlavorParams();
		$data->mediaInfoId = 1;
		
		
		$batchJob = new KalturaBatchJob();
		$batchJob->entryId = $mediaEntry->id;
		$batchJob->partnerId = $mediaEntry->partnerId;
		$newBatchJob = $batchJobService->addConvertJobAction(clone $batchJob, $data, flavorParams::ENGINE_TYPE_KALTURA_COM);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testGetConvertStatus()
	{
		$newBatchJob = $this->testAddConvertJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getConvertStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getConvertStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}

	public function testAbortPendingConvert()
	{
		$newBatchJob = $this->testAddConvertJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortConvert", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortConvertAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}

	public function testAbortConvertProfile()
	{
		$newBatchJob = $this->testAddConvertProfileJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortConvertProfile", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortConvertProfileAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}

	public function testAbortRemoteConvert()
	{
		$newBatchJob = $this->testAddRemoteConvertJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "abortRemoteConvert", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->abortRemoteConvertAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testAddConvertProfileJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addConvertProfileJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$data = new KalturaConvertProfileJobData();
		$data->inputFileSyncLocalPath = 'C:\web\content\imports\data\9spkxiz8m4_100007.avi';
		
		$batchJob = new KalturaBatchJob();
		$batchJob->entryId = $mediaEntry->id;
		$batchJob->partnerId = $mediaEntry->partnerId;
		$newBatchJob = $batchJobService->addConvertProfileJobAction(clone $batchJob, $data);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testGetConvertProfileStatus()
	{
		$newBatchJob = $this->testAddConvertProfileJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getConvertProfileStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getConvertProfileStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testAddRemoteConvertJob()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "addRemoteConvertJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$data = new KalturaRemoteConvertJobData();
		$data->srcFileUrl = 'http://kaldev.kaltura.com/content/zbale/9spkxiz8m4_100007.avi';
		
		$batchJob = new KalturaBatchJob();
		$batchJob->partnerId = KalturaTestsHelpers::getPartnerId();
		$newBatchJob = $batchJobService->addRemoteConvertJobAction(clone $batchJob, $data);
		$this->assertNotNull($newBatchJob);
		
		$this->createdJobs[] = $newBatchJob->id;
		
		return $newBatchJob;
	}
	
	public function testGetRemoteConvertStatus()
	{
		$newBatchJob = $this->testAddRemoteConvertJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("jobs", "getRemoteConvertStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$batchJobResponse = $batchJobService->getRemoteConvertStatusAction($newBatchJob->id);
		$this->assertNotNull($batchJobResponse);
	}
	
	public function testGetExclusiveConvertJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testAddConvertJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveConvertJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$supportedConversionEngineTypes = new KalturaSupportedSubTypes();
		$supportedConversionEngineTypes->supportedSubTypes = KalturaConversionEngineType::KALTURA_COM . ',' . KalturaConversionEngineType::FFMPEG;
		$jobList = $batchJobService->getExclusiveConvertJobsAction($this->prepareLockKey(), 6000, 10, null, $supportedConversionEngineTypes);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $listedBatchJob;
		}
		
		$this->fail('New created Convert job not retreived');
		return null;
	}
	
	private function updateExclusiveConvertJobs($status, $newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusiveConvertJobs($newBatchJob);
			
		$newBatchJob->status = $status;
			
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "updateExclusiveConvertJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$newBatchJob = $batchJobService->updateExclusiveConvertJobAction($newBatchJob->id, $this->prepareLockKey(), $newBatchJob);
		return $newBatchJob;
	}
	
	public function testUpdateExclusiveConvertJobs()
	{
		$newBatchJob = null;
		
		$newBatchJob = $this->updateExclusiveConvertJobs(KalturaBatchJobStatus::QUEUED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveConvertJobs(KalturaBatchJobStatus::PROCESSING, $newBatchJob);
		$newBatchJob = $this->updateExclusiveConvertJobs(KalturaBatchJobStatus::PROCESSED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveConvertJobs(KalturaBatchJobStatus::MOVEFILE, $newBatchJob);
		$newBatchJob = $this->updateExclusiveConvertJobs(KalturaBatchJobStatus::ALMOST_DONE, $newBatchJob);
		$newBatchJob = $this->updateExclusiveConvertJobs(KalturaBatchJobStatus::FINISHED, $newBatchJob);
		$newBatchJob = $this->updateExclusiveConvertJobs(KalturaBatchJobStatus::FAILED, $newBatchJob);
	}

	public function testGetExclusiveAlmostDoneRemoteConvertJobs()
	{
		$newBatchJob = $this->testAddRemoteConvertJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveAlmostDoneRemoteConvertJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusiveAlmostDoneRemoteConvertJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $newBatchJob;
		}
		
		$this->fail('New created RemoteConvert job not retreived');
		return null;
	}

	public function testGetExclusiveAlmostDoneConvertJobs()
	{
		$newBatchJob = $this->updateExclusiveConvertJobs(KalturaBatchJobStatus::ALMOST_DONE);
		$this->testFreeExclusiveConvertJobs($newBatchJob);
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveAlmostDoneConvertJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusiveAlmostDoneConvertJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $newBatchJob;
		}
		
		$this->fail('New created job not retreived');
		return null;
	}

	public function testGetExclusiveAlmostDoneConvertProfileJobs()
	{
		$newBatchJob = $this->testAddConvertProfileJob();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "getExclusiveAlmostDoneConvertProfileJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$jobList = $batchJobService->getExclusiveAlmostDoneConvertProfileJobsAction($this->prepareLockKey(), 6000, 10, null);
		
		if(!$jobList->count)
			$this->fail('No exclusive jobs retreived');
		
		for($i = 0; $i < $jobList->count; $i++)
		{
			$listedBatchJob = $jobList->offsetGet($i);
			if($listedBatchJob->id == $newBatchJob->id)
				return $newBatchJob;
		}
		
		$this->fail('New created ConvertProfile job not retreived');
		return null;
	}
	
	public function testFreeExclusiveConvertProfileJobs()
	{
		$newBatchJob = $this->testGetExclusiveAlmostDoneConvertProfileJobs();
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusiveConvertProfileJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusiveConvertProfileJobAction($newBatchJob->id, $this->prepareLockKey());
	}
	
	public function testFreeExclusiveConvertJobs($newBatchJob = null)
	{
		if(is_null($newBatchJob))
			$newBatchJob = $this->testGetExclusiveConvertJobs();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusiveConvertJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusiveConvertJobAction($newBatchJob->id, $this->prepareLockKey());
	}
	
	public function testFreeExclusiveRemoteConvertJobs()
	{
		$newBatchJob = $this->testGetExclusiveAlmostDoneRemoteConvertJobs();
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "freeExclusiveRemoteConvertJob", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		$batchJobService->freeExclusiveRemoteConvertJobAction($newBatchJob->id, $this->prepareLockKey());
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