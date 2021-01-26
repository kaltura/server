<?php

/**
 * @package plugins.clamAvScanEngine
 * @subpackage batch
 */
class ClamAVScanEngine extends VirusScanEngine
{
	
	private $binFile = null;
	
	private $runWrapped = false;
	
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
		
		if(isset($paramsObject->runWrapped) && $paramsObject->runWrapped == true)
		{
			KalturaLog::err('Process will run using stream wrapper');
			$this->runWrapped = true;
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
		
		if (!kFile::checkFileExists($filePath)) {
			$errorDescription = "Source file does not exists [$filePath]";
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		$clamAvScanWrapper = new ClamAVScanWrapper($this->binFile, $filePath, $this->runWrapped);
		list($return_value, $output, $errorDescription) = $clamAvScanWrapper->execute();
		
		$statusLine = false;
		foreach ($output as $line)
		{
			if (strpos($line, $filePath) === 0 || strpos($line, kFile::realPath($filePath)) === 0)
			{
				$statusLine = $line;
				break;
			}
		}
		$output = implode(PHP_EOL, $output);
		
		if (!$statusLine)
		{
			$errorDescription = "Unknown error - return value [$return_value] errorDescription [$errorDescription]";
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
