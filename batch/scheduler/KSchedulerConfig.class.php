<?php
/**
 * @package Scheduler
 * 
 */

class KSchedulerConfig extends Zend_Config_Ini
{
    const SECTION_SEPARATOR = ':';
    
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
		if(is_dir($this->configFileName))
		{
			$configFileName = kConf::get('cache_root_path') . DIRECTORY_SEPARATOR . 'batch' . DIRECTORY_SEPARATOR . 'config.ini';
			$this->implodeDirectoryFiles($configFileName);
		}
		
		$hostname = self::getHostname();
		parent::__construct($configFileName, $hostname);
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
			$task->setDwhEnabled($this->getDwhEnabled());
			$task->setTimezone($this->getTimezone());
			$task->setInitOnly(false);
			$task->setMaxIdleTime($this->getMaxIdleTime());
			
			$this->taskConfigList[$workerName] = $task;
	  	}
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
		if(!is_dir($this->configTimestamp))
			return filemtime($this->configFileName);
		
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

		if($this->configFilePaths)
			return $this->configFilePaths;
		
		$ignore = array(
			'.',
			'..',
			'.svn'
		);
		
		$d = dir($this->configFileName);
		while (false !== ($file = $d->read())) 
		{
			if(!in_array($file, $ignore))
				$this->configFilePaths[] = $this->configFileName . DIRECTORY_SEPARATOR . $file;
		}
		$d->close();
		
		return $this->configFilePaths;
	}
	
	public function reloadRequired()
	{
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
	
	public function getMaxIdleTime()
	{
		return $this->maxIdleTime;
	}
	
	public function getLogDir()
	{
		return $this->logDir;
	}
	
	public function getPartnerId()
	{
		return $this->partnerId;
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
}

