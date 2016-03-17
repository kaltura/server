<?php
/**
 * @package Scheduler
 *
 */

class KSchedulerConfig extends Zend_Config_Ini
{
    const EXTENSION_SEPARATOR = '@';

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
	 * @var int
	 */
	private $configTimestamp;

	/**
	 * @var array<KSchedularTaskConfig>
	 */
	private $taskConfigList = array();

	/**
	 * @param string $configFileName
	 */
	public function __construct($configFileName)
	{
		$this->configFileName = realpath($configFileName);
		$this->load();
	}

	public function load()
	{
		$this->configTimestamp = $this->calculateFileTimestamp();

		$configFileName = $this->configFileName;
		KalturaLog::log("loading configuration $configFileName at ".$this->configTimestamp);

		if(is_dir($this->configFileName))
		{
			$configFileName = kEnvironment::get('cache_root_path') . DIRECTORY_SEPARATOR . 'batch' . DIRECTORY_SEPARATOR . 'config.ini';
			$this->implodeDirectoryFiles($configFileName);
		}

		$hostname = self::getHostname();
		parent::__construct($configFileName, $hostname, true);
		$this->name = $hostname;
		$this->hostName = $hostname;

		$this->taskConfigList = array();
		foreach($this->enabledWorkers as $workerName => $maxInstances)
		{
			if(!$maxInstances)
				continue;

			$task = new KSchedularTaskConfig($configFileName, $workerName, $maxInstances);
			$task->setPartnerId($this->getPartnerId());
			$task->setSecret($this->getSecret());
			$task->setCurlTimeout($this->getCurlTimeout());
			$task->setSchedulerId($this->getId());
			$task->setSchedulerName($this->getName());
			$task->setServiceUrl($this->getServiceUrl());
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
        if ($this->_loadFileErrorStr !== null) {
            /**
             * @see Zend_Config_Exception
             */
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception($this->_loadFileErrorStr);
        }
    
        $extensions = array();
        foreach($loaded as $extensionName => $extension)
        {
        	if(strpos($extensionName, self::EXTENSION_SEPARATOR) > 0)
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
            switch (count($pieces)) {
                case 1:
                    $iniArray[$thisSection] = $data;
                    break;

                case 2:
                    $extendedSection = trim($pieces[1]);
                    $iniArray[$thisSection] = array_merge(array(';extends'=>$extendedSection), $data);
                    break;

                default:
                    /**
                     * @see Zend_Config_Exception
                     */
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Section '$thisSection' may not extend multiple sections in $filename");
            }
        }
    
        foreach($extensions as $extensionName => $extension)
        {
        	list($section, $extensionSufix) = explode(self::EXTENSION_SEPARATOR, $extensionName, 2);
        	if(!isset($iniArray[$section]))
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
		if(self::$hostname)
			return self::$hostname;

		if(isset($_SERVER['HOSTNAME']))
			self::$hostname = $_SERVER['HOSTNAME'];

		if(is_null(self::$hostname))
			self::$hostname = gethostname();

		if(is_null(self::$hostname))
			self::$hostname = $_SERVER['SERVER_NAME'];

		if(is_null(self::$hostname))
			die('Host name is not defined, please define environment variable named HOSTNAME');

		return self::$hostname;
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

	public function reloadRequired()
	{
		// Check config path udpated
		$filePaths = $this->getCurrentConfigFilePaths();
		if($this->getConfigFilePaths() != $filePaths) {
			$this->configFilePaths = $filePaths;
			return true;
		}
		
		// Check config content updated
		$filemtime = $this->calculateFileTimestamp();
		return ($filemtime > $this->configTimestamp);
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
		if(isset($this->schedulerStatusInterval))
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
}

