<?php

/**
 * 
 * @package Scheduler
 *
 */
class KProcessWrapper
{
	/**
	 * @var resource
	 */
	public $handle;
	
	/**
	 * @var KSchedularTaskConfig
	 */
	public $taskConfig;
	
	/**
	 * @var array
	 */
	private $pipes;
	
	/**
	 * The time that the process should die
	 * 
	 * @var int unix time
	 */
	private $dieTime;
	
	/**
	 * The process system pid
	 * 
	 * @var int
	 */
	private $processId;
	
	/**
	 * @param int $taskIndex
	 * @param string $logDir
	 * @param string $phpPath
	 * @param string $tasksetPath
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct($taskIndex, $logDir, $phpPath, $tasksetPath, KSchedularTaskConfig $taskConfig) // , $cwd, $env , $other_options  = null )
	{
		$taskConfig->setTaskIndex($taskIndex);
		$logName = str_replace('kasync', '', strtolower($taskConfig->name));
		$logDate = date('Y-m-d');
		$logFile = "$logDir/$logName-$taskIndex-$logDate.log";
		$sysLogFile = "$taskConfig->name.$taskIndex";
		
		$this->taskConfig = $taskConfig;
		
		$taskConfigStr = base64_encode(serialize($taskConfig));
		
		$cmdLine = '';
		$cmdLine .= (is_null($taskConfig->affinity) ? '' : "$tasksetPath -c " . ($taskConfig->affinity + $taskIndex) . ' ');
		$cmdLine = "$phpPath ";
		$cmdLine .= "$taskConfig->scriptPath ";
		$cmdLine .= "$taskConfigStr ";
		$cmdLine .= "'[" . mt_rand() . "]' ";
		
		if($taskConfig->getUseSyslog())
		{
			$cmdLine .= "2>&1 | logger -t $sysLogFile";
		}
		else
		{
			$cmdLine .= ">> $logFile 2>&1";
		}
		
		
		$descriptorspec = array(); // stdin is a pipe that the child will read from
//		$descriptorspec = array(0 => array("pipe", "r")); // stdin is a pipe that the child will read from
//			1 => array ( "file" ,$logFile , "a"  ) ,
//			2 => array ( "file" ,$logFile , "a"  ) ,
//			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
//			2 => array("pipe", "w"),  // stdout is a pipe that the child will write to
//			2 => array("file", "{$work_dir}/error-output.txt", "a") // stderr is a file to write to
		
		$other_options = array('suppress_errors' => FALSE, 'bypass_shell' => FALSE);
		
		KalturaLog::debug("Now executing [$cmdLine], [$other_options]");
		$process = proc_open($cmdLine, $descriptorspec, $pipes, null, null, $other_options);
		$this->pipes = $pipes;
		$this->handle = $process;
		$this->dieTime = time() + $taskConfig->maximumExecutionTime + 5;
	}
	
	public function __destruct()
	{
		$this->_cleanup();
	}
	
	/**
	 * @return string the batch worker name
	 */
	public function getName()
	{
		return $this->taskConfig->name;
	}
	
	/**
	 * @return int the batch instance index
	 */
	public function getIndex()
	{
		return $this->taskConfig->getTaskIndex();
	}
	
	/**
	 * @return boolean
	 */
	public function isRunning()
	{
		if($this->dieTime < time())
			return false;
		
		if(! is_resource($this->handle))
			return false;
		
		$status = proc_get_status($this->handle);
		return $status['running'];
	}
	
	public function _cleanup()
	{
		if($this->pipes)
		{
			foreach($this->pipes as $index => $ref)
			{
				if(is_resource($ref))
					fclose($ref);
			}
				
			unset($this->pipes);
			$this->pipes = null;
		}
		
		if($this->handle && is_resource($this->handle))
		{
			if($this->processId && function_exists('posix_kill'))
				posix_kill($this->processId, 9);
				
			proc_terminate($this->handle, 9); //9 is the SIGKILL signal
			proc_close($this->handle);
			$this->handle = null;
		}
	}
	
	/**
	 * @param int $processId
	 */
	public function setProcessId($processId)
	{
		$this->processId = $processId;
	}


}
