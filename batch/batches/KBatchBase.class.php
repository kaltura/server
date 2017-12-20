<?php
/**
 * Base class for all the batch classes.
 *
 * @package Scheduler
 */
abstract class KBatchBase implements IKalturaLogger
{
	const PRIVILEGE_BATCH_JOB_TYPE = "jobtype";
	
	/**
	 * @var KSchedularTaskConfig
	 */
	public static $taskConfig;

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
	public static $kClient = null;

	/**
	 * @var KalturaConfiguration
	 */
	public static $kClientConfig = null;
	
	/**
	 * @var string
	 */
	public static $clientTag = null;

	/**
	 * @var boolean
	 */
	protected $isUnitTest = false;

	/**
	 * @var resource
	 */
	protected $monitorHandle = null;

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
		$done = "Done after [" . (microtime ( true ) - $this->start ) . "] seconds";
		KalturaLog::info($done);
		KalturaLog::stderr($done, KalturaLog::INFO);
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	public static function getType()
	{
		throw new Exception("Method getType must be overridden");
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
		return self::$kClient;
	}


	static public function impersonate($partnerId)
	{
		self::$kClient->setPartnerId($partnerId);
	}

	static public function unimpersonate()
	{
		self::$kClient->setPartnerId(self::$taskConfig->getPartnerId());
	}

	protected function getSchedulerId()
	{
		return self::$taskConfig->getSchedulerId();
	}

	protected function getSchedulerName()
	{
		return self::$taskConfig->getSchedulerName();
	}

	protected function getId()
	{
		return self::$taskConfig->id;
	}

	protected function getIndex()
	{
		return self::$taskConfig->getTaskIndex();
	}

	protected function getName()
	{
		return self::$taskConfig->name;
	}

	protected function getConfigHostName()
	{
		return self::$taskConfig->getHostName();
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
			$data = gzuncompress(base64_decode($argv[1]));
			self::$taskConfig = unserialize($data);
		}
		else
		{
			self::$taskConfig = $taskConfig;
		}

		if(!self::$taskConfig)
			die("Task config not supplied");

		date_default_timezone_set(self::$taskConfig->getTimezone());

		// clear seperator between executions
		KalturaLog::debug('___________________________________________________________________________________');
		KalturaLog::stderr('___________________________________________________________________________________', KalturaLog::DEBUG);
		KalturaLog::info(file_get_contents(dirname( __FILE__ ) . "/../VERSION.txt"));

		if(! (self::$taskConfig instanceof KSchedularTaskConfig))
		{
			KalturaLog::err('config is not a KSchedularTaskConfig');
			die;
		}

		KalturaLog::debug("set_time_limit({".self::$taskConfig->maximumExecutionTime."})");
		set_time_limit(self::$taskConfig->maximumExecutionTime);


		KalturaLog::info('Batch index [' . $this->getIndex() . '] session key [' . $this->sessionKey . ']');

		self::$kClientConfig = new KalturaConfiguration();
		self::$kClientConfig->setLogger($this);
		self::$kClientConfig->serviceUrl = self::$taskConfig->getServiceUrl();
		self::$kClientConfig->curlTimeout = self::$taskConfig->getCurlTimeout();

		if(isset(self::$taskConfig->clientConfig))
		{
			foreach(self::$taskConfig->clientConfig as $attr => $value)
				self::$kClientConfig->$attr = $value;
		}

		self::$kClient = new KalturaClient(self::$kClientConfig);
		self::$kClient->setPartnerId(self::$taskConfig->getPartnerId());

		self::$clientTag = 'batch: ' . self::$taskConfig->getSchedulerName() . ' ' . get_class($this) . " index: {$this->getIndex()} sessionId: " . UniqueId::get();
		self::$kClient->setClientTag(self::$clientTag);
		
		//$ks = self::$kClient->session->start($secret, "user-2", KalturaSessionType::ADMIN);
		$ks = $this->createKS();
		self::$kClient->setKs($ks);

		KDwhClient::setEnabled(self::$taskConfig->getDwhEnabled());
		KDwhClient::setFileName(self::$taskConfig->getDwhPath());
		$this->onBatchUp();

		KScheduleHelperManager::saveRunningBatch($this->getName(), $this->getIndex());
	}

	protected function getParams($name)
	{
		return  self::$taskConfig->$name;
	}

	protected function getAdditionalParams($name)
	{
		if(isset(self::$taskConfig->params) && isset(self::$taskConfig->params->$name))
			return self::$taskConfig->params->$name;

		return null;
	}

	/**
	 * @return array
	 */
	protected function getPrivileges()
	{
		return array('disableentitlement');
	}

	/**
	 * @return string
	 */
	private function createKS()
	{
		$partnerId = self::$taskConfig->getPartnerId();
		$sessionType = KalturaSessionType::ADMIN;
		$puserId = 'batchUser';
		$privileges = implode(',', $this->getPrivileges());
		$adminSecret = self::$taskConfig->getSecret();
		$expiry = 60 * 60 * 24 * 30; // 30 days


		$rand = rand(0, 32000);
		$rand = microtime(true);
		$expiry = time() + $expiry;
		$masterPartnerId = self::$taskConfig->getPartnerId();
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

		if(!is_null(self::$taskConfig->baseLocalPath) || !is_null(self::$taskConfig->baseSharedPath))
		{
			$search[] = self::$taskConfig->baseLocalPath;
			$replace[] = self::$taskConfig->baseSharedPath;
		}
		if(!is_null(self::$taskConfig->baseTempLocalPath) || !is_null(self::$taskConfig->baseTempSharedPath))
		{
			$search[] = self::$taskConfig->baseTempLocalPath;
			$replace[] = self::$taskConfig->baseTempSharedPath;
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
			$search = self::$taskConfig->baseSharedPath;
			$replace = self::$taskConfig->baseLocalPath;
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

	protected function setFilePermissions($filePath)
	{
		if(is_dir($filePath))
		{
			$chmod = 0750;
			if(self::$taskConfig->getDirectoryChmod())
				$chmod = octdec(self::$taskConfig->getDirectoryChmod());
				
			KalturaLog::debug("chmod($filePath, $chmod)");
			@chmod($filePath, $chmod);
			$dir = dir($filePath);
			while (false !== ($file = $dir->read()))
			{
				if($file[0] != '.')
					$this->setFilePermissions($filePath . DIRECTORY_SEPARATOR . $file);
			}
			$dir->close();
		}
		else
		{
			$chmod = 0640;
			if(self::$taskConfig->getChmod())
				$chmod = octdec(self::$taskConfig->getChmod());
		
			KalturaLog::debug("chmod($filePath, $chmod)");
			@chmod($filePath, $chmod);
		}
	}
	
	/**
	 * @param string $file
	 * @param int $size
	 * @return bool
	 */
	protected function checkFileExists($file, $size = null, $directorySync = null)
	{
		$this->setFilePermissions($file);
		
		if($this->isUnitTest)
		{
			KalturaLog::debug("Is in unit test");
			return true;
		}

			// If this is not a file but a directory, certain operations should be done diffrently:
			// - size calcultions
			// - the response from the client (to check the client size beaviour)
		if(is_null($directorySync))
			$directorySync = is_dir($file);
		KalturaLog::info("Check File Exists[$file] size[$size] isDir[$directorySync]");
		if(is_null($size))
		{
			clearstatcache();
			if($directorySync)
				$size=KBatchBase::foldersize($file);
			else
				$size = kFile::fileSize($file);
			if($size === false)
			{
				KalturaLog::debug("Size not found on file [$file]");
				return false;
			}
		}

		$retries = (self::$taskConfig->fileExistReties ? self::$taskConfig->fileExistReties : 1);
		$interval = (self::$taskConfig->fileExistInterval ? self::$taskConfig->fileExistInterval : 5);

		while($retries > 0)
		{
			$check = self::$kClient->batch->checkFileExists($file, $size);
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

		KalturaLog::log("Passed max retries");
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
		$type = self::$taskConfig->type;
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
		$killConfig->maxIdleTime = self::$taskConfig->getMaxIdleTime();
		$killConfig->sleepTime = self::$taskConfig->getMaxIdleTime() / 2;
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
		$killConfig->dwhPath = self::$taskConfig->getDwhPath();
		$killConfig->dwhEnabled = self::$taskConfig->getDwhEnabled();

		$phpPath = 'php'; // TODO - get it from somewhere
		$killerPath = $this->getMonitorPath();
		$killerPathStr = base64_encode(serialize($killConfig));

		$cmdLine = "$phpPath $killerPath $killerPathStr";

		$descriptorspec = array(); // stdin is a pipe that the child will read from
		$other_options = array('suppress_errors' => FALSE, 'bypass_shell' => FALSE);

		KalturaLog::log("Now executing [$cmdLine]");
		KalturaLog::debug('Starting monitor');
		$this->monitorHandle = proc_open($cmdLine, $descriptorspec, $pipes, null, null, $other_options);
	}

	protected function stopMonitor()
	{
		if(!$this->monitorHandle || !is_resource($this->monitorHandle))
			return;

		KalturaLog::debug('Stoping monitor');

		$status = proc_get_status($this->monitorHandle);
		if($status['running'] == true)
		{
			proc_terminate($this->monitorHandle, 9); //9 is the SIGKILL signal
			proc_close($this->monitorHandle);

			$pid = $status['pid'];
			if(!is_numeric($pid))
				throw new Exception("Non numeric PID was supplied. " . $pid);

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
	public static function pollingFileExists($fileName)
	{
		$retries = (self::$taskConfig->inputFileExistRetries ? self::$taskConfig->inputFileExistRetries : 10);
		$interval = (self::$taskConfig->inputFileExistInterval ? self::$taskConfig->inputFileExistInterval : 5);

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

	/**
	 * @param string $path path to encrypted file 
	 * @param string $key
	 * @return string the new temp clear file path
	 */
	public static function createTempClearFile($path, $key)
	{
		$iv = self::getIV();
		$tempPath =  sys_get_temp_dir(). "/clear_" . pathinfo($path, PATHINFO_BASENAME);
		KalturaLog::info("Creating tempFile with Key is: [$key] iv: [$iv] for path [$path] at [$tempPath]");
		$plainData = kEncryptFileUtils::getEncryptedFileContent($path, $key, $iv);
		kFileBase::setFileContent($tempPath, $plainData);
		return $tempPath;
	}
	
	public static function getIV()
	{
		return kConf::get("encryption_iv");
	}
}
