<?php
/**
 * 
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KOperationEngineQtTools  extends KSingleOutputOperationEngine
{
	protected $tmpFolder;
	
	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data)
	{
		parent::configure($taskConfig, $data);
		$this->tmpFolder = $taskConfig->params->localTempPath;
	}
	
	public function operate(kOperator $operator = null, $inFilePath, $logFilePath, $configFilePath = null)
	{
		$qtInFilePath = "$this->tmpFolder/$inFilePath.stb";
		if(symlink($inFilePath, $qtInFilePath))
			$inFilePath = $qtInFilePath;
		
		parent::operate($operator, $inFilePath, $logFilePath, $configFilePath);
	}
}
