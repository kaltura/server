<?php

class SymantecScanEngine extends VirusScanEngine
{
	
	private $binFile = null;
	
	/**
	 * This function should be used to let the engine take specific configurations from the batch job parameters.
	 * For example - command line of the relevant binary file.
	 * @param unknown_type $paramsObject Object containing job parameters
	 */
	public function config($paramsObject)
	{
		if (!isset($paramsObject->symantecScanEngineBin))
		{
			return false;
		}
		$this->binFile = $paramsObject->symantecScanEngineBin;
		return true;
	}
	
	/**
	 * Will execute the virus scan for the given file path and return the output from virus scanner program
	 * and the error description
	 * @param string $filePath
	 * @param boolean $cleanIfInfected
	 * @param string $errorDescription
	 */
	public function execute($filePath, $cleanIfInfected, &$output, &$errorDescription)
	{
		if (!$this->binFile)
		{
			$errorDescriptiong = 'Engine binary file not set';
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		if (!file_exists($filePath)) {
			$errorDescriptiong = 'Source file does not exists ['.$filePath.']';
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		clearstatcache();
		$fileLastChanged = filemtime($filePath);
		
		$scanMode = $cleanIfInfected ? '-mode scanrepair' : '-mode scan';
		$cmd = $this->binFile . ' -verbose ' . $scanMode . ' ' . $filePath;

		$returnValue = null;
		$errorDescription = null;
		$output = null;
		
		KalturaLog::debug("Executing - [$cmd]");
		exec($cmd, $output, $return_value);
		
		if ($returnValue != 0)	// command line error
		{	
			$errorDescription = "Error executing command [$cmd] - return value [$returnValue]";
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		$found = false;
		foreach ($output as $line)
		{
			if (strpos($line, $filePath) === 0) {
				$found = $line;
				break;
			}
		}
		$output = implode(PHP_EOL, $output);
		
		if (!$found)
		{
			$errorDescription = 'Unknown error';
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		$found = explode(' ', $found);
		$returnValue = trim(end($found));
		
		if ($returnValue == '0')
		{
			clearstatcache();
			if ($fileLastChanged && $fileLastChanged != filemtime($filePath)) {
				return KalturaVirusScanJobResult::FILE_WAS_CLEANED;
			}
			else {
				return KalturaVirusScanJobResult::FILE_IS_CLEAN;
			}
		}
		else if ($returnValue == '1' || $returnValue == '2') {
			$errorDescription = "The file was found infected, but was not repaired";
			return KalturaVirusScanJobResult::FILE_INFECTED;
		}
		else if ($returnValue == '-2') {
			$errorDescription = "An error occurred within Symantec Scan Engine. The file was not scanned.";
		}
		else if ($returnValue == '-1') {
			$errorDescription = "An error occurred within the command-line scanner. The file was not scanned.";
		}
		else { 
			$errorDescription = "Unknown returned value from virus scanner";
		}

		return KalturaVirusScanJobResult::SCAN_ERROR;
	}

}