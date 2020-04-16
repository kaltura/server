<?php
/**
 * @package Scheduler
 */

/**
 * Will import a single URL and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	parse URL	(youTube is a special case) 
 * 		fetch heraders (to calculate the size of the file)
 * 		fetch file 
 * 		move the file to the archive
 * 		set the entry's new status and file details  (check if FLV) 
 *
 * @package Scheduler
 */
class KScheduleHelper extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::SCHEDULER_HELPER;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	 */
	public function run($jobs = null)
	{
		try
		{
			$systemReady = self::$kClient->system->ping();
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
		$scheduler->host = KSchedulerConfig::getHostname();
		
		// get command results from the scheduler
		$commandResults = KScheduleHelperManager::loadResultsCommandsFile();
		KalturaLog::info(is_array($commandResults) ? count($commandResults) : 0 . " command results returned from the scheduler");
		if(is_array($commandResults) && count($commandResults))
			$this->sendCommandResults($commandResults);
		
		// get config from the schduler
		$configItems = KScheduleHelperManager::loadConfigItems();
		if(is_array($configItems) && count($configItems))
		{
			KalturaLog::info(count($configItems) . " config records sent from the scheduler");
			$this->sendConfigItems($scheduler, $configItems);
		}
		
		$filters = KScheduleHelperManager::loadFilters();
		KalturaLog::info(is_array($filters) ? count($filters) : 0 . " filter records found for the scheduler");
		
		// get status from the schduler
		$statuses = KScheduleHelperManager::loadStatuses();
		KalturaLog::info(is_array($statuses) ? count($statuses) : 0 . " status records sent from the scheduler");
		
		// send status to the server
		$statusResponse = self::$kClient->batchcontrol->reportStatus($scheduler, (array)$statuses, (array)$filters);
		KalturaLog::info(count($statusResponse->queuesStatus) . " queue status records returned from the server");
		KalturaLog::info(count($statusResponse->controlPanelCommands) . " control commands returned from the server");
		KalturaLog::info(count($statusResponse->schedulerConfigs) . " config items returned from the server");
		
		// send commands to the scheduler		
		$commands = array_merge($statusResponse->queuesStatus, $statusResponse->schedulerConfigs, $statusResponse->controlPanelCommands);
		KalturaLog::info(is_array($commands) ? count($commands) : 0 . " commands sent to scheduler");
		$this->saveSchedulerCommands($commands);
	}
	
	/**
	 * @param KalturaScheduler $scheduler
	 * @param array<KalturaSchedulerConfig> $configItems
	 */
	private function sendConfigItems(KalturaScheduler $scheduler, array $configItems)
	{
		$uniqItems = array();
		foreach ($configItems as $item)
		{
			/* @var $item KalturaSchedulerConfig*/
			if($item instanceof KalturaSchedulerConfig)
			{
				$uniqItems[$item->workerConfiguredId] = $item;
			}
		}

		$configItemsArr = array_chunk($uniqItems, 100);
		foreach($configItemsArr as $configItems)
		{
			self::$kClient->startMultiRequest();
			
			foreach($configItems as $configItem)
			{
					if(is_null($configItem->value))
						$configItem->value = '';
						
					self::$kClient->batchcontrol->configLoaded($scheduler, $configItem->variable, $configItem->value, $configItem->variablePart, $configItem->workerConfiguredId, $configItem->workerName);
			}
			
			self::$kClient->doMultiRequest();
		}
	}
	
	/**
	 * @param array $commandResults
	 */
	private function sendCommandResults(array $commandResults)
	{
		self::$kClient->startMultiRequest();
		
		foreach($commandResults as $commandResult)
		{
			if($commandResult instanceof KalturaSchedulerConfig)
			{
				KalturaLog::info("Handling config id[$commandResult->id], with command id[$commandResult->commandId]");
				self::$kClient->batchcontrol->setCommandResult($commandResult->commandId, $commandResult->commandStatus);
			}
			elseif($commandResult instanceof KalturaControlPanelCommand)
			{
				KalturaLog::info("Handling command id[$commandResult->id]");
				self::$kClient->batchcontrol->setCommandResult($commandResult->id, $commandResult->status, $commandResult->errorDescription);
			}
			else
			{
				KalturaLog::err(get_class($commandResult) . " object sent from scheduler");
			}
		}
		
		self::$kClient->doMultiRequest();
	}
}
