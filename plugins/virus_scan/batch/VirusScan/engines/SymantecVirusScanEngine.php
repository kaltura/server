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
	 * Will execute the virus scan for the given file path
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
		
		//TODO: remove
		return KalturaVirusScanJobResult::FILE_IS_CLEAN;
		
		$scanMode = $cleanIfInfected ? '-mode scanrepair' : '-mode scan';
		$cmd = $this->binFile . ' -verbose -details -timing ' . $scanMode . ' ' . $filePath;

		$returnValue = null;
		$output = null;
		
		$output = system($cmd, $return_value);		

		
	}
	
}