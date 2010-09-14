<?php
require_once("bootstrap.php");

/**
 * Will schedual execution of external commands
 * Copied base functionality from KScheduler
 *
 * @package Scheduler
 * @subpackage Debug
 */
class KGenericDebuger
{
	private $enableDebug = true;
	private $schedulerConfig = null;
	private $configFileName = null;
	
	private $logDir = "/web/kaltura/log";
	
	/**
	 * @param string $configFileName
	 */
	public function __construct($configFileName)
	{
		$this->debug(__LINE__, "__construct($configFileName)");
		$this->configFileName = $configFileName;
		$this->loadConfig();
	}
	
	private function loadConfig()
	{
		$this->debug(__LINE__, "loadConfig()");
		if(!is_null($this->schedulerConfig))
		{
			// check if the helper updated the config file
			$current_file_time = filemtime($this->configFileName);
			if($current_file_time <= $this->schedulerConfig->getFileTimestamp())
				return;
		}
		
		$this->schedulerConfig = new KSchedulerConfig($this->configFileName);
		
		$this->logDir = $this->schedulerConfig->getLogDir();
	}
	
	/**
	 * @param string $jobName
	 */
	public function run($jobName)
	{
		$this->debug(__LINE__, "run($jobName)");
		
		$taskConfigs = $this->schedulerConfig->getTaskConfigList();
		$this->debug(__LINE__, "taskConfigs: " . count($taskConfigs));
		
		foreach($taskConfigs as $taskConfig)
		{
			if($taskConfig->name == $jobName)
				$this->exeJob($taskConfig);
		}
		
		$this->debug(__LINE__, "-- Done --");
		die();
	}
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	private function exeJob(KSchedularTaskConfig $taskConfig)
	{
		$this->debug(__LINE__, "exeJob($taskConfig->name)");
		
		$taskConfig->setTaskIndex(1);
		//$taskConfig->setInitOnly(true);
		
		$instance = new $taskConfig->type($taskConfig);
		$instance->run(); 
		$instance->done();
	}
	
	/**
	 * @param int $line
	 * @param string $text
	 */
	private function debug($line, $text)
	{
		if($this->enableDebug)
			echo "line $line: $text\n";
	}
}

?>