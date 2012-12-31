<?php
/**
 * Base class for all the batch classes.
 * 
 * @package Scheduler
 */
abstract class KBatchBase implements IKalturaLogger
{
	/**
	 * @var KSchedularTaskConfig
	 */
	protected $taskConfig;
	
	/**
	 * @var string
	 */
	protected $sessionKey;
	
	/**
	 * @var int timestamp
	 */
	private $start;
	
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

	/**
	 * @return KalturaBatchJobType
	 */
	abstract protected function getJobType();
	
	/**
	 * @param array $jobs
	 * @return array $jobs
	 */
	abstract public function run($jobs = null);
	
	protected function init()
	{
		set_error_handler(array(&$this, "errorHandler"));
	}
	
	public function errorHandler($errNo, $errStr, $errFile, $errLine)
	{
	    
		$errorFormat = "%s line %d - %s";
		switch ($errNo)
		{
			case E_NOTICE:
			case E_STRICT:
			case E_USER_NOTICE:
				KalturaLog::log(sprintf($errorFormat, $errFile, $errLine, $errStr), KalturaLog::NOTICE);
				break;
			case E_USER_WARNING:
			case E_WARNING:
				KalturaLog::log(sprintf($errorFormat, $errFile, $errLine, $errStr), KalturaLog::WARN);
				break; 
		}
	}
	
	public function done()
	{
		KalturaLog::info("Done after [" . (microtime ( true ) - $this->start ) . "] seconds");
	}
	
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
		
	
	protected function impersonate($partnerId)
	{
		$this->kClientConfig->partnerId = $partnerId;
		$this->kClient->setConfig($this->kClientConfig);
	}
	
	protected function unimpersonate()
	{
		$this->kClientConfig->partnerId = $this->taskConfig->getPartnerId();
		$this->kClient->setConfig($this->kClientConfig);
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
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct($taskConfig = null)
	{
		/*
		 *  argv[0] - the script name
		 *  argv[1] - serialized KSchedulerConfig config
		 */
		global $argv, $g_context;

		$this->sessionKey = uniqid('sess');		
		$this->start = microtime(true);
		
		if(is_null($taskConfig))
		{
			$this->taskConfig = unserialize(base64_decode($argv[1]));
		}
		else
		{
			$this->taskConfig = $taskConfig;
		}
		
		if(!$this->taskConfig)
			die("Task config not supplied");
		
		date_default_timezone_set($this->taskConfig->getTimezone());
		
		// clear seperator between executions
		KalturaLog::debug('___________________________________________________________________________________');
		KalturaLog::info(file_get_contents(dirname( __FILE__ ) . "/../VERSION.txt"));
		
		if(! ($this->taskConfig instanceof KSchedularTaskConfig))
		{
			KalturaLog::err('config is not a KSchedularTaskConfig');
			die;
		}
		 
		KalturaLog::debug("set_time_limit({$this->taskConfig->maximumExecutionTime})");
		set_time_limit($this->taskConfig->maximumExecutionTime);
		
		
		KalturaLog::debug('This batch index: ' . $this->getIndex());
		KalturaLog::debug('This session key: ' . $this->sessionKey);
		
		$this->kClientConfig = new KalturaConfiguration($this->taskConfig->getPartnerId());
		$this->kClientConfig->setLogger($this);
		$this->kClientConfig->serviceUrl = $this->taskConfig->getServiceUrl();
		$this->kClientConfig->curlTimeout = $this->taskConfig->getCurlTimeout();
		$this->kClientConfig->clientTag = 'batch: ' . $this->taskConfig->getSchedulerName();
		
		$this->kClient = new KalturaClient($this->kClientConfig);
		//$ks = $this->kClient->session->start($secret, "user-2", KalturaSessionType::ADMIN);
		$ks = $this->createKS();
		$this->kClient->setKs($ks);
		
		KDwhClient::setEnabled($this->taskConfig->getDwhEnabled());
		KDwhClient::setFileName($this->taskConfig->getDwhPath());
		$this->onBatchUp();
		
		KScheduleHelperManager::saveRunningBatch($this->getName(), $this->getIndex());
	}
	
	protected function getParams($name)
	{
		return  $this->taskConfig->$name;
	}
	
	protected function getAdditionalParams($name)
	{
		if(isset($this->taskConfig->params) && isset($this->taskConfig->params->$name))
			return $this->taskConfig->params->$name;
			
		return null;
	}
	
	/**
	 * @return string
	 */
	private function createKS()
	{
		$partnerId = $this->taskConfig->getPartnerId();
		$sessionType = KalturaSessionType::ADMIN;
		$puserId = 'batchUser';
		$privileges = 'disableentitlement';
		$adminSecret = $this->taskConfig->getSecret();
		$expiry = 60 * 60 * 24 * 30; // 30 days
		
		
		$rand = rand(0, 32000);
		$rand = microtime(true);
		$expiry = time() + $expiry;
		$masterPartnerId = $this->taskConfig->getPartnerId();
		$additionalData = null;
		
		$fields = array($partnerId, '', $expiry, $sessionType, $rand, $puserId, $privileges, $masterPartnerId, $additionalData);
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
	
	protected static function foldersize($path)
	{
	  if(!file_exists($path)) return 0;
	  if(is_file($path)) return kFile::fileSize($path);
	  $ret = 0;
	  foreach(glob($path."/*") as $fn)
	    $ret += KBatchBase::foldersize($fn);
	  return $ret;
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

			// If this is not a file but a directory, certain operations should be done diffrently:
			// - size calcultions
			// - the response from the client (to check the client size beaviour)
		$directorySync = is_dir($file);
		KalturaLog::info("Check File Exists[$file] size[$size]");
		if(! $size)
		{
			clearstatcache();
			if($directorySync)
				$size=KBatchBase::foldersize($file);
			else
				$size = kFile::fileSize($file);
			if(! $size)
				return false;
		}
		
		$retries = ($this->taskConfig->fileExistReties ? $this->taskConfig->fileExistReties : 1);
		$interval = ($this->taskConfig->fileExistInterval ? $this->taskConfig->fileExistInterval : 5);
		
		while($retries > 0)
		{
			$check = $this->kClient->batch->checkFileExists($file, $size);
				// In case of directorySync - do not check client sizeOk - to be revised
			if($check->exists && ($check->sizeOk || $directorySync))
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
		KScheduleHelperManager::unlinkRunningBatch($this->getName(), $this->getIndex());
	}
	
	/**
	 * @param array $commands
	 */
	public function saveSchedulerCommands(array $commands)
	{
		$type = $this->taskConfig->type;
		$file = "$type.cmd";
		KScheduleHelperManager::saveCommand($file, $commands);
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
				KalturaLog::err("Cannot create temp directory [$path] due to an error. Please fix and restart");
				return null;
			}
		}
		
		return $path;
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
		$killConfig->dwhEnabled = $this->taskConfig->getDwhEnabled();
		
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
	
	/**
	 * @param string $fileName
	 * @return boolean
	 */
	protected function pollingFileExists($fileName)
	{
		$retries = ($this->taskConfig->inputFileExistRetries ? $this->taskConfig->inputFileExistRetries : 10);
		$interval = ($this->taskConfig->inputFileExistInterval ? $this->taskConfig->inputFileExistInterval : 5);
		
		for ($retry = 0; $retry < $retries; $retry++)
		{
			clearstatcache();
			if (file_exists($fileName))
				return true;
			
			KalturaLog::log("File $fileName does not exist, try $retry, waiting $interval seconds");
			sleep($interval);
		}
		return false;
	}
	
	function log($message)
	{
		KalturaLog::log($message);
	}
}
