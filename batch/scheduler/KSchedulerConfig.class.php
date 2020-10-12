<?php
/**
 * @package Scheduler
 *
 */

class KSchedulerConfig extends Zend_Config_Ini
{
	const EXTENSION_SEPARATOR = '@';
	const DEFAULT_CONFIG_RELOAD_INTVERAL = 30;
	const HOSTNAME_WILDCARD = '#';

	/**
	 * @var host name as initiated by self::getHostname()
	 */
	private static $hostname = null;

	/**
	 * @var string file path or directory path to configuration
	 */
	private $configFileName;

	/**
	 * @var array<string> configuration file paths
	 */
	private $configFilePaths;

	/**
	 * @var array<KSchedularTaskConfig>
	 */
	private $taskConfigList = array();

	/**
	 * @var int
	 */
	private $configTimestamp;

	/**
	 * @var int
	 */
	private $nextConfigReloadTime;

	/**
	 * @var int
	 */
	private $configReloadInterval;

	/**
	 * @var bool
	 */
	private $loadConfigFromDisc;

	/**
	 * @var KalturaClient
	 */
	private $kClient;

	/**
	 * @var array
	 */
	private $kClientConfig;

	/**
	 * @var string
	 */
	private $currentIniMd5;

	/**
	 * @var bool
	 */
	public $errorLoading = false;

	/**
	 * @var int
	 */
	private $schedulerId;

	/**
	 * @param string $configFileName
	 */
	public function __construct($configFileName)
	{
		$this->configFileName = realpath($configFileName);
		$this->kClientConfig = kConf::getMap('batchBase', true);
		$this->loadConfigFromDisc = isset($this->kClientConfig['loadConfigFromDisc']) ? $this->kClientConfig['loadConfigFromDisc'] : false;
		$this->configReloadInterval = isset($this->kClientConfig['configReloadInterval']) ? $this->kClientConfig['configReloadInterval'] : self::DEFAULT_CONFIG_RELOAD_INTVERAL;
		$this->load();
	}

	public function load()
	{
		$hostname = self::getHostname();
		$configFileName = kEnvironment::get('cache_root_path') . DIRECTORY_SEPARATOR . 'batch' . DIRECTORY_SEPARATOR . 'config.ini';

		if ($this->loadConfigFromDisc)
		{
			KalturaLog::log('loading configuration from Disc at ' . date('H:i:s', $this->configTimestamp));
			$this->configTimestamp = $this->calculateFileTimestamp();
			if(is_dir($this->configFileName))
			{
				$this->implodeDirectoryFiles($configFileName);
			}
		}
		else
		{
			KalturaLog::log('loading configuration from server at ' . date('H:i:s', $this->configTimestamp));
			$this->configTimestamp = time();
			try
			{
				if (!$this->loadConfigFromServer($configFileName, $hostname))
				{
					return false;
				}
			}
			catch(Exception $e)
			{
				KalturaLog::alert('Error loading configuration from server! ' . $e->getMessage());
				return false;
			}
		}

		try
		{
			parent::__construct($configFileName, $hostname, true);
		}
		catch (Exception $e)
		{
			$hostNamePrefix = preg_replace('/\d+$/', self::HOSTNAME_WILDCARD , $hostname);
			parent::__construct($configFileName, $hostNamePrefix, true);
		}

		$this->name = $hostname;
		$this->hostName = $hostname;

		$this->taskConfigList = array();

		if (!$this->schedulerId)
		{
			try
			{
				if (!$this->initSchedulerId($hostname))
				{
					return false;
				}
			}
			catch(Exception $e)
			{
				KalturaLog::alert('Error loading SchedulerId from server! ' . $e->getMessage());
				return false;
			}
		}

		foreach ($this->enabledWorkers as $workerName => $maxInstances)
		{
			if (!$maxInstances)
				continue;

			$task = new KSchedularTaskConfig($configFileName, $workerName, $maxInstances);
			$task->setPartnerId($this->getPartnerId());
			$task->setSecret($this->getSecret());
			$task->setCurlTimeout($this->getCurlTimeout());
			$task->setSchedulerId($this->schedulerId);
			$task->setSchedulerName($this->getName());
			$task->setServiceUrl($this->getServiceUrl());
			$task->setS3Arn($this->getS3Arn());
			$task->setDwhPath($this->getDwhPath());
			$task->setDirectoryChmod($this->getDirectoryChmod());
			$task->setChmod($this->getChmod());
			$task->setDwhEnabled($this->getDwhEnabled());
			$task->setTimezone($this->getTimezone());
			$task->setInitOnly(false);
			$task->setRemoteServerUrl($this->getRemoteServerUrl());
			$task->setMaxIdleTime($this->getMaxIdleTime());

			$this->taskConfigList[$workerName] = $task;
		}
		return true;
	}
	/* (non-PHPdoc)
	 * @see Zend_Config_Ini::_loadIniFile()
	 */
	protected function _loadIniFile($filename)
	{
		set_error_handler(array($this, '_loadFileErrorHandler'));
		$loaded = parse_ini_file($filename, true); // Warnings and errors are suppressed
		restore_error_handler();
		// Check if there was a error while loading file
		if ($this->_loadFileErrorStr !== null)
		{
			/**
			 * @see Zend_Config_Exception
			 */
			require_once 'Zend/Config/Exception.php';
			throw new Zend_Config_Exception($this->_loadFileErrorStr);
		}

		$extensions = array();
		foreach ($loaded as $extensionName => $extension)
		{
			if (strpos($extensionName, self::EXTENSION_SEPARATOR) > 0)
			{
				$extensions[$extensionName] = $extension;
				unset($loaded[$extensionName]);
			}
		}

		$iniArray = array();
		foreach ($loaded as $key => $data)
		{
			$pieces = explode($this->_sectionSeparator, $key);
			$thisSection = trim($pieces[0]);
			switch (count($pieces))
			{
				case 1:
					$iniArray[$thisSection] = $data;
					break;

				case 2:
					$extendedSection = trim($pieces[1]);
					$iniArray[$thisSection] = array_merge(array(';extends' => $extendedSection), $data);
					break;

				default:
					/**
					 * @see Zend_Config_Exception
					 */
					require_once 'Zend/Config/Exception.php';
					throw new Zend_Config_Exception("Section '$thisSection' may not extend multiple sections in $filename");
			}
		}

		foreach ($extensions as $extensionName => $extension)
		{
			list($section, $extensionSufix) = explode(self::EXTENSION_SEPARATOR, $extensionName, 2);
			if (!isset($iniArray[$section]))
				throw new Zend_Config_Exception("Section '$section' cannot be found in $filename, '$extensionName' is invalid extension name");

			$iniArray[$section] = kEnvironment::mergeConfigItem($iniArray[$section], $extension, false, false);
		}

		return $iniArray;
	}

	static public function setHostname($hostname)
	{
		self::$hostname = $hostname;
	}

	static public function getHostname()
	{
		if (self::$hostname)
			return self::$hostname;

		if (isset($_SERVER['HOSTNAME']))
			self::$hostname = $_SERVER['HOSTNAME'];

		if (is_null(self::$hostname))
			self::$hostname = gethostname();

		if (is_null(self::$hostname))
			self::$hostname = $_SERVER['SERVER_NAME'];

		if (is_null(self::$hostname))
			die('Host name is not defined, please define environment variable named HOSTNAME');

		return self::$hostname;
	}

	/**
	 * @return bool
	 */
	public function reloadRequired()
	{
		if ($this->nextConfigReloadTime < time())
		{
			$this->nextConfigReloadTime = time() + $this->configReloadInterval;
			if ($this->loadConfigFromDisc)
			{
				// Check config path udpated
				$filePaths = $this->getCurrentConfigFilePaths();
				if ($this->getConfigFilePaths() != $filePaths)
				{
					$this->configFilePaths = $filePaths;
					return true;
				}
				// Check config content updated
				$filemtime = $this->calculateFileTimestamp();
				return ($filemtime > $this->configTimestamp);
			}
			return true;
		}
		return false;
	}

	public function getTaskConfigList()
	{
		return $this->taskConfigList;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getStatusInterval()
	{
		return $this->statusInterval;
	}

	public function getSchedulerStatusInterval()
	{
		if (isset($this->schedulerStatusInterval))
			return $this->schedulerStatusInterval;

		return 60;
	}

	public function getTasksetPath()
	{
		return $this->tasksetPath;
	}

	public function getRemoteServerUrl()
	{
		return $this->remoteServerUrl;
	}

	public function getMaxIdleTime()
	{
		return $this->maxIdleTime;
	}

	public function getLogDir()
	{
		return $this->logDir;
	}

	public function getPidFileDir()
	{
		return $this->pidFileDir;
	}

	public function getPartnerId()
	{
		return $this->partnerId;
	}

	public function getDirectoryChmod()
	{
		return $this->directoryChmod;
	}

	public function getChmod()
	{
		return $this->chmod;
	}

	public function getDwhEnabled()
	{
		return $this->dwhEnabled;
	}

	public function getDwhPath()
	{
		return $this->dwhPath;
	}

	public function getTimezone()
	{
		return $this->timezone;
	}

	public function getServiceUrl()
	{
		return $this->serviceUrl;
	}

	public function getCurlTimeout()
	{
		return $this->curlTimeout;
	}

	public function getSecret()
	{
		return $this->secret;
	}

	public function getS3Arn()
	{
		return $this->s3Arn;
	}

	/**
	 * @param string $name
	 * @return KSchedularTaskConfig
	 */
	public function getTaskConfig($name)
	{
		$taskConfig = $this->taskConfigList[$name];
		return $taskConfig;
	}

	public function getLogWorkerInterval()
	{
		return $this->logWorkerInterval;
	}

	/**
	 * @param $configFileName
	 * @param $hostname
	 * @return string
	 */
	protected function loadConfigFromServer($configFileName, $hostname)
	{
		$this->errorLoading = true;
		$iniMd5 = null;
		$this->initClient();
		$configurationPluginClient = KalturaConfMapsClientPlugin::get($this->kClient);
		if ($configurationPluginClient)
		{
			$configurationMap = $configurationPluginClient->confMaps->getBatchMap($hostname);
			if ($configurationMap)
			{
				$content = json_decode($configurationMap, true);
				if (json_last_error() == JSON_ERROR_NONE && !empty($content))
				{
					$this->errorLoading = false;
					$newIniMd5 = md5($content);
					if (!isset($this->currentIniMd5) || ($newIniMd5 && $this->currentIniMd5 != $newIniMd5))
					{
						file_put_contents($configFileName, $content);
						$this->currentIniMd5 = $newIniMd5;
						KalturaLog::log('Configuration Loaded from Server ' . date('H:i:s', $this->configTimestamp));
						$this->schedulerId = null;
						return true;
					}
					else
					{
						KalturaLog::log('No need to reload Configuration. Configuration hasn\'t changed.');
					}
				}
				else
				{
					KalturaLog::alert('Could not decode batch configuration maps');
				}
			}
			else
			{
				KalturaLog::alert('Could Not load batch configuration maps from server');
			}
		}
		else
		{
			KalturaLog::err('Could Not load Conf Maps Plugin. Please check configuration.');
		}
		return false;
	}

	protected function initClient()
	{
		if ($this->kClient)
		{
			$ks = $this->kClient->generateSession($this->kClientConfig['secret'], 'batchUser', KalturaSessionType::ADMIN, '-1');
			$this->kClient->setKs($ks);
			return;
		}
		else
		{
			$clientConfig = new KalturaConfiguration();
			$clientConfig->serviceUrl = $this->kClientConfig['serviceUrl'];
			$clientConfig->curlTimeout = $this->kClientConfig['curlTimeout'];
			$this->kClient = new KalturaClient($clientConfig );
			$this->kClient->setPartnerId($this->kClientConfig['partnerId']);
			$ks = $this->kClient->generateSession($this->kClientConfig['secret'], 'batchUser', KalturaSessionType::ADMIN, '-1');
			$this->kClient->setKs($ks);
		}
	}

	protected function implodeDirectoryFiles($path)
	{
		$content = '';

		$configFilePaths = $this->getConfigFilePaths();
		foreach($configFilePaths as $configFilePath)
			$content .= file_get_contents($configFilePath) . "\n";

		file_put_contents($path, $content);
	}

	protected function calculateFileTimestamp()
	{
		clearstatcache();
		if(!is_dir($this->configFileName)) {
			return filemtime($this->configFileName);
		}

		$configFilePaths = $this->getConfigFilePaths();

		$filemtime = 0;
		foreach($configFilePaths as $configFilePath)
			$filemtime = max($filemtime, filemtime($configFilePath));

		return $filemtime;
	}

	protected function getConfigFilePaths()
	{
		if(!is_dir($this->configFileName))
			return $this->configFileName;

		if(!$this->configFilePaths)
			$this->configFilePaths = $this->getCurrentConfigFilePaths();

		return $this->configFilePaths;
	}

	protected function getCurrentConfigFilePaths()
	{
		if(!is_dir($this->configFileName))
			return  $this->configFileName;

		$configFilePaths = array();
		$d = dir($this->configFileName);

		while (false !== ($file = $d->read()))
		{
			if(preg_match('/\.ini$/', $file))
				$configFilePaths[] = $this->configFileName . DIRECTORY_SEPARATOR . $file;
		}
		$d->close();

		return $configFilePaths;
	}

	/**
	 * @param $hostname
	 * @return bool
	 * @throws KalturaClientException
	 */
	protected function initSchedulerId($hostname)
	{
		if (!is_null($this->getId()))
		{
			$this->schedulerId = $this->getId();
			return true;
		}

		KalturaLog::debug("Scheduler ID not configured for hostname $hostname . Trying to retrieve scheduler id from server.");
		$kalturaScheduler = new KalturaScheduler();
		$kalturaScheduler->host = $hostname;
		$kalturaScheduler->name = $hostname;
		$scheulder = $this->kClient->batchcontrol->getOrCreateScheduler($kalturaScheduler);
		if (!$scheulder)
		{
			KalturaLog::alert('No Scheduler has be returned from server!');
			return false;
		}
		if (is_null($scheulder->configuredId))
		{
			KalturaLog::alert('Empty schedulerId was retrieved from server for scheudler ' . print_r($scheulder, true));
			return false;
		}
		KalturaLog::debug("Retrieved ID - " . $scheulder->configuredId);
		$this->schedulerId = $scheulder->configuredId;
		return true;
	}
}

