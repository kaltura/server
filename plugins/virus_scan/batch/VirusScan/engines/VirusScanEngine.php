<?php

abstract class VirusScanEngine
{
		
	/**
	 * Return a new instance of a class extending VirusScanEngine, according to give $type
	 * @param KalturaVirusScanEngineType $type
	 */
	public static function getEngine($type)
	{
		switch ($type)
		{
			case KalturaVirusScanEngineType::SYMANTEC_SCAN_ENGINE:
				return new SymantecScanEngine();
		}
		return null;
	}
	
	
	/**
	 * This function should be used to let the engine take specific configurations from the batch job parameters.
	 * For example - command line of the relevant binary file.
	 * @param unknown_type $paramsObject Object containing job parameters
	 */
	public abstract function config($paramsObject);
		// must be implemented by extending classes
	
	/**
	 * Will execute the virus scan for the given file path
	 * @param string $filePath
	 * @param boolean $cleanIfInfected
	 * @param string $errorDescription
	 */
	public abstract function execute($filePath, $cleanIfInfected, &$output, &$errorDescription);
		// must be implemented by extending classes

	
	
	
	
}