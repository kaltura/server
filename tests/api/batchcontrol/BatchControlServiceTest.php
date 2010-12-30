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
	 * @dataProvider provideData
	 */
	public function testReportStatus(KalturaScheduler $scheduler, KalturaSchedulerStatusArray $schedulerStatuses, KalturaWorkerQueueFilterArray $workerQueueFilters)
	{
		$resultObject = $this->client->batchcontrol->reportStatus($scheduler, $schedulerStatuses, $workerQueueFilters);
		$this->assertType('KalturaSchedulerStatusResponse', $resultObject);
	}

	/**
	 * Tests batchcontrol->configLoaded action
	 * @param KalturaScheduler $scheduler
	 * @param string $configParam
	 * @param string $configValue
	 * @param string $configParamPart
	 * @param int $workerConfigId
	 * @param string $workerName
	 * @dataProvider provideData
	 */
	public function testConfigLoaded(KalturaScheduler $scheduler, $configParam, $configValue, $configParamPart = null, $workerConfigId = null, $workerName = null)
	{
		$resultObject = $this->client->batchcontrol->configLoaded($scheduler, $configParam, $configValue, $configParamPart, $workerConfigId, $workerName);
		$this->assertType('KalturaSchedulerConfig', $resultObject);
	}

	/**
	 * Tests batchcontrol->stopScheduler action
	 * @param int $schedulerId
	 * @param int $adminId
	 * @param string $cause
	 * @dataProvider provideData
	 */
	public function testStopScheduler($schedulerId, $adminId, $cause)
	{
		$resultObject = $this->client->batchcontrol->stopScheduler($schedulerId, $adminId, $cause);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
	}

	/**
	 * Tests batchcontrol->stopWorker action
	 * @param int $workerId
	 * @param int $adminId
	 * @param string $cause
	 * @dataProvider provideData
	 */
	public function testStopWorker($workerId, $adminId, $cause)
	{
		$resultObject = $this->client->batchcontrol->stopWorker($workerId, $adminId, $cause);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
	}

	/**
	 * Tests batchcontrol->kill action
	 * @param int $workerId
	 * @param int $batchIndex
	 * @param int $adminId
	 * @param string $cause
	 * @dataProvider provideData
	 */
	public function testKill($workerId, $batchIndex, $adminId, $cause)
	{
		$resultObject = $this->client->batchcontrol->kill($workerId, $batchIndex, $adminId, $cause);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
	}

	/**
	 * Tests batchcontrol->startWorker action
	 * @param int $workerId
	 * @param int $adminId
	 * @param string $cause
	 * @dataProvider provideData
	 */
	public function testStartWorker($workerId, $adminId, $cause = null)
	{
		$resultObject = $this->client->batchcontrol->startWorker($workerId, $adminId, $cause);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
	}

	/**
	 * Tests batchcontrol->setSchedulerConfig action
	 * @param int $schedulerId
	 * @param int $adminId
	 * @param string $configParam
	 * @param string $configValue
	 * @param string $configParamPart
	 * @param string $cause
	 * @dataProvider provideData
	 */
	public function testSetSchedulerConfig($schedulerId, $adminId, $configParam, $configValue, $configParamPart = null, $cause = null)
	{
		$resultObject = $this->client->batchcontrol->setSchedulerConfig($schedulerId, $adminId, $configParam, $configValue, $configParamPart, $cause);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
	}

	/**
	 * Tests batchcontrol->setWorkerConfig action
	 * @param int $workerId
	 * @param int $adminId
	 * @param string $configParam
	 * @param string $configValue
	 * @param string $configParamPart
	 * @param string $cause
	 * @dataProvider provideData
	 */
	public function testSetWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart = null, $cause = null)
	{
		$resultObject = $this->client->batchcontrol->setWorkerConfig($workerId, $adminId, $configParam, $configValue, $configParamPart, $cause);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
	}

	/**
	 * Tests batchcontrol->setCommandResult action
	 * @param int $commandId
	 * @param KalturaControlPanelCommandStatus $status
	 * @param string $errorDescription
	 * @dataProvider provideData
	 */
	public function testSetCommandResult($commandId, KalturaControlPanelCommandStatus $status, $errorDescription = null)
	{
		$resultObject = $this->client->batchcontrol->setCommandResult($commandId, $status, $errorDescription);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
	}

	/**
	 * Tests batchcontrol->listCommands action
	 * @param KalturaControlPanelCommandFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testListCommands(KalturaControlPanelCommandFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->batchcontrol->listCommands($filter, $pager);
		$this->assertType('KalturaControlPanelCommandListResponse', $resultObject);
	}

	/**
	 * Tests batchcontrol->getCommand action
	 * @param int $commandId
	 * @dataProvider provideData
	 */
	public function testGetCommand($commandId)
	{
		$resultObject = $this->client->batchcontrol->getCommand($commandId);
		$this->assertType('KalturaControlPanelCommand', $resultObject);
	}

	/**
	 * Tests batchcontrol->listSchedulers action
	 * @dataProvider provideData
	 */
	public function testListSchedulers()
	{
		$resultObject = $this->client->batchcontrol->listSchedulers();
		$this->assertType('KalturaSchedulerListResponse', $resultObject);
	}

	/**
	 * Tests batchcontrol->listWorkers action
	 * @dataProvider provideData
	 */
	public function testListWorkers()
	{
		$resultObject = $this->client->batchcontrol->listWorkers();
		$this->assertType('KalturaSchedulerWorkerListResponse', $resultObject);
	}

	/**
	 * Tests batchcontrol->getFullStatus action
	 * @dataProvider provideData
	 */
	public function testGetFullStatus()
	{
		$resultObject = $this->client->batchcontrol->getFullStatus();
		$this->assertType('KalturaControlPanelCommand', $resultObject);
	}

}
