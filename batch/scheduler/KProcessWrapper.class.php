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
	 * Whether the process wrapper is mocked.
	 * @var boolean
	 */
	private $isMockedProcess;
	
	public function __construct(KSchedularTaskConfig $taskConfig, $taskIndex) {
		$taskConfig->setTaskIndex($taskIndex);
		$this->taskConfig = $taskConfig;
		
		$this->dieTime = time() + $taskConfig->maximumExecutionTime + 5;
	}
	
	public function initMockedProcess($procId) 
	{
		$this->processId = $procId;
		$this->isMockedProcess = true;
	}
	
	/**
	 * @param int $taskIndex
	 * @param string $logDir
	 * @param string $phpPath
	 * @param string $tasksetPath
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function init($logDir, $phpPath, $tasksetPath)
	{
		$idx = $this->taskConfig->getTaskIndex();
		$logName = str_replace('kasync', '', strtolower($this->taskConfig->name));
		$logDate = date('Y-m-d');
		$logFileOut = "$logDir/$logName-$idx-$logDate.log";
		$logFileErr = "$logDir/$logName-$idx-$logDate.err.log";
	
		$taskConfigStr = base64_encode(gzcompress(serialize($this->taskConfig)));
		
		$cmdLine = '';
		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && PHP_OS !== 'Darwin') {
			// Set the process as its own session leader
			$cmdLine = 'setsid ';
		}
		
		$cmdLine .= "$phpPath ";
		$cmdLine .= realpath(__DIR__ . '/../') . '/' . $this->taskConfig->scriptPath . ' ';
		$cmdLine .= "$taskConfigStr ";
		$cmdLine .= "'[" . mt_rand() . "]' ";
		$cmdLine .= ">> $logFileOut 2>> $logFileErr";
		
		
		$descriptorspec = array(); // stdin is a pipe that the child will read from
		$other_options = array('suppress_errors' => FALSE, 'bypass_shell' => FALSE);
		
		KalturaLog::debug("Now executing [$cmdLine]");
		$process = proc_open($cmdLine, $descriptorspec, $pipes, null, null, $other_options);
		$this->pipes = $pipes;
		$this->handle = $process;
		$this->isMockedProcess = false;
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
		$shouldNotValidateDieTime = ($this->taskConfig->type === 'KAsyncConvert' &&
			isset($this->taskConfig->params->processStopCheckingDieTime) &&
			$this->taskConfig->params->processStopCheckingDieTime == 1);

		if (!$shouldNotValidateDieTime)
		{
			if ($this->dieTime < time())
			{
				return false;
			}
		}
		
		if($this->isMockedProcess) {
			$res = $this->checkMockedProcessRunning();	
			if(!$res)
				$this->processId = null;
			return $res;
		}
		
		if(!is_resource($this->handle))
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
		
		if($this->isMockedProcess) {
			$this->killProcess();
			KScheduleHelperManager::unlinkRunningBatch($this->taskConfig->name, $this->taskConfig->getTaskIndex());
			return;
		}
		
		if($this->handle && is_resource ($this->handle))
		{
			$status = proc_get_status ( $this->handle );
			if(!$status ['running'])
				return;
			
			$this->killProcess();
			
			proc_terminate ( $this->handle );
			proc_close ( $this->handle );
			
			$this->handle = null;
		}
	}
	
	private function killProcess() {
		KalturaLog::notice("About to kill process " . $this->processId);
		if ($this->processId) {
			if(function_exists ( 'posix_kill' )){
				posix_kill ( $this->processId, 9 );
			} else {
				// Make sure we kill the child process (the PHP)
				system ( "kill " . $this->processId, $rc );
			}
		}
	}

	
	/**
	 * @param int $processId
	 */
	public function setProcessId($processId)
	{
		$this->processId = $processId;
	}

	
	public function checkMockedProcessRunning() {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			exec ( "tasklist /FI \"PID eq " . $this->processId . "\"", $rc);
			return (strpos($rc[0], "No tasks are running which match the specified criteria") === FALSE);
		} else {
		    system('kill -0 ' . $this->processId, $rc);
		    return ($rc == 0);
		}
	}
}
