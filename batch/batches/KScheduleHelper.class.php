<?php
require_once("bootstrap.php");
/**
 * Will import a single URL and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	parse URL	(youTube is a special case) 
 * 		fetch heraders (to calculate the size of the file)
 * 		fetch file (update the job's progress - 100% is when the whole file as appeared in the header)
 * 		move the file to the archive
 * 		set the entry's new status and file details  (check if FLV) 
 *
 * @package Scheduler
 */
class KScheduleHelper extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::SCHEDULER_HELPER;
	}
	
	protected function init()
	{
		
	}
	
	/**
	 * @param int $jobId
	 * @param KalturaBatchJob $job
	 */
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job){}
	
	/**
	 * @param KalturaBatchJob $job
	 */
	protected function freeExclusiveJob(KalturaBatchJob $job){}
	
	public function run()
	{
		KalturaLog::info("Schedule helper batch is running");
		
		try
		{
			$systemReady = $this->kClient->system->ping();
			if (!$systemReady) {
				KalturaLog::err("System is not yet ready - ping failed");
				return;
			}
		}
		catch (KalturaClientException $e)
		{
			KalturaLog::err("System is not yet ready - ping failed");
			return;
		}
		
		$scheduler = new KalturaScheduler();
		$scheduler->configuredId = $this->getSchedulerId();
		$scheduler->name = $this->getSchedulerName();
		$scheduler->host = $this->getConfigHostName();
		
		// if the hostName is not set in the config - search the differnt env params 
		if ( ! $scheduler->host )
		{
			if(isset($_SERVER['COMPUTERNAME']))
				$scheduler->host = $_SERVER['COMPUTERNAME'];
			elseif(isset($_SERVER['HOSTNAME']))
				$scheduler->host = $_SERVER['HOSTNAME'];
			elseif(function_exists('gethostname'))
				$scheduler->host = gethostname();
			else
				$scheduler->host = 'unknown';
		}
		// get command results from the scheduler
		$commandResults = KScheduleHelperManager::loadCommandsFile($this->taskConfig->params->commandResultsFilePath);
		KalturaLog::info(count($commandResults) . " command results returned from the scheduler");
		if(count($commandResults))
			$this->sendCommandResults($commandResults);
		
		if($this->taskConfig->params->configItemsFilePath)
		{
			// get config from the schduler
			$configItems = KScheduleHelperManager::loadConfigItems($this->taskConfig->params->configItemsFilePath);
			if(count($configItems))
			{
				KalturaLog::info(count($configItems) . " config records sent from the scheduler");
				$this->sendConfigItems($scheduler, $configItems);
			}
		}
		
		$filters = KScheduleHelperManager::loadFilters($this->taskConfig->getQueueFiltersDir());
		KalturaLog::info(count($filters) . " filter records found for the scheduler");
		
		$statuses = array();
		if($this->taskConfig->params->statusFilePath)
		{
			// get status from the schduler
			$statuses = KScheduleHelperManager::loadStatuses($this->taskConfig->params->statusFilePath);
			KalturaLog::info(count($statuses) . " status records sent from the scheduler");
		}
		
		// send status to the server
		$statusResponse = $this->kClient->batchcontrol->reportStatus($scheduler, (array)$statuses, (array)$filters);
		KalturaLog::info(count($statusResponse->queuesStatus) . " queue status records returned from the server");
		KalturaLog::info(count($statusResponse->controlPanelCommands) . " control commands returned from the server");
		KalturaLog::info(count($statusResponse->schedulerConfigs) . " config items returned from the server");
		
		// send commands to the scheduler		
		$commands = array_merge($statusResponse->queuesStatus, $statusResponse->schedulerConfigs, $statusResponse->controlPanelCommands);
		KalturaLog::info(count($commands) . " commands sent to scheduler");
		$this->saveSchedulerCommands($commands);
	}
	
	/**
	 * @param KalturaScheduler $scheduler
	 * @param array<KalturaSchedulerConfig> $configItems
	 */
	private function sendConfigItems(KalturaScheduler $scheduler, array $configItems)
	{
		KalturaLog::debug("sendConfigItems(" . count($configItems) . ")");
		
		$configItemsArr = array_chunk($configItems, 100);
		
		foreach($configItemsArr as $configItems)
		{
			$this->kClient->startMultiRequest();
			
			foreach($configItems as $configItem)
			{
				if($configItem instanceof KalturaSchedulerConfig)
				{
					if(is_null($configItem->value))
						$configItem->value = '';
						
					$this->kClient->batchcontrol->configLoaded($scheduler, $configItem->variable, $configItem->value, $configItem->variablePart, $configItem->workerConfiguredId, $configItem->workerName);
				}
			}
			
			$this->kClient->doMultiRequest();
		}
	}
	
	/**
	 * @param array $commandResults
	 */
	private function sendCommandResults(array $commandResults)
	{
		KalturaLog::debug("sendCommandResults(" . count($commandResults) . ")");
		
		$this->kClient->startMultiRequest();
		
		foreach($commandResults as $commandResult)
		{
			if($commandResult instanceof KalturaSchedulerConfig)
			{
				KalturaLog::info("Handling config id[$commandResult->id], with command id[$commandResult->commandId]");
				$this->kClient->batchcontrol->setCommandResult($commandResult->commandId, $commandResult->commandStatus);
			}
			elseif($commandResult instanceof KalturaControlPanelCommand)
			{
				KalturaLog::info("Handling command id[$commandResult->id]");
				$this->kClient->batchcontrol->setCommandResult($commandResult->id, $commandResult->status, $commandResult->errorDescription);
			}
			else
			{
				KalturaLog::err(get_class($commandResult) . " object sent from scheduler");
			}
		}
		
		$this->kClient->doMultiRequest();
	}
}
?>