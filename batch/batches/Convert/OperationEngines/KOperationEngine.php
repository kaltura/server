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
	 * @var string
	 */
	protected $encryptionKey = null;
	
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
	
	/**
	 * @var KalturaConvartableJobData
	 */
	protected $data = null;

	/**
	 * @var KalturaBatchJob
	 */
	protected $job = null;
	
	protected function __construct($cmd = null)
	{
		$this->cmd = $cmd;
	}
	
	abstract protected function getCmdLine();

	public function configure(KalturaConvartableJobData $data, KalturaBatchJob $job)
	{
		$this->data = $data;
		$this->job = $job;
		$this->setMediaInfoEnabled(KBatchBase::$taskConfig->params->mediaInfoEnabled);

		KalturaLog::info("taskConfig-->".print_r(KBatchBase::$taskConfig,true)."\ndata->".print_r($data,true));
	}

	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		$this->operator = $operator;
		$this->inFilePath = $inFilePath;
		$this->configFilePath = $configFilePath;
		
		$status = $this->doOperation();
		if($status === false) 	return false; //return false if the engine has a closer
		else 					return true;
	}	
	
	public function closeOperation()
	{
		return $this->doCloseOperation();
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
		$this->operationComplete($return_value, $output);
			/*
	 		 * If operator is defined as 'optional', upon execution failure the operator 
			 * will copy the source to the output, rather than fail and halt the flavor execution 
			 */	
		if($return_value != 0) {
			if(isset($this->operator) && isset($this->operator->isOptional) && $this->operator->isOptional>0){
				$msg = "Operator failed with return value: [$return_value]";
				if(isset($this->message)) $msg.= ", message :[".$this->message."]"; 
				$msg.= ".Operator is defined as optional, therefore switching to passthrough mode - copy the source to output.";
				$this->message = $msg;
				copy($this->inFilePath, $this->outFilePath);
			}
			else 
				throw new KOperationEngineException("return value: [$return_value]");
		}
		$this->logMediaInfo($this->outFilesPath);
	}
	
	protected function operationComplete($rc, $output) {
		return;	
	}
	
	protected function doCloseOperation()
	{
		return true;
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
			if(($filePath !== FALSE) && (file_exists($filePath)))
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
		kFile::appendToFile($this->logFilePath, $str);
	}
	
	/**
	 * @throws KOperationEngineException
	 */
	protected function validateFormat($expectedFormat)
	{
		$inputFormat = $this->getInputFormat();
		if($inputFormat != $expectedFormat)
			throw new KOperationEngineException("File [$this->inFilePath] is of wrong format [$inputFormat], expecting [$expectedFormat]");
	}
	
	/**
	 * Executing file on the input path
	 * @return string
	 */
	protected function getInputFormat()
	{
		$returnValue = null;
		$output = null;
		$matches = null;
		
		if(realpath($this->inFilePath) === FALSE)
			throw new KOperationEngineException("Illegal input file was supplied.");
		
		$command = "file '{$this->inFilePath}'";
		KalturaLog::debug("Executing: $command");
		exec($command, $output, $returnValue);
		if($returnValue == 0 && preg_match("/^[^:]+: ([^,]+),/", reset($output), $matches))
		{
			$type = $matches[1];
			KalturaLog::info("file [{$this->inFilePath}] type [$type]");
			return $type;
		}
		return null;
	}


	protected function getSrcActualPathFromData()
	{
		$srcFileSyncDescriptor = reset($this->data->srcFileSyncs);
		if($srcFileSyncDescriptor)
			return $srcFileSyncDescriptor->actualFileSyncLocalPath;		
		else
			return null;
	}
	
	protected function getSrcRemoteUrlFromData()
	{
		$srcFileSyncDescriptor = reset($this->data->srcFileSyncs);
		if($srcFileSyncDescriptor)
			return $srcFileSyncDescriptor->fileSyncRemoteUrl;		
		else
			return null;
	}
	
	public function getData()
	{
		return $this->data;
	}

	public function setEncryptionKey($key)
	{
		$this->encryptionKey = $key;
	}
}


