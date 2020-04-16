<?php
/**
 * base class for the real ConversionEngines in the system - ffmpeg,menconder and flix. 
 * 
 * @package Scheduler
 * @subpackage Conversion.engines
 */
class KOperationEngineOldVersionWrapper extends KOperationEngine
{
	/**
	 * @var KConversionEngine
	 */
	protected $convertor;
	
	public function __construct($type, KalturaConvartableJobData $data)
	{
		$this->convertor = KConversionEngine::getInstance($type);
		$this->logFilePath = $data->destFileSyncLocalPath . ".log";
	}

	protected function doOperation()
	{
		list($ok, $errorMessage) = $this->convertor->convert($this->data, $this->job->id);
		if(!$ok)
			throw new KOperationEngineException($errorMessage);
	}
	
	/**
	 * @param bool $enabled
	 */
	public function setMediaInfoEnabled($enabled)
	{
		$this->convertor->setMediaInfoEnabled($enabled);
	}
	
	/* (non-PHPdoc)
	 * @see KOperationEngine::getLogFilePath()
	 */
	public function getLogFilePath()
	{
		return $this->convertor->getLogFilePath();
	}
	
	/* (non-PHPdoc)
	 * @see KOperationEngine::getLogData()
	 */
	public function getLogData()
	{
		return $this->convertor->getLogData();
	}
	
	protected function getCmdLine(){}
}


