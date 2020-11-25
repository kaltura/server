<?php

/**
 * @package plugins.clamAvScanEngine
 * @subpackage batch
 */
class ClamAVScanEngine extends VirusScanEngine
{
	
	private $binFile = null;
	
	/**
	 * This function should be used to let the engine take specific configurations from the batch job parameters.
	 * For example - command line of the relevant binary file.
	 * @param unknown_type $paramsObject Object containing job parameters
	 */
	public function config($paramsObject)
	{
		if (!isset($paramsObject->clamAvScanEngineBin))
		{
		    KalturaLog::err('Binary file configuration not found');
			return false;
		}
		$this->binFile = $paramsObject->clamAvScanEngineBin;
		return true;
	}
	
	/**
	 * Will execute the virus scan for the given file path and return the output from virus scanner program
	 * and the error description
	 * @param string $filePath
	 * @param boolean $cleanIfInfected - not supported in ClamAV since files cannot be cleaned
	 * @param string $errorDescription
	 */
	public function execute($filePath, $cleanIfInfected, &$output, &$errorDescription)
	{
		if (!$this->binFile)
		{
			$errorDescription = 'Engine binary file not set';
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		if (!file_exists($filePath)) {
			$errorDescription = 'Source file does not exists ['.$filePath.']';
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		clearstatcache();
		$fileLastChanged = filemtime($filePath);
		
		$cmd = $this->binFile . ' --verbose ' . $filePath;

		$errorDescription = null;
		$output = null;
		
		KalturaLog::info("Executing - [$cmd]");
		exec($cmd, $output, $return_value);
				
		$statusLine = false;
		foreach ($output as $line)
		{
			if (strpos($line, $filePath) === 0 || strpos($line, realpath($filePath)) === 0)
			{
				$statusLine = $line;
				break;
			}
		}
		$output = implode(PHP_EOL, $output);
		
		if (!$statusLine)
		{
			$errorDescription = 'Unknown error - return value ['.$return_value.']';
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}

		$statusLineArr = explode(' ', $statusLine);
		$scanStatus = trim(end($statusLineArr));
		
		if ($scanStatus == 'OK' || strpos($statusLine , 'Empty file') != 0)
		{
			return KalturaVirusScanJobResult::FILE_IS_CLEAN;
		}
		else if ($scanStatus == 'FOUND')
		{
			$errorDescription = "The file was found infected, but was not repaired";
			return KalturaVirusScanJobResult::FILE_INFECTED;
		}
		else
		{
		    $errorDescription = $statusLine;
		    return KalturaVirusScanJobResult::SCAN_ERROR;
		}		
	}

}
