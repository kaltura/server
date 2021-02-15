<?php

/**
 * @package plugins.clamAvScanEngine
 * @subpackage batch
 */
class ClamAVScanWrapper
{
		/**
	 * Original cmd bin path
	 * @var $binPath string
	 */
	protected $binPath;
	
	/**
	 * File path to scan
	 * @var $filePath string
	 */
	protected $filePath;
	
	/**
	 * Should run clamdscan using proc wrapper
	 * @var $runWrapped boolean
	 */
	protected $runWrapped;
	
	public function __construct($binPath, $filePath, $runWrapped = false)
	{
		$this->binPath = $binPath;
		$this->filePath = $filePath;
		$this->runWrapped = $runWrapped;
	}
	
	public function execute()
	{
		if(!$this->runWrapped)
		{
			return $this->runDirectCmd();
		}
		
		return $this->runWrapped();
	}
	
	protected function runDirectCmd()
	{
		$errorDescription = $output = null;
		$cmd = $this->binPath . ' --verbose ' . $this->filePath;
		
		KalturaLog::info("Executing - [$cmd]");
		exec($cmd, $output, $return_value);
		
		return array($return_value, $output, "");
	}
	
	protected function runWrapped()
	{
		$cmd = $this->binPath . ' --verbose -';
		list($return_value, $output, $errorDescription) = kExecWrapper::runWrapped($cmd, $this->filePath);
		
		//We are piping the in stream so we should replace stream in the output with the actual file path
		$output = str_replace("stream:", $this->filePath.":", $output);
		$output = explode("\n", $output);
		return array($exitCode, $output, $procErr);
	}
}
