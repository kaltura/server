<?php

class KBatchKiller
{
	/**
	 * @var KBatchKillerConfig
	 */
	private $config;
	
	public function __construct(KBatchKillerConfig $config) 
	{
		$this->config = $config;
		
		KDwhClient::setFileName($config->dwhPath);
	}
	
	/**
	 * @return bool
	 */
	protected function shouldKill()
	{
		clearstatcache();
		
		$now = time();
		foreach($this->config->files as $file)
		{
			if(!file_exists($file))
			{
				$this->logToDWH(KBatchEvent::EVENT_KILLER_FILE_DOESNT_EXIST, $file);
				return true;
			}
				
			$idle = $now - filemtime($file);
			if($idle > $this->config->maxIdleTime)
			{
				$this->logToDWH(KBatchEvent::EVENT_KILLER_FILE_IDLE, $file, $idle);
				return true;
			}
		}
		return false;
	}
	
	protected function logToDWH($event_id, $filePath = null, $idleTime = null)
	{
		$event = new KBatchEvent();
		
		$event->value_1 = $idleTime;
		$event->value_2 = $filePath;
		
		$event->batch_client_version = "1.0";
		$event->batch_event_time = time();
		$event->batch_event_type_id = $event_id;
		
		$event->batch_session_id = $this->config->sessionKey;
		$event->batch_id = $this->config->batchIndex;
		$event->batch_name = $this->config->batchName;
		$event->section_id = $this->config->workerId;
		$event->batch_type = $this->config->workerType;
		$event->location_id = $this->config->schedulerId;
		$event->host_name = $this->config->schedulerName;
		
		KDwhClient::send($event);
	}
	
	protected function killBatch()
	{
		$ppid = $this->config->pid;
		self::killProcessTree($ppid);
	}
	
	/*
	* Kill the process and all its childs and sub-childs.
	* The parent process should be killed after the child processes.
	*/
	protected static function killProcessTree($ppid)
	{
		$mypid = getmypid();
KalturaLog::info(__METHOD__.': Killing parent pid='.$ppid.', mypid='.$mypid);
//		$pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);
//		$pids = preg_split('/\s+/', `ps -o ppid $ppid | tail -n 1`);
		$rawpids = preg_split('/\s+/', `ps -o pid,ppid -ax | grep $ppid`);
		$pids = array();
		
		/*
			Clean-up and normalize the list of pids, into a valid pairs (pid:ppid)
		*/
		foreach($rawpids as $pid) {
			if(!is_numeric($pid)) {
				echo "not num=".$pid;
			}
			else {
				$pids[] = $pid;
			}
		}
		/*
			run through the list, go recursive for every child and kill the process
		*/
KalturaLog::info(__METHOD__.': Child pids='.print_r($pids,true));				
		$cnt = count($pids);
		for ($i=0;$i<$cnt; $i+=2) {
			$pid = $pids[$i];
			if(!is_numeric($pid) || $pid==$mypid || $pids[$i+1]!=$ppid){
				continue;
			}
			self::killProcessTree($pid);
			
KalturaLog::info(__METHOD__.': Killing pid='.$pid);
			if(function_exists('posix_kill'))
			{
				$rv=posix_kill($pid, 9);
				KalturaLog::info("pid=".$pid.", rv=".$rv);
			}
			else
			{
				exec("kill -9 $pid", $output); // for linux
	//			exec("taskkill -F -PID $pid", $output); // for windows
			}
		}
		if(function_exists('posix_kill'))
		{
			$rv=posix_kill($ppid, 9);
			KalturaLog::info("ppid=".$ppid.", rv=".$rv);
		}
		else
		{
			exec("kill -9 $ppid", $output); // for linux
//			exec("taskkill -F -PID $ppid", $output); // for windows
		}
	}
	
	public function run()
	{
		$this->logToDWH(KBatchEvent::EVENT_KILLER_UP);
		while(true)
		{
			sleep($this->config->sleepTime);
			if($this->shouldKill())
			{
				$this->killBatch();
				return;
			}
		}
	}
}