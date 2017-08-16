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

			while(1) {
				if($manager->RefreshJobs($chunkedEncodeMaxConcurrent, $jobs)===false)
					sleep(2);
			}
        }


}

