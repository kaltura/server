<?php
/**
 * @package plugins.symantecScanEngine
 * @subpackage lib
 */
class SymantecScanJavaEngine extends SymantecScanEngine
{
	const STATUS_PREFIX = 'Scan Status: ';
	const UNABLE_TO_SEND_DATA_TO_THE_SERVER = 'ERROR: Unknown error in execution : Unable to send data to the server.';
	const NUM_OF_ATTEMPTS = 5;
	
	private $binFile = 'java -jar /opt/SYMCScan/ssecls/ssecls.jar';
	
	/**
	 * This function should be used to let the engine take specific configurations from the batch job parameters.
	 * For example - command line of the relevant binary file.
	 * @param unknown_type $paramsObject Object containing job parameters
	 */
	public function config($paramsObject)
	{
		if (!isset($paramsObject->symantecScanJavaEngineCmd))
		{
			return false;
		}
		$this->binFile = $paramsObject->symantecScanJavaEngineCmd;
		return true;
	}
	
	function isEngineRunning()
	{
		exec('netstat -na | grep :1344 | grep LISTEN | grep -i tcp', $output);
		return ($output ? true : false);
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
			$errorDescription = 'Engine command line not set';
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		if (!file_exists($filePath)) {
			$errorDescription = 'Source file does not exists ['.$filePath.']';
			return KalturaVirusScanJobResult::SCAN_ERROR;
		}
		
		$scanMode = $cleanIfInfected ? 'scanrepair' : 'scan';
		$logFile = "$filePath.virusScan.log";
		$cmd = $this->binFile . " --action $scanMode --verbose -f $filePath > $logFile 2>&1";

		$errorDescription = null;
		$output = null;
		
		
		KalturaLog::info("Executing - [$cmd]");
		
		for($tries = 1; $tries <= self::NUM_OF_ATTEMPTS; $tries ++) {
			while (!$this->isEngineRunning())
			{
				KalturaLog::info("Symantec engine not running, retrying in 10 seconds");
				sleep(10);
			}
			system ( $cmd, $return_value );
			$output = file ( $logFile );
			if (!count ( $output ) || strpos($output [0],self::UNABLE_TO_SEND_DATA_TO_THE_SERVER) === false )
				break;
			KalturaLog::info ( "Retrying scan.attempt number:".$tries );
			sleep ( 10 );
		}
		
		$found = false;
		foreach ($output as $line)
		{
			$matches = null;
			if(preg_match('/^ERROR: (.+)/', $line, $matches))
			{
				$errorDescription = $matches[1];
				return KalturaVirusScanJobResult::SCAN_ERROR;
			}
			
			if (kString::beginsWith($line, self::STATUS_PREFIX)) {
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
		
		$returnValue = trim(substr($found, strlen(self::STATUS_PREFIX)));
		
		if ($returnValue == 'Clean')
		{
			return KalturaVirusScanJobResult::FILE_IS_CLEAN;
		}
		else if ($returnValue == 'Repaired')
		{
			return KalturaVirusScanJobResult::FILE_WAS_CLEANED;
		}
		else if ($returnValue == 'Infected')
		{
			return KalturaVirusScanJobResult::FILE_INFECTED;
		}
		else 
		{ 
			$errorDescription = "Unknown returned value from virus scanner";
		}

		return KalturaVirusScanJobResult::SCAN_ERROR;
	}

}
