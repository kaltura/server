<?php

/**
 * 
 * @package Scheduler
 *
 */
class KProcessWrapper
{
	public $handle;
	public $taskConfig;
	private $pipes;
	private $dieTime;
	
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
	
	public function getName()
	{
		return $this->taskConfig->name;
	}
	
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
				$this->closeResource($ref);
				
			unset($this->pipes);
			$this->pipes = null;
		}
		
		if($this->handle && is_resource($this->handle))
		{
//			$status = proc_get_status($this->handle);
//			if($status['running'] == true)
//			{
//				//process ran too long, kill it
//				//get the parent pid of the process we want to kill
//				$ppid = $status['pid'];
//				
//				//use ps to get all the children of this process, and kill them
//				$pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);
//				foreach($pids as $pid)
//				{
//					if(is_numeric($pid))
//					{
//						posix_kill($pid, 9); //9 is the SIGKILL signal
//					}
//				}
//			}
			
			proc_terminate($this->handle, 9); //9 is the SIGKILL signal
			proc_close($this->handle);
			$this->handle = null;
		}
	}
	
	private function closeResource($resource)
	{
		if(is_resource($resource))
			fclose($resource);
	}

}
?>