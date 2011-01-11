<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/BatchControlServiceBaseTest.php');

/**
 * batchcontrol service test case.
 */
class BatchControlServiceTest extends BatchControlServiceBaseTest
{
	/**
	 * Tests batchcontrol->reportStatus action
	 * @param KalturaScheduler $scheduler
	 * @param KalturaSchedulerStatusArray $schedulerStatuses
	 * @param KalturaWorkerQueueFilterArray $workerQueueFilters
	 * @param KalturaSchedulerStatusResponse $reference
	 * @dataProvider provideData
	 */
	public function testReportStatus(KalturaScheduler $scheduler, KalturaSchedulerStatusArray $schedulerStatuses, KalturaWorkerQueueFilterArray $workerQueueFilters, KalturaSchedulerStatusResponse $reference)
	{
		$resultObject = $this->client->batchcontrol->reportStatus($scheduler, $schedulerStatuses, $workerQueueFilters, $reference);
		$this->assertType('KalturaSchedulerStatusResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->configLoaded action
	 * @param KalturaScheduler $scheduler
	 * @param string $configParam
	 * @param string $configValue
	 * @param string $configParamPart
	 * @param int $workerConfigId
	 * @param string $workerName
	 * @param KalturaSchedulerConfig $reference
	 * @dataProvider provideData
	 */
	public function testConfigLoaded(KalturaScheduler $scheduler, $configParam, $configValue, $configParamPart = null, $workerConfigId = null, $workerName = null, KalturaSchedulerConfig $reference)
	{
		$resultObject = $this->client->batchcontrol->configLoaded($scheduler, $configParam, $configValue, $configParamPart, $workerConfigId, $workerName, $reference);
		$this->assertType('KalturaSchedulerConfig', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->stopScheduler action
	 * @param int $schedulerId
	 * @param int $adminId
	 * @param string $cause
	 * @param KalturaControlPanelCommand $reference
	 * @dataProvider provideData
	 */
	public function testStopScheduler($schedulerId, $adminId, $cause, KalturaControlPanelCommand $reference)
	{
		$resultObject = $this->client->batchcontrol->stopScheduler($schedulerId, $adminId, $cause, $reference);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->stopWorker action
	 * @param int $workerId
	 * @param int $adminId
	 * @param string $cause
	 * @param KalturaControlPanelCommand $reference
	 * @dataProvider provideData
	 */
	public function testStopWorker($workerId, $adminId, $cause, KalturaControlPanelCommand $reference)
	{
		$resultObject = $this->client->batchcontrol->stopWorker($workerId, $adminId, $cause, $reference);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->kill action
	 * @param int $workerId
	 * @param int $batchIndex
	 * @param int $adminId
	 * @param string $cause
	 * @param KalturaControlPanelCommand $reference
	 * @dataProvider provideData
	 */
	public function testKill($workerId, $batchIndex, $adminId, $cause, KalturaControlPanelCommand $reference)
	{
		$resultObject = $this->client->batchcontrol->kill($workerId, $batchIndex, $adminId, $cause, $reference);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->startWorker action
	 * @param int $workerId
	 * @param int $adminId
	 * @param string $cause
	 * @param KalturaControlPanelCommand $reference
	 * @dataProvider provideData
	 */
	public function testStartWorker($workerId, $adminId, $cause = null, KalturaControlPanelCommand $reference)
	{
		$resultObject = $this->client->batchcontrol->startWorker($workerId, $adminId, $cause, $reference);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->setSchedulerConfig action
	 * @param int $schedulerId
	 * @param int $adminId
	 * @param string $configParam
	 * @param string $configValue
	 * @param string $configParamPart
	 * @param string $cause
	 * @param KalturaControlPanelCommand $reference
	 * @dataProvider provideData
	 */
	public function testSetSchedulerConfig($schedulerId, $adminId, $configParam, $configValue, $configParamPart = null, $cause = null, KalturaControlPanelCommand $reference)
	{
		$resultObject = $this->client->batchcontrol->setSchedulerConfig($schedulerId, $adminId, $configParam, $configValue, $configParamPart, $cause, $reference);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->setWorkerConfig action
	 * @param int $workerId
	 * @param int $adminId
	 * @param string $configParam
	 * @param string $configValue
	 * @param string $configParamPart
	 * @param string $cause
	 * @param KalturaControlPanelCommand $reference
	 * @dataProvider provideData
	 */
	public function testSetWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart = null, $cause = null, KalturaControlPanelCommand $reference)
	{
		$resultObject = $this->client->batchcontrol->setWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart, $cause, $reference);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->setCommandResult action
	 * @param int $commandId
	 * @param KalturaControlPanelCommandStatus $status
	 * @param string $errorDescription
	 * @param KalturaControlPanelCommand $reference
	 * @dataProvider provideData
	 */
	public function testSetCommandResult($commandId, $status, $errorDescription = null, KalturaControlPanelCommand $reference)
	{
		$resultObject = $this->client->batchcontrol->setCommandResult($commandId, $status, $errorDescription, $reference);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->listCommands action
	 * @param KalturaControlPanelCommandFilter $filter
	 * @param KalturaFilterPager $pager
	 * @param KalturaControlPanelCommandListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListCommands(KalturaControlPanelCommandFilter $filter = null, KalturaFilterPager $pager = null, KalturaControlPanelCommandListResponse $reference)
	{
		$resultObject = $this->client->batchcontrol->listCommands($filter, $pager, $reference);
		$this->assertType('KalturaControlPanelCommandListResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->getCommand action
	 * @param int $commandId
	 * @param KalturaControlPanelCommand $reference
	 * @dataProvider provideData
	 */
	public function testGetCommand($commandId, KalturaControlPanelCommand $reference)
	{
		$resultObject = $this->client->batchcontrol->getCommand($commandId, $reference);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->listSchedulers action
	 * @param KalturaSchedulerListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListSchedulers(KalturaSchedulerListResponse $reference)
	{
		$resultObject = $this->client->batchcontrol->listSchedulers($reference);
		$this->assertType('KalturaSchedulerListResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->listWorkers action
	 * @param KalturaSchedulerWorkerListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListWorkers(KalturaSchedulerWorkerListResponse $reference)
	{
		$resultObject = $this->client->batchcontrol->listWorkers($reference);
		$this->assertType('KalturaSchedulerWorkerListResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests batchcontrol->getFullStatus action
	 * @param KalturaControlPanelCommand $reference
	 * @dataProvider provideData
	 */
	public function testGetFullStatus(KalturaControlPanelCommand $reference)
	{
		$resultObject = $this->client->batchcontrol->getFullStatus($reference);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
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
