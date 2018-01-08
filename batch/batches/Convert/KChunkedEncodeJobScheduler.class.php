<?php
/**
 * @package Scheduler
 */

/**
 * KChunkedEncodeJobScheduler
 *	Looks for Chunked Encode jobs stored in memcache storage
 *	Uses following configuration fields
 *	- chunkedEncodeMemcacheHost - memcache host URL (mandatory)
 *	- chunkedEncodeMemcachePort - memcache host port (mandatory)
 *	- chunkedEncodeMemcacheToken - token to differentiate between general/global Kaltura jobs and per customer dedicated servers (optional, default:null)
 *	- chunkedEncodeMaxConcurrent - maximum concurrently executed chunks jobs, more or less servers core number (optional, default:5)
 *
 * @package Scheduler
 */
class KChunkedEncodeJobScheduler extends KPeriodicWorker
{
        /* (non-PHPdoc)
         * @see KBatchBase::getType()
         */
        public static function getType()
        {
			return KalturaBatchJobType::CHUNKED_ENCODE_JOB_SCHEDULER;
        }

        /* (non-PHPdoc)
         * @see KBatchBase::run()
         */
        public function run($jobs = null)
        {
			$pidFileName = isset(KBatchBase::$taskConfig->params->tempDirectoryPath)? KBatchBase::$taskConfig->params->tempDirectoryPath : sys_get_temp_dir();
			$pidFileName.= '/chunked_encode_scheduler.pid';
			if($this->lockSchedulerProcessId($pidFileName,get_class($this))==false){
				return("Duplicate Chunk Schedulers");
			}

				/*
				 * 'chunkedEncodeMemcacheHost' and 'chunkedEncodeMemcachePort'
				 * are mandatory
				 */
			if(!(isset(KBatchBase::$taskConfig->params->chunkedEncodeMemcacheHost) 
			&& isset(KBatchBase::$taskConfig->params->chunkedEncodeMemcachePort))){
				$returnVar = -1;
				$errMsg = "ERROR: Missing memcache host/port in the batch/worker.ini";
				KalturaLog::log($errMsg);
				return ($errMsg);
			}
			$host = KBatchBase::$taskConfig->params->chunkedEncodeMemcacheHost;
			$port = KBatchBase::$taskConfig->params->chunkedEncodeMemcachePort;
			
			if(isset(KBatchBase::$taskConfig->params->chunkedEncodeMemcacheToken)){
				$token = KBatchBase::$taskConfig->params->chunkedEncodeMemcacheToken;
			}
			else
				$token = null;

			if(isset(KBatchBase::$taskConfig->params->chunkedEncodeMaxConcurrent)){
				$chunkedEncodeMaxConcurrent = KBatchBase::$taskConfig->params->chunkedEncodeMaxConcurrent;
			}
			else {
				$chunkedEncodeMaxConcurrent = 5;
			}

				// Allocate the manager object
			$manager = new KChunkedEncodeMemcacheScheduler($token);

			$config = array('host'=>$host, 'port'=>$port);//, 'flags'=>1);
			$manager->Setup($config);
			
				// List of per scheduler instance currently processed jobs.
				// Required in order to manage correctly the max concurrent active jobs 
				// (aka 'chunkedEncodeMaxConcurrent')
			$jobs = array();
			$attempts = 5;
			while(1) {
				$rv = $manager->RefreshJobs($chunkedEncodeMaxConcurrent, $jobs);
				if($rv===false) {
					if(--$attempts==0){
						KalturaLog::log("Failed to RefreshJobs. Probably memcache server failure. Leaving");
						break;
					}
					KalturaLog::log("Remaining attempts:$attempts");
				}
				if($rv===true)
					$attempts = 5;
				else
					sleep(2);
			}
        }

        /*************
         * 
         */
		private static function lockSchedulerProcessId($fileName, $processName)
		{
			KalturaLog::log("pidFilename($fileName), processName($processName)");
			$myPid = getmypid();
			KalturaLog::log("myPid:$myPid");
			if(!file_exists($fileName)){
				file_put_contents($fileName, $myPid);
				KalturaLog::log("Locking process ($processName,pid:$myPid) in file ($fileName)");
				return true;
			}
			if(file_exists($fileName)){
				$lockPid = (int)file_get_contents($fileName);
				if($lockPid==0) {
					KalturaLog::log("Don't lock process ($processName,pid:$myPid). Allow duplicates.");
					return true;
				}
				$output = shell_exec("pgrep -f $processName");
				$pidArr = explode("\n", $output);
				foreach($pidArr as $pid) {
					$pid = trim($pid);
					if($pid==$lockPid) {
						$output = shell_exec("kill -9 $lockPid");
						KalturaLog::log("Killed running process ($processName, pid:$pid)");
					}
				}
			}
			file_put_contents($fileName, $myPid);
			KalturaLog::log("Locking process ($processName,pid:$myPid) in file ($fileName)");
			return true;
		}

}

