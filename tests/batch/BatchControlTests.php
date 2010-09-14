<?php
require_once("tests/bootstrapTests.php");

class BatchControlTests extends PHPUnit_Framework_TestCase 
{
	const TEST_SCHEDULER_ID = 5555;
	const TEST_SCHEDULER_NAME = 'Test Sched';
	
	const TEST_WORKER_ID = 10;
	const TEST_WORKER_NAME = 'Test Job';
	
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
		parent::tearDown();
		
		SchedulerPeer::deleteBySchedulerConfigId(self::TEST_SCHEDULER_ID);
	}
		
	public function testConfigLoadedScheduler()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batchcontrol", "configLoaded", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$newSchedulerConfig = $batchJobService->configLoadedAction(self::TEST_SCHEDULER_ID, self::TEST_SCHEDULER_NAME, 'logDir', 'c:/web/kaltura/log');
		$this->assertNotNull($newSchedulerConfig);
				
		$newSchedulerConfig = $batchJobService->configLoadedAction(self::TEST_SCHEDULER_ID, self::TEST_SCHEDULER_NAME, 'maxExecutionTime', '45');
		$this->assertNotNull($newSchedulerConfig);
	}
	
	public function testConfigLoadedWorker()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batchcontrol", "configLoaded", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$newSchedulerConfig = $batchJobService->configLoadedAction(self::TEST_SCHEDULER_ID, self::TEST_SCHEDULER_NAME, 'name', 'KAsyncDirectoryCleanup', null, self::TEST_WORKER_ID, self::TEST_WORKER_NAME);
		$this->assertNotNull($newSchedulerConfig);
				
		$newSchedulerConfig = $batchJobService->configLoadedAction(self::TEST_SCHEDULER_ID, self::TEST_SCHEDULER_NAME, 'params', 'c:/web/tmp/1/', 'path', self::TEST_WORKER_ID, self::TEST_WORKER_NAME);
		$this->assertNotNull($newSchedulerConfig);
		
		return $newSchedulerConfig;
	}
	
	public function testReportStatus()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batchcontrol", "reportStatus", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$schedulerStatuses = new KalturaSchedulerStatusArray();
		
		$schedulerStatus = new KalturaSchedulerStatus();
		$schedulerStatus->schedulerConfiguredId = self::TEST_SCHEDULER_ID;
		$schedulerStatus->workerConfiguredId = self::TEST_WORKER_ID;
		$schedulerStatus->workerType = KalturaBatchJobType::IMPORT;
		$schedulerStatus->type = KalturaSchedulerStatusType::RUNNING_BATCHES_COUNT;
		$schedulerStatus->value = 5;
		$schedulerStatuses[] = $schedulerStatus;
		
		$schedulerStatus = new KalturaSchedulerStatus();
		$schedulerStatus->schedulerConfiguredId = self::TEST_SCHEDULER_ID;
		$schedulerStatus->type = KalturaSchedulerStatusType::RUNNING_BATCHES_CPU;
		$schedulerStatus->value = 80;
		$schedulerStatuses[] = $schedulerStatus;
		
		$newSchedulerStatusResponse = $batchJobService->reportStatusAction(self::TEST_SCHEDULER_ID, self::TEST_SCHEDULER_NAME, $schedulerStatuses);
		$this->assertNotNull($newSchedulerStatusResponse);
	}
	
	public function testSetConfig()
	{
		$config = $this->testConfigLoadedWorker();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batchcontrol", "setConfig", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$newCommand = $batchJobService->setConfigAction($config->schedulerId, self::TEST_SCHEDULER_NAME, 1, 'tester admin', 'executionTimes', '4,16', null, $config->workerId, self::TEST_WORKER_NAME);
		
		$this->assertNotNull($newCommand);
		
		return $newCommand;
	}
	
	public function testStart()
	{
		$config = $this->testConfigLoadedWorker();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batchcontrol", "start", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$newCommand = $batchJobService->startAction($config->schedulerId, self::TEST_SCHEDULER_NAME, KalturaControlPanelCommandTargetType::JOB, 1, 'tester admin', $config->workerId, self::TEST_WORKER_NAME);
		
		$this->assertNotNull($newCommand);
		
		return $newCommand;
	}
	
	public function testStop()
	{
		$config = $this->testConfigLoadedWorker();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batchcontrol", "stop", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$newCommand = $batchJobService->stopAction($config->schedulerId, self::TEST_SCHEDULER_NAME, KalturaControlPanelCommandTargetType::JOB, 1, 'tester admin', 'stop test', $config->workerId, self::TEST_WORKER_NAME);
		
		$this->assertNotNull($newCommand);
		
		return $newCommand;
	}
	
	public function testGetCommand()
	{
		$command = $this->testStop();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batchcontrol", "getCommand", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$newCommand = $batchJobService->getCommandAction($command->id);
		
		$this->assertNotNull($newCommand);
		
		return $newCommand;
	}
	
	public function testSetCommandResult()
	{
		$command = $this->testStop();
		
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batchcontrol", "setCommandResult", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$newCommand = $batchJobService->setCommandResultAction($command->id, KalturaControlPanelCommandStatus::FAILED, 'failure tested');
		
		$this->assertNotNull($newCommand);
		
		return $newCommand;
	}
	
}

?>