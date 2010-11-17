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
		
		$scanMode = $cleanIfInfected ? '-mode scanrepair' : '-mode scan';
		$cmd = $this->binFile . ' -verbose ' . $scanMode . ' ' . $filePath;

		$returnValue = null;
		$errorDescription = null;
		$output = null;
		
		exec($cmd, $output, $return_value);
		$output = implode(PHP_EOL, $output);
		if ($returnValue != 0)	// command line error
		{	
			$errorDescription = "Error executing command [$cmd]";
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		$firstLine = $output[0];
		$lineArgs = preg_split('/\s+/', $firstLine);
		$returnValue = $lineArgs[count($lineArgs) - 2];
		
		switch ($returnValue)
		{
			case -2:
				$errorDescription = "An error occurred within Symantec Scan Engine. The file was not scanned.";
				return KalturaVirusScanJobResult::SCAN_ERROR;
			case -1:
				$errorDescription = "An error occurred within the command-line scanner. The file was not scanned.";
				return KalturaVirusScanJobResult::SCAN_ERROR;
			case 0:
				return KalturaVirusScanJobResult::FILE_IS_CLEAN;
			case 1:
			case 2:
				$errorDescription = "The file was found infected, but was not repaired";
				return KalturaVirusScanJobResult::FILE_INFECTED;
			default:
				$errorDescription = "Unknown returned value from virus scanner";
				return KalturaVirusScanJobResult::SCAN_ERROR;			
		}
	}
	
}