<?php
/**
 * base class for the real ConversionEngines in the system - ffmpeg,menconder and flix. 
 * 
 * @package Scheduler
 * @subpackage Conversion
 */
class KSingleOutputOperationEngine extends KOperationEngine
{
	/**
	 * @var string
	 */
	protected $outFilePath;
	
	protected function getCmdLine()
	{
		if(isset($this->configFilePath)){
			$xml = file_get_contents($this->configFilePath);
			$xml = str_replace(
				array(KDLCmdlinePlaceholders::OutDir,KDLCmdlinePlaceholders::OutFileName),
				array($this->outDir,$this->outFilePath),
				$xml);
			file_put_contents($this->configFilePath, $xml);
		}
		
		$command = '';
		$exec_cmd = $this->cmd;
		$inputFilePath = kFile::buildDirectUrl($this->inFilePath);
		kBatchUtils::addReconnectParams("http", $inputFilePath,$exec_cmd);
		
		if($this->operator && $this->operator->command)
		{
			$command = str_replace (
				array(KDLCmdlinePlaceholders::InFileName, KDLCmdlinePlaceholders::OutFileName, KDLCmdlinePlaceholders::ConfigFileName, KDLCmdlinePlaceholders::BinaryName),
				array('"' . $inputFilePath . '"', $this->outFilePath, $this->configFilePath, $exec_cmd),
				$this->operator->command);
		}
		
		return "$exec_cmd $command >> \"{$this->logFilePath}\" 2>&1";
	}

	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd);
		
		$this->outFilesPath[] = $outFilePath;
		$this->outFilePath = $outFilePath;
		$this->logFilePath = "$outFilePath.log";
	}
}


