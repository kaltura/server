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
		if(isset($this->configFilePath))
		{
			$xml = kFile::getFileContent($this->configFilePath);
			$xml = str_replace(
					array(KDLCmdlinePlaceholders::OutDir,KDLCmdlinePlaceholders::OutFileName), 
					array($this->outDir,$this->outFilePath), 
					$xml);
			kFile::getFileContent($this->configFilePath, $xml);
		}
		
		$command = '';
		if($this->operator && $this->operator->command)
		{
			$command = str_replace ( 
				array(KDLCmdlinePlaceholders::InFileName, KDLCmdlinePlaceholders::OutFileName, KDLCmdlinePlaceholders::ConfigFileName, KDLCmdlinePlaceholders::BinaryName), 
				array($this->inFilePath, $this->outFilePath, $this->configFilePath, $this->cmd),
				$this->operator->command);
		}
				
		return "{$this->cmd} $command >> \"{$this->logFilePath}\" 2>&1";
	}

	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd);
		
		$this->outFilesPath[] = $outFilePath;
		$this->outFilePath = $outFilePath;
		$this->logFilePath = "$outFilePath.log";
	}
}


