<?php
require_once("bootstrap.php");

/**
 * Will schedual execution of external commands
 * Copied base functionality from KScheduler
 *
 * @package Scheduler
 */
class KGenericScheduler
{
	private $enableDebug = true;
	
	/**
	 * @var KSchedulerConfig
	 */
	private $schedulerConfig = null;
	
	private $configFileName = null;
	
	private $keepRunning = true;
	
	private $logDir = "c:/web/kaltura/log";
	private $phpPath = null;
	private $maxExecutionTime;
	private $statusInterval;
	private $nextStatusTime = 0;
	
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
	 * Stores start commands that received from the control panel 
	 * @var array
	 */
	private $startedRemotely = array();
	
	/**
	 * Stores stop commands that received from the control panel 
	 * @var array
	 */
	private $stoppedRemotely = array();
	
	/**
	 * @param string $phpPath
	 * @param string $configFileName
	 */
	public function __construct($phpPath, $configFileName)
	{
		$this->phpPath = $phpPath;
		$this->configFileName = $configFileName;
		$this->loadConfig();
	}
	
	public function __destruct()
	{
		KalturaLog::debug("__destruct()");
		$this->_cleanup();
	}
	
	private function initAllWorkers()
	{
		$taskConfigs = $this->schedulerConfig->getTaskConfigList();
		
		foreach($taskConfigs as $taskConfig)
		{
			if(!$taskConfig->type)
				continue;
			
			if(!$taskConfig->enable)
				$shouldRun = false;
			
			$tmpConfig = clone $taskConfig;
			$tmpConfig->setInitOnly(true);
			
			KalturaLog::info('Initilizing ' . $tmpConfig->name);
			$tasksetPath = $this->schedulerConfig->getTasksetPath();
			$proc = new KProcessWrapper(0, $this->logDir, $this->phpPath, $tasksetPath, $tmpConfig);
			sleep(1);
		}
	}
	
	private function cleanQueueFiltersDir()
	{
		$dirPath = $this->schedulerConfig->getQueueFiltersDir();
		
		if (!is_dir($dirPath))
			return;

		$dh = opendir($dirPath);
	    if(!$dh) 
			return;
			
        while (($file = readdir($dh)) !== false) 
        {
        	if($file == '.' || $file == '..')
        		continue;
        		
        	if(!preg_match('/.\.flt$/', $file))
        		continue;
        		
        	$filePath = "$dirPath/$file";
        	if(filetype($filePath) == 'dir')
        		continue;
        		
        	@unlink($filePath);
        }
        closedir($dh);
	}
	
	private function loadConfig()
	{
		$firstLoad = true;
		if(!is_null($this->schedulerConfig))
		{
			$firstLoad = false;
			
			// check if the helper updated the config file
			clearstatcache();
			$current_file_time = filemtime($this->configFileName);
			if($current_file_time <= $this->schedulerConfig->getFileTimestamp())
				return;
				
			sleep(2); // make sure the file finsied to be written
		}
		
		$this->schedulerConfig = new KSchedulerConfig($this->configFileName);
		date_default_timezone_set($this->schedulerConfig->getTimezone());
		
		$this->cleanQueueFiltersDir();
		$this->queueSizes = array();
		
		if($firstLoad)
			KalturaLog::info(file_get_contents('VERSION.txt'));
			
		KalturaLog::info("Loading configuration file at: " . date('Y-m-d H:i'));
		
		$configItems = $this->createConfigItem($this->schedulerConfig->getScheduler()->toArray());
		$taskConfigs = $this->schedulerConfig->getTaskConfigList();
		foreach($taskConfigs as $taskConfig)
		{
			if(is_null($taskConfig->type)) // is the scheduler itself
				continue;
				
			$vars = get_object_vars($taskConfig);
			$subConfigItems = $this->createConfigItem($vars, $taskConfig->id, $taskConfig->name);
			$configItems = array_merge($configItems, $subConfigItems);
		}
		KalturaLog::info("sending configuration to the server");
		KScheduleHelperManager::saveConfigItems($this->schedulerConfig->getConfigItemsFilePath(), $configItems);
		
		$this->logDir = $this->schedulerConfig->getLogDir();
		$this->maxExecutionTime = $this->schedulerConfig->getMaxExecutionTime();
		$this->statusInterval = $this->schedulerConfig->getStatusInterval();
				
		set_time_limit($this->maxExecutionTime);
		
		$this->initAllWorkers();
	}
	
	public function sendStatusNow()
	{
		$this->nextStatusTime = 0;
	}
	
	public function run()
	{
		KalturaLog::debug("run()");
		$startTime = time();
		
		while($this->keepRunning)
		{
			$this->loadConfig();
			$this->loadCommands();
		
			$fullCycle = false;
			if($this->nextStatusTime < time())
			{
				$fullCycle = true;
				
				$this->nextStatusTime = time() + $this->statusInterval;
				KalturaLog::debug("Next Status Time: " . date('H:i:s', $this->nextStatusTime));
			}
			
			$statuses = array();
			$taskConfigs = $this->schedulerConfig->getTaskConfigList();
			
			foreach($taskConfigs as $taskConfig)
			{
				if(!$taskConfig->type)
					continue;
				
				$runningTasksCount = $this->numberOfRunningTasks($taskConfig->name);
				$statuses[] = $this->createStatus($taskConfig, KalturaSchedulerStatusType::RUNNING_BATCHES_COUNT, $runningTasksCount);
				
				$shouldRun = true;
				
				if(!$taskConfig->enable)
					$shouldRun = false;
				
				if(!$taskConfig->autoStart && !isset($this->startedRemotely[$taskConfig->name]))
					$shouldRun = false;
				
				if(isset($this->stoppedRemotely[$taskConfig->name]))
					$shouldRun = false;
				
				if($fullCycle)
					$statuses[] = $this->createStatus($taskConfig, KalturaSchedulerStatusType::RUNNING_BATCHES_IS_RUNNING, ($shouldRun ? 1 : 0));
					
				if($shouldRun && $this->shouldExecute($taskConfig))
					$this->spawn($taskConfig);
			}
			
			if($fullCycle)
			{
				$statuses[] = $this->createSchedulerStatus(KalturaSchedulerStatusType::RUNNING_BATCHES_IS_RUNNING, 1);
				KScheduleHelperManager::saveStatuses($this->schedulerConfig->getStatusFilePath(), $statuses);
			}
			
			$runningBatches = KScheduleHelperManager::loadRunningBatches($this->schedulerConfig->getCommandsDir());
			foreach($this->runningTasks as $taskName => &$tasks)
			{
				if(! count($tasks))
					continue;
				
				foreach($tasks as $index => &$proc)
				{
					if($proc->isRunning())
					{
						if(isset($runningBatches[$proc->getName()][$proc->getIndex()]))
							unset($runningBatches[$proc->getName()][$proc->getIndex()]);
							
						continue;
					}
						
					$proc->_cleanup();
					unset($tasks[$index]);
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
		die();
	}
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 * @return boolean
	 */
	private function shouldExecute(KSchedularTaskConfig $taskConfig)
	{
		$runningBatches = $this->numberOfRunningTasks($taskConfig->name);
		//KalturaLog::debug("[$runningBatches] of tasks [{$taskConfig->name}] can reach [{$taskConfig->maxInstances}]");
		
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
			
		$lastExecution = $this->getLastExecutionTime($taskConfig->name);
		$nextExecution = $lastExecution + $taskConfig->sleepBetweenStopStart;
		if($nextExecution < time())
			return true;
			
		return false;
	}
	
	private function getLastExecutionTime($taskName)
	{
		if(!isset($this->lastRunTime[$taskName]))
			return 0;
			
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
		KalturaLog::debug("_cleanup()");
		
		foreach($this->runningTasks as $taskName => &$tasks)
		{
			if(!count($tasks))
				continue;
				
			foreach($tasks as $index => &$proc)
			{
				$proc->_cleanup();
			}
		}
		
		$this->runningTasks = array();
	}
	
	private function loadCommands()
	{
		$commands = KScheduleHelperManager::loadCommands($this->schedulerConfig->getCommandsDir());
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
			elseif($command instanceof KalturaSchedulerConfig)
			{
				$command_results[] = $this->handleConfig($command);
			}
			elseif($command instanceof KalturaControlPanelCommand)
			{
				$command_results[] = $this->handleCommand($command);
			}
			else
			{
				KalturaLog::err("command of type " . get_class($command) . " could not be handled");
			}
		}
		
		$cnt = count($command_results);
		if($cnt)
		{
			$path = $this->schedulerConfig->getCommandResultsFilePath();
			KalturaLog::info("Sending $cnt command results to the server [$path]");
			KScheduleHelperManager::saveCommands($path, $command_results);
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
			
			if($size && $size != $oldSize)
				KalturaLog::info("Worker $taskConfig->name, queue size: $size");
				
			return;
		}
		KalturaLog::err("Worker id not found [$workerId]");
	}
	
	/***
	 * handleCommand
	 * @param KalturaSchedulerConfig $command
	 * @return KalturaSchedulerConfig
	 */
	private function handleConfig(KalturaSchedulerConfig $config)
	{
		KalturaLog::info("Save $config->variable [$config->variablePart] attribute to $config->value for worker $config->workerName");
		$success = $this->schedulerConfig->saveConfig($config->variable, $config->value, $config->workerName, $config->variablePart);
		
		if($success)
		{
			$config->commandStatus = KalturaControlPanelCommandStatus::DONE;
		}
		else
		{
			KalturaLog::err("Failed to save $config->variable [$config->variablePart] attribute to $config->value for worker $config->workerName");
			$config->commandStatus = KalturaControlPanelCommandStatus::FAILED;
		}
		
		return $config;
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
			case KalturaControlPanelCommandType::START:
				
				switch(intval($command->targetType))
				{
					case KalturaControlPanelCommandTargetType::JOB_TYPE:
						$success = $this->startByType($command->workerName, $description);
						break;
						
					case KalturaControlPanelCommandTargetType::JOB:
						$success = $this->startById($command->workerConfiguredId, $description);
						break;
						
					default:
						$description = "Target type [$command->targetType] not supported for start command";
						break;
				}
				break;
				
			case KalturaControlPanelCommandType::STOP:
				
				switch(intval($command->targetType))
				{
					case KalturaControlPanelCommandTargetType::SCHEDULER:
						KalturaLog::info("Scheduler stopping...");
						$this->keepRunning = false;
						$success = true;
						break;
						
					case KalturaControlPanelCommandTargetType::JOB_TYPE:
						$success = $this->stopByType($command->workerName, $description);
						break;
						
					case KalturaControlPanelCommandTargetType::JOB:
						$success = $this->stopById($command->workerConfiguredId, $description);
						break;
						
					default:
						$description = "Target type [$command->targetType] not supported for stop command";
						break;
				}
				break;
				
			case KalturaControlPanelCommandType::KILL:
				
				if(intval($command->targetType) != KalturaControlPanelCommandTargetType::BATCH)
				{
					$description = 'Target type not supported for kill command';
					break;
				}
				$success = $this->killBatch($command->workerName, $command->batchIndex, $description);
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
	
	private function startById($id, &$description)
	{
		foreach($this->schedulerConfig->getTaskConfigList() as $taskConfig)
			if($taskConfig->id == $id)
				return $this->startByName($taskConfig->name, $description);
	}
	
	private function startByName($name, &$description)
	{
		$this->startedRemotely[$name] = true;
		if(isset($this->stoppedRemotely[$name]))
			unset($this->stoppedRemotely[$name]);
			
		// check if the job exists on this scheduler
		foreach($this->schedulerConfig->getTaskConfigList() as $taskConfig)
		{
			if($taskConfig->name == $name)
			{
				KalturaLog::info("$name started");
				return true;
			}
		}
				
		$description = "Could not find a job named $name";
		return false;
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
		$proc->_cleanup();
		
		KalturaLog::info("$name [$batchIndex] killed");
		
		return true;
	}
	
	private function stopById($id, &$description)
	{
		foreach($this->schedulerConfig->getTaskConfigList() as $taskConfig)
			if($taskConfig->id == $id)
				return $this->stopByName($taskConfig->name, $description);
	}
	
	private function stopByName($name, &$description)
	{
		$this->stoppedRemotely[$name] = true;
		if(isset($this->startedRemotely[$name]))
			unset($this->startedRemotely[$name]);
			
		// check if the job is running on this scheduler
		if(isset($this->runningTasks[$name]))
		{
			KalturaLog::info("$name stoped");
			return true;
		}
			
		$description = "Could not find a job named $name";
		return false;
	}
	
	private function startByType($type, &$description)
	{
		$found_any = false;
		
		foreach($this->schedulerConfig->getTaskConfigList() as $taskConfig)
		{
			if($taskConfig->type == $type)
			{
				$found_any = true;
				$this->startByName($taskConfig->name);
			}
		}
		
		if(!$found_any)
			$description = "Could not find any job of type $type";
			
		return $found_any;
	}
	
	private function stopByType($type, &$description)
	{
		$found_any = false;
		
		foreach($this->schedulerConfig->getTaskConfigList() as $taskConfig)
		{
			if($taskConfig->type == $type)
			{
				$found_any = true;
				$this->stopByName($taskConfig->name);
			}
		}
		
		if(!$found_any)
			$description = "Could not find any job of type $type";
		
		return $found_any;
	}
}

?>