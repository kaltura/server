<?php
/**
 * @package Scheduler
 * 
 */

class KSchedulerConfig
{
	private $configFileName;
	private $config;
	private $configTimestamp;
	private $taskConfigList = array();
	
	/**
	 * @param string $configFileName
	 */
	public function __construct( $configFileName )
	{
		$this->configFileName = str_replace("\\", "/", $configFileName);
		$this->configTimestamp = filemtime(realpath($this->configFileName));
		$this->parseConfgiFile($this->configFileName);
	}
	
	/**
	 * set the valus in the memory and save it to the file
	 * 
	 * @param string $variable
	 * @param string $value
	 * @param string $taskName
	 * @param string $variablePart
	 * @return boolean
	 */
	public function saveConfig($variable, $value, $taskName = null, $variablePart = null)
	{
		$ret = $this->setConfig($variable, $value, $taskName, $variablePart);
		if($ret)
		{
			$this->saveToIniFile($this->configFileName);
			$this->configTimestamp = filemtime($this->configFileName);
		}
		
		return $ret;
	}
	
	/**
	 * set the valus in the memory
	 * 
	 * @param string $variable
	 * @param string $value
	 * @param string $taskName
	 * @param string $variablePart
	 * @return boolean
	 */
	public function setConfig($variable, $value, $taskName = null, $variablePart = null)
	{
		if(is_null($taskName))
		{
			if(is_null($variablePart))
			{
				$this->config->KScheduler->$variable = $value;
			}
			else
			{
				$this->config->KScheduler->$variable->$variablePart = $value;
			}
			return true;
		}
		
		if(!isset($this->taskConfigList[$taskName]))
			return false;
			
		$taskConfig = &$this->taskConfigList[$taskName];
		
		if(!isset($taskConfig->$variable))
			return false;
	
		if(!strlen($variablePart))
		{
			$taskConfig->$variable = $value;
		}
		else
		{
			$taskConfig->$variable->$variablePart = $value;
		}
		return true;
	}
	
	public function getFilePath()
	{
		return $this->configFileName;	
	}
	
	public function getFileTimestamp()
	{
		return $this->configTimestamp;	
	}
	
	public function getTaskConfigList()
	{
		return 	$this->taskConfigList;
	}
	
	public function getScheduler()
	{
		return $this->config->KScheduler;
	}
	
	public function getId()
	{
		return $this->config->KScheduler->id;
	}
	
	public function getName()
	{
		return $this->config->KScheduler->name;
	}
	
	public function getStatusInterval()
	{
		return $this->config->KScheduler->statusInterval;
	}
	
	public function getTasksetPath()
	{
		return $this->config->KScheduler->tasksetPath;
	}
	
	public function getConfigItemsFilePath()
	{
		return $this->config->KScheduler->configItemsFilePath;
	}
	
	public function getQueueFiltersDir()
	{
		return $this->config->KScheduler->queueFiltersDir;
	}
	
	public function getCommandsDir()
	{
		return $this->config->KScheduler->commandsDir;
	}
	
	public function getUseSyslog()
	{
		return $this->config->KScheduler->useSyslog;
	}
	
	public function getMaxIdleTime()
	{
		return $this->config->KScheduler->maxIdleTime;
	}
	
	public function getStatusFilePath()
	{
		return $this->config->KScheduler->statusFilePath;
	}
	
	public function getCommandResultsFilePath()
	{
		return $this->config->KScheduler->commandResultsFilePath;
	}
	
	public function getLogDir()
	{
		return $this->config->KScheduler->logDir;
	}
	
	public function getMaxExecutionTime()
	{
		return $this->config->KScheduler->maxExecutionTime;
	}

	public function getPartnerId()
	{
		return $this->config->KScheduler->partnerId;
	}

	public function getHostName()
	{
		return $this->config->KScheduler->hostName;
	}

	public function getDwhPath()
	{
		return $this->config->KScheduler->dwhPath;
	}

	public function getTimezone()
	{
		return $this->config->KScheduler->timezone;
	}

	public function getServiceUrl()
	{
		return $this->config->KScheduler->serviceUrl;
	}
	
	public function getCurlTimeout()
	{
		return $this->config->KScheduler->curlTimeout;
	}
	
	public function getSecret()
	{
		return $this->config->KScheduler->secret;
	}
	
	
	/**
	 * @param string $name
	 * @return KSchedularTaskConfig
	 */
	public function getTaskConfig ( $name )
	{
		$taskConfig = $this->taskConfigList[$name];
		return $taskConfig;
	}
	
	private function parseConfgiFile ( $configFileName )
	{
	  	$this->config = new Zend_Config_Ini( $configFileName );

	  	foreach($this->config as $taskData)
	  	{
	  		$task = new KSchedularTaskConfig();
			$task->id = $taskData->id;
			$task->name = $taskData->name ;
			$task->type = $taskData->type;
			$task->maximumExecutionTime = $taskData->maximumExecutionTime ;
			$task->friendlyName = $taskData->friendlyName ; //7;
			$task->maxJobsEachRun = $taskData->maxJobsEachRun ; //7;
			$task->scriptPath = $taskData->scriptPath ; // "php C:/web/kaltura/support_prod/test/procmgr/class1.php";
			$task->scriptArgs=  $taskData->scriptArgs ; // "1";
			$task->maxInstances = $taskData->maxInstances ; // 1;
			$task->sleepBetweenStopStart = $taskData->sleepBetweenStopStart; // 3;
			$task->startForQueueSize = $taskData->startForQueueSize; // 0;
			$task->autoStart = $taskData->autoStart;
			$task->enable = $taskData->enable; //1;	  
			$task->affinity = $taskData->affinity;	  
			$task->fileExistReties = $taskData->fileExistReties;	  
			$task->fileExistInterval = $taskData->fileExistInterval;	  
			$task->baseLocalPath = $taskData->baseLocalPath;	  
			$task->baseSharedPath = $taskData->baseSharedPath;	  
			$task->baseTempLocalPath = $taskData->baseTempLocalPath;	  
			$task->baseTempSharedPath = $taskData->baseTempSharedPath;
			$task->params = $taskData->params; //1;
			$task->partnerGroups = $taskData->partnerGroups; //1;
			$task->minCreatedAtMinutes = $taskData->minCreatedAtMinutes; //1;
			$task->minPriority = $taskData->minPriority; //1;
			$task->maxPriority = $taskData->maxPriority; //1;
			$task->jobSubTypeIn = $taskData->jobSubTypeIn; //1;
			$task->jobSubTypeNotIn = $taskData->jobSubTypeNotIn; //1;
			
			$task->setPartnerId($this->getPartnerId());
			$task->setSecret($this->getSecret());
			$task->setCurlTimeout($this->getCurlTimeout());
			$task->setSchedulerId($this->getId());
			$task->setSchedulerName($this->getName());
			$task->setServiceUrl($this->getServiceUrl());
			$task->setDwhPath($this->getDwhPath());
			$task->setHostName($this->getHostName());
			$task->setTimezone($this->getTimezone());
			$task->setQueueFiltersDir($this->getQueueFiltersDir());
			$task->setCommandsDir($this->getCommandsDir());
			$task->setUseSyslog($this->getUseSyslog());
			$task->setInitOnly(false);
			$task->setMaxIdleTime($this->getMaxIdleTime());
			
			
	  		$this->taskConfigList[$this->config->key()] = $task;
	  	}
	}	

	public function saveToIniFile ( $fileName  )
	{
		$fileContent = '';
		foreach($this->config as $k => $v)
		{
			$fileContent .= "\n[$k]\n";
			$fileContent .= $this->configArrayToIni($v);
		}
		
		file_put_contents ( $fileName , $fileContent);
	}
	
	/**
	 * taken from http://www.zfforum.de/showthread.php?t=1453
	 */
	private function configArrayToIni($config,  $parentPrefix = false, $lastPrefixCount = 0) 
	{    
		$str = "";
    	if(is_array($config)) 
    	{
        	foreach ($config as $k => $v) 
        	{
	            $prefix = $k;            
    	        if($parentPrefix !== false) 
    	        {
        	        $prefix = $parentPrefix . '.' . $prefix;
            	}  
            	$prefixCount = substr_count($prefix, '.');
	            if(is_array($v)) 
	            {               
	               $str .= $this->configArrayToIni($v, $prefix, $prefixCount);
	            }
	            else 
	            {   
	               	if($prefixCount != $lastPrefixCount) 
	            	{
	                	$str .= "\n";                     
					}
               		$str .= $prefix . (strlen($prefix) < 50 ? str_repeat(" ", 50-strlen($prefix)) : '') . "= " . $v . "\n";               
            	}
            	$lastPrefixCount = $prefixCount;
        	}        
    	}
    	else
    	{
    		return $this->configArrayToIni($config->toArray(),  $parentPrefix , $lastPrefixCount ) ;
    	}
    	
    	return $str;
	}
	
  
	
}

/**
 * @package Scheduler
 *
 */
class KSchedularTaskConfig
{
	public $id;
	public $name;
	public $type;
	public $maximumExecutionTime;
	public $friendlyName;
	public $maxJobsEachRun;
	public $scriptPath;
	public $scriptArgs;
	public $maxInstances;
	public $sleepBetweenStopStart;
	public $startForQueueSize;
	public $enable;
	public $autoStart;
	public $affinity;
	public $fileExistReties;
	public $fileExistInterval;
	public $baseLocalPath;
	public $baseSharedPath;
	public $baseTempLocalPath;
	public $baseTempSharedPath;
	public $params;
	
	// our additional parameters
	public $partnerGroups;
	public $minCreatedAtMinutes;
	public $minPriority;
	public $maxPriority;
	public $jobSubTypeIn;
	public $jobSubTypeNotIn;
	
	
	// these params are not coming from the config, but set by the scheduler before new batch execution
	private $taskIndex;
	private $schedulerId;
	private $schedulerName;
	private $partnerId;
	private $serviceUrl;
	private $secret;
	private $curlTimeout;
	private $dwhPath;
	private $hostName;
	private $timezone;
	private $commandsDir;
	private $useSyslog;
	private $queueFiltersDir;
	private $initOnly;
	private $maxIdleTime;
	
	public function getTaskIndex()
	{
		return $this->taskIndex;
	}
	
	/**
	 * @param $maxIdleTime the $maxIdleTime to set
	 */
	public function setMaxIdleTime($maxIdleTime)
	{
		$this->maxIdleTime = $maxIdleTime;
	}

	/**
	 * @return the $maxIdleTime
	 */
	public function getMaxIdleTime()
	{
		return $this->maxIdleTime;
	}
	
	/**
	 * @param $initOnly the $initOnly to set
	 */
	public function setInitOnly($initOnly)
	{
		$this->initOnly = $initOnly;
	}

	/**
	 * @return the $initOnly
	 */
	public function isInitOnly()
	{
		return $this->initOnly;
	}

	
	/**
	 * @param $hostName the $hostName to set
	 */
	public function setHostName($hostName)
	{
		if(is_null($this->hostName))
			$this->hostName = $hostName;
	}

	/**
	 * @return the $hostName
	 */
	public function getHostName()
	{
		return $this->hostName;
	}

	
	/**
	 * @param $dwhPath the $dwhPath to set
	 */
	public function setDwhPath($dwhPath)
	{
		if(is_null($this->dwhPath))
			$this->dwhPath = $dwhPath;
	}

	/**
	 * @return the $dwhPath
	 */
	public function getDwhPath()
	{
		return $this->dwhPath;
	}
		
	/**
	 * @param $queueFiltersDir the $queueFiltersDir to set
	 */
	public function setQueueFiltersDir($queueFiltersDir)
	{
		if(is_null($this->queueFiltersDir))
			$this->queueFiltersDir = $queueFiltersDir;
	}
	
	/**
	 * @param $commandsDir the $commandsDir to set
	 */
	public function setCommandsDir($commandsDir)
	{
		if(is_null($this->commandsDir))
			$this->commandsDir = $commandsDir;
	}
	
	/**
	 * @param $useSyslog the $useSyslog to set
	 */
	public function setUseSyslog($useSyslog)
	{
		if(is_null($this->useSyslog))
			$this->useSyslog = $useSyslog;
	}
	
	/**
	 * @param $timezone the $timezone to set
	 */
	public function setTimezone($timezone)
	{
		if(is_null($this->timezone))
			$this->timezone = $timezone;
	}

	/**
	 * @return the $queueFiltersDir
	 */
	public function getQueueFiltersDir()
	{
		return $this->queueFiltersDir;
	}
	
	/**
	 * @return the $commandsDir
	 */
	public function getCommandsDir()
	{
		return $this->commandsDir;
	}
	
	/**
	 * @return the $useSyslog
	 */
	public function getUseSyslog()
	{
		return $this->useSyslog;
	}
	
	/**
	 * @return the $timezone
	 */
	public function getTimezone()
	{
		return $this->timezone;
	}
	
	public function setTaskIndex($taskIndex)
	{
		$this->taskIndex = $taskIndex;
	}

	public function getSchedulerName()
	{
		return $this->schedulerName;
	}
	
	public function setSchedulerName($schedulerName)
	{
		$this->schedulerName = $schedulerName;
	}
	
	public function getSchedulerId()
	{
		return $this->schedulerId;
	}
	
	public function setSchedulerId($schedulerId)
	{
		$this->schedulerId = $schedulerId;
	}
	
	public function getPartnerId()
	{
		return $this->partnerId;
	}
	
	public function setPartnerId($partnerId)
	{
		if(is_null($this->partnerId))
			$this->partnerId = $partnerId;
	}

	public function getServiceUrl()
	{
		return $this->serviceUrl;
	}
	
	public function setServiceUrl($serviceUrl)
	{
		if(is_null($this->serviceUrl))
			$this->serviceUrl = $serviceUrl;
	}

	public function getSecret()
	{
		return $this->secret;
	}
	
	public function setSecret($secret)
	{
		if(is_null($this->secret))
			$this->secret = $secret;
	}

	public function getCurlTimeout()
	{
		return $this->curlTimeout;
	}
	
	public function setCurlTimeout($curlTimeout)
	{
		if(is_null($this->curlTimeout))
			$this->curlTimeout = $curlTimeout;
	}
}
?>