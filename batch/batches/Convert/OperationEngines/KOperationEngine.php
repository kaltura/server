<?php
/**
 * base class for the real ConversionEngines in the system - ffmpeg,menconder and flix. 
 * 
 * @package Scheduler
 * @subpackage Conversion
 */
abstract class KOperationEngine
{
	/**
	 * @var kOperator
	 */
	protected $operator = null;
	
	/**
	 * @var string
	 */
	protected $inFilePath = null;
	
	/**
	 * @var array
	 */
	protected $outFilesPath = array();
	
	/**
	 * @var string
	 */
	protected $configFilePath = null;
	
	/**
	 * @var string
	 */
	protected $logFilePath = null;
	
	/**
	 * @var string
	 */
	protected $message = null;
	
	/**
	 * @var string
	 */
	protected $cmd = null;
	
	/**
	 * @var bool
	 */
	protected $mediaInfoEnabled = false;
	
	protected function __construct($cmd = null)
	{
		$this->cmd = $cmd;
	}
	
	abstract protected function getCmdLine();
	
	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data)
	{
		$this->setMediaInfoEnabled($taskConfig->params->mediaInfoEnabled);
	}
	
	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		$this->operator = $operator;
		$this->inFilePath = $inFilePath;
		$this->configFilePath = $configFilePath;
		
		$this->doOperation();
	}	
	
	protected function doOperation()
	{
		if(!file_exists($this->inFilePath))
			throw new KOperationEngineException("File [$this->inFilePath] does not exist");

		$cmd = $this->getCmdLine();
		
		$this->addToLogFile("Executed by [" . get_class($this) . "] on input file [$this->inFilePath]");
		$this->addToLogFile($cmd, KalturaLog::INFO);
		$this->logMediaInfo($this->inFilePath);
				
	
		$start = microtime(true);
		$output = system($cmd, $return_value);		
		$end = microtime(true);
	
		$duration = ( $end - $start );
						 
		$this->addToLogFile(get_class($this) . ": [$return_value] took [$duration] seconds", KalturaLog::INFO);
		$this->addToLogFile($output);
			
		if($return_value != 0) 
			throw new KOperationEngineException("return value: [$return_value]");
			
		$this->logMediaInfo($data->destFileSyncLocalPath);
	}
	
	/**
	 * @param bool $enabled
	 */
	public function setMediaInfoEnabled($enabled)
	{
		$this->mediaInfoEnabled = $enabled;
	}
	
	
	/**
	 * @param string $filePath
	 */
	protected function logMediaInfo($filePath)
	{
		if(!$this->mediaInfoEnabled)
			return;
			
		try
		{
			$filePath = realpath($filePath);
			if(file_exists($filePath))
			{
				system("mediainfo $filePath >> \"{$this->logFilePath}\" 2>&1");
			}
			else
			{
				$this->addToLogFile("Cannot find file [$filePath]") ;
			}
		}
		catch(Exception $ex)
		{
			$this->addToLogFile($ex->getMessage()) ;
		}		
	}
	
	/**
	 * @return array<int,string> in the form of array[bitrate] = path
	 */
	public function getOutFilesPath()
	{
		return $this->outFilesPath;
	}
	
	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * @return string
	 */
	public function getLogData()
	{
		return file_get_contents($this->logFilePath);
	}
	
	/**
	 * @return string
	 */
	public function getLogFilePath()
	{
		return $this->logFilePath;
	}
	
	/**
	 * @param string $str
	 */
	protected function addToLogFile($str, $priority = KalturaLog::DEBUG)
	{
		KalturaLog::log($str, $priority);
		file_put_contents($this->logFilePath, $str, FILE_APPEND);
	}

}


