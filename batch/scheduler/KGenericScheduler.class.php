<?php
/**
 * Will schedual execution of external commands
 * Copied base functionality from KScheduler
 *
 * @package Scheduler
 */
class KGenericScheduler
{
	/**
	 * @var bool
	 */
	private $enableDebug = true;
	
	/**
	 * @var KSchedulerConfig
	 */
	private $schedulerConfig = null;
	
	/**
	 * @var bool
	 */
	private $keepRunning = true;
	
	private $logDir = "c:/web/kaltura/log";
	private $phpPath = null;
	private $statusInterval;
	private $schedulerStatusInterval;
	private $nextStatusTime = 0;
	private $nextSchedulerStatusTime = 0;
	
	/**
	 * Stores all groups of tasks, the index is the task type
	 * @var array is an array of array
	 */
	private $runningTasks = array();
	
	/**
	 * Stores the last execution time of each task type
	 * @var array
	 */
	private $lastRunTime = array();
	
	/**
	 * Stores the last executed index of each task type
	 * @var array
	 */
	private $nextRunIndex = array();
	
	/**
	 * Stores the size of the queue on the server of each task type
	 * @var array
	 */
	private $queueSizes = array();
	
	/**
	 * @param string $phpPath
	 * @param string $configFileName
	 */
	public function __construct($phpPath, $configFileName)
	{
		$this->phpPath = $phpPath;
		$this->loadConfig($configFileName);
	}
	
	public function __destruct()
	{
		$this->_cleanup();
	}
	
	private function initAllWorkers()
	{
		$taskConfigs = $this->schedulerConfig->getTaskConfigList();
		
		foreach($taskConfigs as $taskConfig)
		{
			if(!$taskConfig->type)
				continue;
			
			$tmpConfig = clone $taskConfig;
			$tmpConfig->setInitOnly(true);
			
			KalturaLog::info('Initilizing ' . $tmpConfig->name);
			$tasksetPath = $this->schedulerConfig->getTasksetPath();
			$proc = new KProcessWrapper(0, $this->logDir, $this->phpPath, $tasksetPath, $tmpConfig);
			sleep(1);
		}
	}
	
	private function loadConfig($configFileName = null)
	{
		$firstLoad = is_null($this->schedulerConfig);
		if($firstLoad)
		{
			$this->schedulerConfig = new KSchedulerConfig($configFileName);
			date_default_timezone_set($this->schedulerConfig->getTimezone());
			KalturaLog::info(file_get_contents('VERSION.txt'));
		}
		else
		{	
			if(!$this->schedulerConfig->reloadRequired())
				return;
				
			sleep(2); // make sure the file finsied to be written
			$this->schedulerConfig->load();
		}
		
		KScheduleHelperManager::clearFilters();
		$this->queueSizes = array();
			
		KalturaLog::info("Loading configuration file at: " . date('Y-m-d H:i'));
		
		$configItems = $this->createConfigItem($this->schedulerConfig->toArray());
		$taskConfigs = $this->schedulerConfig->getTaskConfigList();
		
		$this->logDir = $this->schedulerConfig->getLogDir();
		$this->statusInterval = $this->schedulerConfig->getStatusInterval();
		$this->schedulerStatusInterval = $this->schedulerConfig->getSchedulerStatusInterval();
		KDwhClient::setEnabled($this->schedulerConfig->getDwhEnabled());
		KDwhClient::setFileName($this->schedulerConfig->getDwhPath());
		
		$taskConfigsValidations = array();
		foreach($taskConfigs as $taskConfig)
		{
			/* @var $taskConfig KSchedularTaskConfig */
			
			if(is_null($taskConfig->type)) // is the scheduler itself
				continue;
				
			if(isset($taskConfigsValidations[$taskConfig->id]))
			{
				KalturaLog::err("Duplicated worker id [$taskConfig->id] in worker names [$taskConfig->name] and [" . $taskConfigsValidations[$taskConfig->id] . "]");
				$this->keepRunning = false;
				return;
			}
			
			if(in_array($taskConfig->name, $taskConfigsValidations))
			{
				KalturaLog::err("Duplicated worker name [$taskConfig->name] in worker ids [$taskConfig->id] and [" . array_search($taskConfig->name, $taskConfigsValidations) . "]");
				$this->keepRunning = false;
				return;
			}
			
			$taskConfigsValidations[$taskConfig->id] = $taskConfig->name;
			$vars = get_object_vars($taskConfig);
			$subConfigItems = $this->createConfigItem($vars, $taskConfig->id, $taskConfig->name);
			$configItems = array_merge($configItems, $subConfigItems);
		}
		KalturaLog::info("sending configuration to the server");
		KScheduleHelperManager::saveConfigItems($configItems);
					
		$this->initAllWorkers();
	}
	
	public function sendStatusNow()
	{
		$this->nextStatusTime = 0;
		$this->nextSchedulerStatusTime = 0;
	}
	
	public function run()
	{
		KalturaLog::debug("Running");
		$startTime = time();
		
		while($this->keepRunning)
		{
			$this->loadConfig();
			$this->loadCommands();
		
			$fullCycle = false;
			$sendSchedulerStatus = false;
			if($this->nextStatusTime < time())
			{
				$fullCycle = true;
				
				$this->nextStatusTime = time() + $this->statusInterval;
				KalturaLog::debug("Next Status Time: " . date('H:i:s', $this->nextStatusTime));
			}
		
			if($this->nextSchedulerStatusTime < time())
			{
				$sendSchedulerStatus = true;
				
				$this->nextSchedulerStatusTime = time() + $this->schedulerStatusInterval;
				KalturaLog::debug("Next Scheduler Status Time: " . date('H:i:s', $this->nextSchedulerStatusTime));
			}
			
			$statuses = array();
			$taskConfigs = $this->schedulerConfig->getTaskConfigList();
			$indexedTaskConfigs = array();
			foreach($taskConfigs as $taskConfig)
			{
				$indexedTaskConfigs[$taskConfig->name] = $taskConfig;
				if(!$taskConfig->type)
					continue;
				
				$runningTasksCount = $this->numberOfRunningTasks($taskConfig->name);
				if($fullCycle)
					$statuses[] = $this->createStatus($taskConfig, KalturaSchedulerStatusType::RUNNING_BATCHES_COUNT, $runningTasksCount);
				
				if($fullCycle)
					$statuses[] = $this->createStatus($taskConfig, KalturaSchedulerStatusType::RUNNING_BATCHES_IS_RUNNING, 1);
					
				if($this->shouldExecute($taskConfig))
					$this->spawn($taskConfig);
			}
			
			if($sendSchedulerStatus)
				$statuses[] = $this->createSchedulerStatus(KalturaSchedulerStatusType::RUNNING_BATCHES_IS_RUNNING, 1);
				
			if(count($statuses))
				KScheduleHelperManager::saveStatuses($statuses);
			
			$runningBatches = KScheduleHelperManager::loadRunningBatches();
			foreach($this->runningTasks as $taskName => &$tasks)
			{
				if(! count($tasks))
					continue;
				
				foreach($tasks as $index => &$proc)
				{
					/* @var $proc KProcessWrapper */
					if($proc->isRunning())
					{
						if(isset($runningBatches[$proc->getName()][$proc->getIndex()]))
						{
							$processId = $runningBatches[$proc->getName()][$proc->getIndex()];
							$proc->setProcessId($processId);
							unset($runningBatches[$proc->getName()][$proc->getIndex()]);
						}
							
						continue;
					}
						
					$proc->_cleanup();
					unset($tasks[$index]);
					
					if(!isset($indexedTaskConfigs[$taskName]))
						continue;
						
					$taskConfig = $indexedTaskConfigs[$taskName];
					self::onRunningInstancesEvent($taskConfig, count($tasks));
				}
			}
			
			foreach($runningBatches as $workerName => $indexes)
			{
				if(!is_array($indexes))
					continue;
					
				$keys = array_keys($indexes);
				$index = intval(reset($keys));
				$this->nextRunIndex[$workerName] = $index;
			}
				
			sleep(1);
		}
		
		KalturaLog::info("-- Done --");
		KalturaLog::debug("ended after [" . (time() - $startTime) . "] seconds");
	}
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 * @return boolean
	 */
	private function shouldExecute(KSchedularTaskConfig $taskConfig)
	{
		$runningBatches = $this->numberOfRunningTasks($taskConfig->name);
		
		if($taskConfig->startForQueueSize)
		{
			if(!isset($this->queueSizes[$taskConfig->id]) || $this->queueSizes[$taskConfig->id] < $taskConfig->startForQueueSize)
				return false;
		}
		
		if($runningBatches >= $taskConfig->maxInstances)
			return false;
			
		if(isset($this->queueSizes[$taskConfig->id]) && $this->queueSizes[$taskConfig->id] > 0)
		{
			$this->queueSizes[$taskConfig->id]--;
			return true;
		}
			
		if($taskConfig->sleepBetweenStopStart)
		{
			$lastExecution = $this->getLastExecutionTime($taskConfig->name);
			$nextExecution = $lastExecution + $taskConfig->sleepBetweenStopStart;
			if($nextExecution < time())
				return true;
		}
			
		return false;
	}
	
	private function getLastExecutionTime($taskName)
	{
		if(!isset($this->lastRunTime[$taskName]))
			$this->lastRunTime[$taskName] = time();
			
		return $this->lastRunTime[$taskName];
	}
	
	private function spawn(KSchedularTaskConfig $taskConfig)
	{
		$taskIndex = $this->getNextAvailableIndex($taskConfig->name, $taskConfig->maxInstances);
		$taskIndex = intval($taskIndex);
		
		$this->lastRunTime[$taskConfig->name] = time();
		$this->nextRunIndex[$taskConfig->name] = $taskIndex + 1;
		
		KalturaLog::info("Executing $taskConfig->name [$taskIndex]");
		$tasksetPath = $this->schedulerConfig->getTasksetPath();
		$proc = new KProcessWrapper($taskIndex, $this->logDir, $this->phpPath, $tasksetPath, clone $taskConfig);
						
		$this->runningTasks[$taskConfig->name][$taskIndex] = &$proc;
		self::onRunningInstancesEvent($taskConfig, count($this->runningTasks[$taskConfig->name]));
	}

	/**
	 * @param array $data
	 * @param int $workerConfiguredId
	 * @param string $workerName
	 * @param string $parentVariable
	 * @return array<KalturaSchedulerConfig>
	 */
	public function createConfigItem(array $data, $workerConfiguredId = null, $workerName = null, $parentVariable = null)
	{
		$configItems = array();
		
		foreach($data as $variable => $value)
		{
			if(is_array($value) || $value instanceof Zend_Config)
			{
				$subData = $value;
				if($value instanceof Zend_Config)
					$subData = $value->toArray();
					
				$subConfigItems = $this->createConfigItem($subData, $workerConfiguredId, $workerName, $variable);
				$configItems = array_merge($configItems, $subConfigItems);
				continue;
			}
			
			$configItem = new KalturaSchedulerConfig();
			$configItem->schedulerConfiguredId = $this->schedulerConfig->getId();
			$configItem->schedulerName = $this->schedulerConfig->getName();
			
			$configItem->workerConfiguredId = $workerConfiguredId;
			$configItem->workerName = $workerName;
			
			if(is_null($parentVariable))
			{
				$configItem->variable = $variable;
			}
			else
			{
				$configItem->variable = $parentVariable;
				$configItem->variablePart = $variable;
			}
			$configItem->value = $value;
			
			$configItems[] = $configItem;
		}
		
		return $configItems;
	}
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 * @param int $type
	 * @param int $value
	 * @return KalturaSchedulerStatus
	 */
	private function createStatus(KSchedularTaskConfig $taskConfig, $type, $value)
	{
		$clazz = $taskConfig->type;
		
		$status = new KalturaSchedulerStatus();
		$status->schedulerConfiguredId = $this->schedulerConfig->getId();
		$status->workerConfiguredId = $taskConfig->id;
		$status->type = $type;
		$status->value = $value;
		eval("\$status->workerType = ${clazz}::getType();");
		
		return $status;
	}
	
	/**
	 * @param int $type
	 * @param int $value
	 * @return KalturaSchedulerStatus
	 */
	private function createSchedulerStatus($type, $value)
	{
		$status = new KalturaSchedulerStatus();
		$status->schedulerConfiguredId = $this->schedulerConfig->getId();
		$status->type = $type;
		$status->value = $value;
		
		return $status;
	}
	
	/**
	 * @param string $name
	 * @return int
	 */
	private function numberOfRunningTasks($name = null)
	{
		if(!$name)
			return (count($this->runningTasks, COUNT_RECURSIVE) - count($this->runningTasks, COUNT_NORMAL));
		
		if(!isset($this->runningTasks[$name]))
			return 0;
		
		return count($this->runningTasks[$name]);
	}
	
	/**
	 * @param string $name
	 * @param int $maxInstances
	 * @return int
	 */
	private function getNextAvailableIndex($name, $maxInstances)
	{
		if(! isset($this->runningTasks[$name]))
			return 0;
		
		$nextIndex = 0;
		if(isset($this->nextRunIndex[$name]))
		{
			$nextIndex = $this->nextRunIndex[$name];
			if($nextIndex >= $maxInstances)
				$nextIndex = 0;
		}
		
		$tasks = $this->runningTasks[$name];
		
		for($index = $nextIndex; $index < $maxInstances; $index++)
			if(!isset($tasks[$index]))
				return $index;
				
		for($index = 0; $index < $nextIndex; $index++)
			if(!isset($tasks[$index]))
				return $index;
	}
	
	private function _cleanup()
	{
		foreach($this->runningTasks as $taskName => &$tasks)
		{
			if(!count($tasks))
				continue;
				
			$taskConfig = null;
			foreach($tasks as $index => &$proc)
			{
				$taskConfig = $proc->taskConfig;
				$proc->_cleanup();
			}
			self::onRunningInstancesEvent($taskConfig, 0);
		}
		
		$this->runningTasks = array();
	}
	
	private function loadCommands()
	{
		$commands = KScheduleHelperManager::loadCommands();
		if(!$commands || !is_array($commands) || !count($commands))
			return;
			
//		KalturaLog::info(count($commands) . " commands found");
			
		$command_results = array();
		foreach($commands as $command)
		{
			if($command instanceof KalturaBatchQueuesStatus)
			{
				$this->handleQueueStatus($command->workerId, $command->size);
			}
			elseif($command instanceof KalturaControlPanelCommand)
			{
				$command_results[] = $this->handleCommand($command);
			}
			else
			{
				KalturaLog::err("command of type " . get_class($command) . " could not be handled");
				$command_results[] = KalturaControlPanelCommandStatus::FAILED;
			}
		}
		
		$cnt = count($command_results);
		if($cnt)
		{
			KalturaLog::info("Sending $cnt command results to the server");
			KScheduleHelperManager::saveCommandsResults($command_results);
		}
	}
	
	private function handleQueueStatus($workerId, $size)
	{
		$taskConfigs = $this->schedulerConfig->getTaskConfigList();
		
		foreach($taskConfigs as $taskConfig)
		{
			if($workerId != $taskConfig->id)
				continue;
				
			$oldSize = 0;
			if(isset($this->queueSizes[$workerId]))
				$oldSize = $this->queueSizes[$workerId];
				
			$this->queueSizes[$workerId] = $size;
			
			if($size != $oldSize)
			{
				self::onQueueEvent($taskConfig, $size);
				
				if($size)
					KalturaLog::info("Worker $taskConfig->name, queue size: $size");
			}
				
			return;
		}
		KalturaLog::err("Worker id not found [$workerId]");
	}
	
	/***
	 * handleCommand
	 * @param KalturaControlPanelCommand $command
	 * @return KalturaControlPanelCommand
	 */
	private function handleCommand(KalturaControlPanelCommand $command)
	{
		KalturaLog::info("Handling command id " . $command->id);
		
		$description = null;
		$success = false;
		
		switch($command->type)
		{
			case KalturaControlPanelCommandType::KILL:
				
				if(intval($command->targetType) != KalturaControlPanelCommandTargetType::SCHEDULER)
				{
					KalturaLog::info("Scheduler stopping...");
					$this->keepRunning = false;
					$success = true;
				}
						
				if(intval($command->targetType) != KalturaControlPanelCommandTargetType::BATCH)
				{
					$description = 'Target type not supported for kill command';
					$success = $this->killBatch($command->workerName, $command->batchIndex, $description);
				}
				break;
				
			default:
				$description = 'Command type not supported';
				break;
		}
		
		if($success)
		{
			$command->status = KalturaControlPanelCommandStatus::DONE;
			$this->sendStatusNow();
		}
		else
		{
			KalturaLog::err("Error handling commnad id $command->id: $description");
			$command->status = KalturaControlPanelCommandStatus::FAILED;
			$command->errorDescription = $description;
		}
		
		return $command;
	}
	
	private function killBatch($name, $batchIndex, $description)
	{
		// check if the job is running on this scheduler
		if(!isset($this->runningTasks[$name]))
		{
			$description = "Could not find a job named $name";
			return false;
		}
	
		// check if the job is running on this scheduler
		if(!isset($this->runningTasks[$name][$batchIndex]))
		{
			$description = "Batch index $batchIndex is not running";
			return true;
		}
		
		$proc = &$this->runningTasks[$name][$batchIndex];
		$taskConfig = $proc->taskConfig;
		$proc->_cleanup();
		
		self::onRunningInstancesEvent($taskConfig, count($this->runningTasks[$name]));
		
		KalturaLog::info("$name [$batchIndex] killed");
		
		return true;
	}
	
	protected function onQueueEvent(KSchedularTaskConfig $taskConfig, $queueSize)
	{
		$event = new KBatchEvent();
		$event->batch_event_type_id = KBatchEvent::EVENT_BATCH_QUEUE;
		$event->value_1 = $queueSize;
		
		self::onEvent($event, $taskConfig);
	}
	
	protected function onRunningInstancesEvent(KSchedularTaskConfig $taskConfig, $runningInstances)
	{
		$event = new KBatchEvent();
		$event->batch_event_type_id = KBatchEvent::EVENT_BATCH_RUNNING;
		$event->value_1 = $runningInstances;
		
		self::onEvent($event, $taskConfig);
	}
	
	protected function onEvent(KBatchEvent $event, KSchedularTaskConfig $taskConfig)
	{
		$event->batch_client_version = "1.0";
		$event->batch_event_time = time();
		
		$event->batch_name = $taskConfig->name;
		$event->section_id = $taskConfig->id;
		$event->batch_type = $taskConfig->type;
		$event->location_id = $this->schedulerConfig->getId();
		$event->host_name = $this->schedulerConfig->getName();
	
		KDwhClient::send($event);
	}
}
