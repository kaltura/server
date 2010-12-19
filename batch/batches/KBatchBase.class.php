<?php
require_once ("bootstrap.php");
/**
 * Will be used as the base class for all the batch classes.
 * 
 * 
 * @package Scheduler
 */
abstract class KBatchBase extends KRunableClass implements IKalturaLogger
{
	/**
	 * @var KalturaClient
	 */
	protected $kClient = null;
	
	/**
	 * @var KalturaConfiguration
	 */
	protected $kClientConfig = null;
	
	/**
	 * @var boolean
	 */
	protected $isUnitTest = false;
	
	/**
	 * @var resource
	 */
	protected $monitorHandle = null;
	
	protected abstract function init();
	
	/**
	 * @param int $jobId
	 * @param KalturaBatchJob $job
	 */
	protected abstract function updateExclusiveJob($jobId, KalturaBatchJob $job);
	
	/**
	 * @param KalturaBatchJob $job
	 */
	protected abstract function freeExclusiveJob(KalturaBatchJob $job);
	
	/**
	 * @return int
	 * @throws Exception
	 */
	public static function getType()
	{
		throw new Exception("getType must be overidden");
	}
	
	/**
	 * @param boolean $unitTest
	 */
	public function setUnitTest($unitTest)
	{
		$this->isUnitTest = $unitTest;
	}
	
	/**
	 * @return KalturaClient
	 */
	protected function getClient()
	{
		return $this->kClient;
	}
	
	/**
	 * @return KalturaBatchJobFilter
	 */
	protected function getFilter()
	{
		$filter = new KalturaBatchJobFilter();
		
		if($this->partnerGroups && $this->partnerGroups != '*')
			$filter->workGroupIdIn = $this->partnerGroups;
		
		if ($this->taskConfig->minPriority && is_numeric($this->taskConfig->minPriority))
			$filter->priorityGreaterThanOrEqual = $this->taskConfig->minPriority;
		
		if ($this->taskConfig->maxPriority && is_numeric($this->taskConfig->maxPriority))
			$filter->priorityLessThanOrEqual = $this->taskConfig->maxPriority;
		
		if ($this->taskConfig->minCreatedAtMinutes && is_numeric($this->taskConfig->minCreatedAtMinutes))
		{
			$minCreatedAt = time() - ($this->taskConfig->minCreatedAtMinutes * 60);
			$filter->createdAtLessThanOrEqual = $minCreatedAt;
		}
		
		return $filter;
	}
	
	protected function getSchedulerId()
	{
		return $this->taskConfig->getSchedulerId();
	}
	
	protected function getSchedulerName()
	{
		return $this->taskConfig->getSchedulerName();
	}
	
	protected function getId()
	{
		return $this->taskConfig->id;
	}
	
	protected function getIndex()
	{
		return $this->taskConfig->getTaskIndex();
	}
	
	protected function getName()
	{
		return $this->taskConfig->name;
	}

	protected function getConfigHostName()
	{
		return $this->taskConfig->getHostName();
	}
	
	
	/**
	 * @return KalturaExclusiveLockKey
	 */
	protected function getExclusiveLockKey()
	{
		$lockKey = new KalturaExclusiveLockKey();
		$lockKey->schedulerId = $this->getSchedulerId();
		$lockKey->workerId = $this->getId();
		$lockKey->batchIndex = $this->getIndex();
		
		return $lockKey;
	}
	
	/**
	 * @param KalturaBatchJob $job
	 */
	protected function onFree(KalturaBatchJob $job)
	{
		$this->onJobEvent($job, KBatchEvent::EVENT_JOB_FREE);
	}
	
	/**
	 * @param KalturaBatchJob $job
	 */
	protected function onUpdate(KalturaBatchJob $job)
	{
		$this->onJobEvent($job, KBatchEvent::EVENT_JOB_UPDATE);
	}
	
	/**
	 * 
	 */
	protected function onBatchUp()
	{
		$this->onEvent(KBatchEvent::EVENT_BATCH_UP);
	}
	
	/**
	 * 
	 */
	protected function onBatchDown()
	{
		$this->onEvent(KBatchEvent::EVENT_BATCH_DOWN);
	}
	
	/**
	 * @param string $file
	 * @param int $size
	 * @param int $event_id
	 */
	protected function onFileEvent($file, $size, $event_id)
	{
		$event = new KBatchEvent();
		$event->value_1 = $size;
		$event->value_2 = $file;
		
		$this->onEvent($event_id, $event);
	}
	
	/**
	 * @param int $event_id
	 * @param KBatchEvent $event
	 */
	protected function onEvent($event_id, KBatchEvent $event = null)
	{
		if(is_null($event))
			$event = new KBatchEvent();
		
		$event->batch_client_version = "1.0";
		$event->batch_event_time = time();
		$event->batch_event_type_id = $event_id;
		
		$event->batch_session_id = $this->sessionKey;
		$event->batch_id = $this->getIndex();
		$event->batch_name = $this->getName();
		$event->section_id = $this->getId();
		$event->batch_type = $this->getType();
		$event->location_id = $this->getSchedulerId();
		$event->host_name = $this->getSchedulerName();
		
		KDwhClient::send($event);
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param int $event_id
	 */
	protected function onJobEvent(KalturaBatchJob $job, $event_id)
	{
		$event = new KBatchEvent();
		
		$event->partner_id = $job->partnerId;
		$event->entry_id = $job->entryId;
		$event->bulk_upload_id = $job->bulkJobId;
		$event->batch_parant_id = $job->parentJobId;
		$event->batch_root_id = $job->rootJobId;
		$event->batch_status = $job->status;
		$event->batch_progress = $job->progress;
		
		$this->onEvent($event_id, $event);
	}
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct($taskConfig = null)
	{
		parent::__construct($taskConfig);
		
		KalturaLog::debug('This batch index: ' . $this->getIndex());
		KalturaLog::debug('This session key: ' . $this->sessionKey);
		
		$this->kClientConfig = new KalturaConfiguration();
		$this->kClientConfig->setLogger($this);
		$this->kClientConfig->serviceUrl = $this->taskConfig->getServiceUrl();
		$this->kClientConfig->curlTimeout = $this->taskConfig->getCurlTimeout();
		$this->kClientConfig->clientTag = 'batch: ' . $this->taskConfig->getSchedulerName();
		
		$this->kClient = new KalturaClient($this->kClientConfig);
		//$ks = $this->kClient->session->start($secret, "user-2", KalturaSessionType::ADMIN);
		$ks = $this->createKS();
		$this->kClient->setKs($ks);
		
		KDwhClient::setFileName($this->taskConfig->getDwhPath());
		$this->onBatchUp();
		
		KScheduleHelperManager::saveRunningBatch($this->taskConfig->getCommandsDir(), $this->getName(), $this->getIndex());
	}
	
	/**
	 * @return string
	 */
	private function createKS()
	{
		$partnerId = $this->taskConfig->getPartnerId();
		$sessionType = KalturaSessionType::ADMIN;
		$puserId = 'batchUser';
		$privileges = '';
		$adminSecret = $this->taskConfig->getSecret();
		$expiry = 60 * 60 * 24 * 30; // 30 days
		
		
		$rand = rand(0, 32000);
		$rand = microtime(true);
		$expiry = time() + $expiry;
		$fields = array($partnerId, '', $expiry, $sessionType, $rand, $puserId, $privileges);
		$str = implode(";", $fields);
		
		$salt = $adminSecret;
		$hashed_str = $this->hash($salt, $str) . "|" . $str;
		$decoded_str = base64_encode($hashed_str);
		
		return $decoded_str;
	}
	
	/**
	 * @param string $salt
	 * @param string $str
	 * @return string
	 */
	private function hash($salt, $str)
	{
		return sha1($salt . $str);
	}
	
	/**
	 * @param string $localPath
	 * @return string
	 */
	protected function translateLocalPath2Shared($localPath)
	{
		$search = array();
		$replace = array();
		
		if(!is_null($this->taskConfig->baseLocalPath) || !is_null($this->taskConfig->baseSharedPath))
		{
			$search[] = $this->taskConfig->baseLocalPath;
			$replace[] = $this->taskConfig->baseSharedPath;
		}
		if(!is_null($this->taskConfig->baseTempLocalPath) || !is_null($this->taskConfig->baseTempSharedPath))
		{
			$search[] = $this->taskConfig->baseTempLocalPath;
			$replace[] = $this->taskConfig->baseTempSharedPath;
		}
		
		$search[] = '\\';
		$replace[] = '/';
			
		return str_replace($search, $replace, $localPath);
	}
	
	/**
	 * @param string $sharedPath
	 * @return string
	 */
	protected function translateSharedPath2Local($sharedPath)
	{
		$search = array();
		$replace = array();
		
		if(!is_null($search) || !is_null($replace))
		{
			$search = $this->taskConfig->baseSharedPath;
			$replace = $this->taskConfig->baseLocalPath;
		}
			
		return str_replace($search, $replace, $sharedPath);
	}
	
	/**
	 * @param array $files array(0 => array('name' => [name], 'path' => [path], 'size' => [size]), 1 => array('name' => [name], 'path' => [path], 'size' => [size]))
	 * @return string
	 */
	protected function checkFilesArrayExist(array $files)
	{
		foreach($files as $file)
			if(!$this->checkFileExists($file['path'], $file['size']))
				return false;
				
		return true;
	}
	
	/**
	 * @param string $file
	 * @param int $size
	 * @return bool
	 */
	protected function checkFileExists($file, $size = null)
	{
		if($this->isUnitTest)
			return true;
			
		KalturaLog::info("Check File Exists[$file] size[$size]");
		if(! $size)
		{
			clearstatcache();
			$size = filesize($file);
			if(! $size)
				return false;
		}
		
		$retries = ($this->taskConfig->fileExistReties ? $this->taskConfig->fileExistReties : 1);
		$interval = ($this->taskConfig->fileExistInterval ? $this->taskConfig->fileExistInterval : 5);
		
		while($retries > 0)
		{
			$check = $this->kClient->batch->checkFileExists($file, $size);
			if($check->exists && $check->sizeOk)
			{
				$this->onFileEvent($file, $size, KBatchEvent::EVENT_FILE_EXISTS);
				return true;
			}
			$this->onFileEvent($file, $size, KBatchEvent::EVENT_FILE_DOESNT_EXIST);
			
			sleep($interval);
			$retries --;
		}
		return false;
	}
	
	public function __destruct()
	{
		$this->onBatchDown();
		KScheduleHelperManager::unlinkRunningBatch($this->taskConfig->getCommandsDir(), $this->getName(), $this->getIndex());
	}
	
	/**
	 * @param string $jobType
	 * @param boolean $isCloser
	 * @return KalturaWorkerQueueFilter
	 */
	public function getQueueFilter($jobType, $isCloser = false)
	{
		$filter = $this->getFilter();
		
		$filter->jobTypeEqual = $jobType;
		
		if($isCloser)
		{
			$filter->statusEqual = KalturaBatchJobStatus::ALMOST_DONE;
		}
		else
		{
			$filter->statusIn = KalturaBatchJobStatus::PENDING . ',' . KalturaBatchJobStatus::RETRY;
		}
		
		$workerQueueFilter = new KalturaWorkerQueueFilter();
		$workerQueueFilter->schedulerId = $this->getSchedulerId();
		$workerQueueFilter->workerId = $this->getId();
		$workerQueueFilter->filter = $filter;
		$workerQueueFilter->jobType = $jobType;
		
		return $workerQueueFilter;
	}
	
	/**
	 * @param int $jobType
	 * @param boolean $isCloser
	 */
	public function saveQueueFilter($jobType, $isCloser = false)
	{
		$filter = $this->getQueueFilter($jobType, $isCloser);
		
		$dir = $this->taskConfig->getQueueFiltersDir();
		$type = $this->taskConfig->type;
		$res = self::createDir($dir);
		if(! $res)
			return;
		
		$path = "$dir/$type.flt";
		KalturaLog::debug("Saving filter to $path: " . print_r($filter, true));
		
		KScheduleHelperManager::saveFilter($path, $filter);
	}
	
	/**
	 * @param int $jobType
	 * @param int $size
	 * @param boolean $isCloser
	 */
	public function saveSchedulerQueue($jobType, $size = null, $isCloser = false)
	{
		if(is_null($size))
		{
			$workerQueueFilter = $this->getQueueFilter($jobType, $isCloser);
			$size = $this->kClient->batch->getQueueSize($workerQueueFilter);
		}
		
		$queueStatus = new KalturaBatchQueuesStatus();
		$queueStatus->workerId = $this->getId();
		$queueStatus->jobType = $jobType;
		$queueStatus->size = $size;
		
		$this->saveSchedulerCommands(array($queueStatus));
	}
	
	/**
	 * @param array $commands
	 */
	public function saveSchedulerCommands(array $commands)
	{
		$dir = $this->taskConfig->getCommandsDir();
		$type = $this->taskConfig->type;
		$res = self::createDir($dir);
		if(! $res)
			return;
		
		$path = "$dir/$type.cmd";
		KScheduleHelperManager::saveCommands($path, $commands);
	}
	
	/**
	 * @param string $path
	 * @param int $rights
	 * @return NULL|string
	 */
	public static function createDir($path, $rights = 0777)
	{
		if(! is_dir($path))
		{
			if(! file_exists($path))
			{
				KalturaLog::info("Creating temp directory [$path]");
				mkdir($path, $rights, true);
			}
			else
			{
				// already exists but not a directory 
				KalutraLog::err("Cannot create temp directory [$path] due to an error. Please fix and restart");
				return null;
			}
		}
		
		return $path;
	}
	
	/**
	 * @return KalturaBatchJob
	 */
	protected function newEmptyJob()
	{
		return new KalturaBatchJob();
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param string $msg
	 * @param int $status
	 * @param int $progress
	 * @param unknown_type $data
	 * @param boolean $remote
	 * @return KalturaBatchJob
	 */
	protected function updateJob(KalturaBatchJob $job, $msg, $status, $progress = null, KalturaJobData $data = null, $remote = null)
	{
		$updateJob = $this->newEmptyJob();
		
		if(! is_null($msg))
		{
			$updateJob->message = $msg;
			$updateJob->description = $job->description . "\n$msg";
		}
		
		$updateJob->status = $status;
		$updateJob->progress = $progress;
		$updateJob->data = $data;
		$updateJob->lastWorkerRemote = $remote;
		
		KalturaLog::info("job[$job->id] status: [$status] msg : [$msg]");
		if($this->isUnitTest)
			return $job;
		
		$job = $this->updateExclusiveJob($job->id, $updateJob);
		if($job instanceof KalturaBatchJob)
			$this->onUpdate($job);
		
		return $job;
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param int $errType
	 * @param int $errNumber
	 * @param string $msg
	 * @param int $status
	 * @param KalturaJobData $data
	 * @return KalturaBatchJob
	 */
	protected function closeJob(KalturaBatchJob $job, $errType, $errNumber, $msg, $status, $data = null)
	{
		if(! is_null($errType))
			KalturaLog::err($msg);
		
		$updateJob = $this->newEmptyJob();
		
		if(! is_null($msg))
		{
			$updateJob->message = $msg;
			$updateJob->description = $job->description . "\n$msg";
		}
		
		if($status == KalturaBatchJobStatus::FINISHED)
			$updateJob->progress = 100;
			
		$updateJob->status = $status;
		$updateJob->errType = $errType;
		$updateJob->errNumber = $errNumber;
		$updateJob->data = $data;
		
		KalturaLog::info("job[$job->id] status: [$status] msg : [$msg]");
		if($this->isUnitTest)
		{
			$job->status = $updateJob->status;
			$job->message = $updateJob->message;
			$job->description = $updateJob->description;
			$job->errType = $updateJob->errType;
			$job->errNumber = $updateJob->errNumber;
			return $job;
		}
		
		$job = $this->updateExclusiveJob($job->id, $updateJob);
		if($job instanceof KalturaBatchJob)
			$this->onUpdate($job);
		$this->onUpdate($job);
		
		KalturaLog::info("Free job[$job->id]");
		$job = $this->freeExclusiveJob($job);
		if($job instanceof KalturaBatchJob)
			$this->onFree($job);
		
		return $job;
	}
	
	protected function getMonitorPath()
	{
		return 'killer/KBatchKillerExe.php';
	}
	
	protected function startMonitor(array $files)
	{
		if($this->monitorHandle && is_resource($this->monitorHandle))
			return;
		
		$killConfig = new KBatchKillerConfig();
		
		$killConfig->pid = getmypid();
		$killConfig->maxIdleTime = $this->taskConfig->getMaxIdleTime();
		$killConfig->sleepTime = $this->taskConfig->getMaxIdleTime() / 2;
			/*
			Do not run killer process w/out set config->maxIdle
			*/
		if($killConfig->maxIdleTime<=0 || is_null($killConfig->maxIdleTime) ) {
			KalturaLog::info(__METHOD__.': The MaxIdleTime is not set properly. The Killer job will not run');
			return;
		}
		$killConfig->files = $files;
//$killConfig->files = array("/root/anatol/0_phxt8hsa.api.log");
		$killConfig->sessionKey = $this->sessionKey;
		$killConfig->batchIndex = $this->getIndex();
		$killConfig->batchName = $this->getName();
		$killConfig->workerId = $this->getId();
		$killConfig->workerType = $this->getType();
		$killConfig->schedulerId = $this->getSchedulerId();
		$killConfig->schedulerName = $this->getSchedulerName();
		$killConfig->dwhPath = $this->taskConfig->getDwhPath();
		
		$phpPath = 'php'; // TODO - get it from somewhere
		$killerPath = $this->getMonitorPath();
		$killerPathStr = base64_encode(serialize($killConfig));
		
		$cmdLine = "$phpPath $killerPath $killerPathStr";
		
		$descriptorspec = array(); // stdin is a pipe that the child will read from
		$other_options = array('suppress_errors' => FALSE, 'bypass_shell' => FALSE);
		
		KalturaLog::debug("Killer config:\n" . print_r($killConfig, true));
		KalturaLog::debug("Now executing [$cmdLine]");
		KalturaLog::info('Starting monitor');
		$this->monitorHandle = proc_open($cmdLine, $descriptorspec, $pipes, null, null, $other_options);
	}
	
	protected function stopMonitor()
	{
		if(!$this->monitorHandle || !is_resource($this->monitorHandle))
			return;
			
		KalturaLog::info('Stoping monitor');
	
		$status = proc_get_status($this->monitorHandle);
		if($status['running'] == true)
		{
			proc_terminate($this->monitorHandle, 9); //9 is the SIGKILL signal
			proc_close($this->monitorHandle);
			
			$pid = $status['pid'];
			
			if(function_exists('posix_kill'))
			{
				posix_kill($pid, 9);
			}
			else
			{
				exec("kill -9 $pid", $output); // for linux
				//exec("taskkill -F -PID $pid", $output); // for windows
			}
		}
		
		$this->monitorHandle = null;
	}
	
	function log($message)
	{
		KalturaLog::log($message);
	}
}