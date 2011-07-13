<?php

require_once dirname(__FILE__) . '/../bootstrap.php';

/**
 * 
 * Enables to use a global data file for retriving and setting values
 * @author Roni
 *
 */
class KalturaGlobalData extends KalturaTestConfig
{
	/**
	 * 
	 * The default location for the global data file
	 * @var string
	 */
	const DEFAULT_DATA_PATH = "global.data";
		
	/**
	 * 
	 * The data file for the global data
	 * @var KalturaTestConfig
	 */
	private static $dataFile = null;
	 
	/**
	 * 
	 * The data file path
	 * @var string
	 */
	private static $dataFilePath = null;
	
	/**
	 * 
	 * Sets data in the global file with the given name and value
	 */
	public static function setData($name, $value)
	{
		if(KalturaGlobalData::$dataFile == null)
		{
			$isInit = KalturaGlobalData::initDataFile();
			if(!$isInit)
			{
				return null;
			}
		}
		
		KalturaGlobalData::$dataFile->$name = $value; 
		KalturaGlobalData::$dataFile->saveToIniFile();
	}
	
	/**
	 * 
	 * Sets data in the global file with the given name and value
	 */
	public static function getData($name)
	{
		if(KalturaGlobalData::$dataFile == null)
		{
			$isInit = KalturaGlobalData::initDataFile();
			if(!$isInit)
				return null;
		}
			
		$value = null; 
		 
		if(is_string($name) || is_integer($name))
		{
			$value = KalturaGlobalData::$dataFile->get($name);
		}
			
		if(empty($value)) //Empty value equals null
			$value = null;
						
		return $value;
	}
	
	/**
	 * 
	 * Inits the global data file
	 * @return true - If global exists or was created, null other wise 
	 */
	private static function initDataFile()
	{
		if(is_null(KalturaGlobalData::$dataFilePath))
		{
			$classFilePath = KAutoloader::getClassFilePath("KalturaGlobalData");
			$dir = dirname($classFilePath);
			KalturaGlobalData::setDataFilePath($dir ."/" . KalturaGlobalData::DEFAULT_DATA_PATH);
		}
		
		if(file_exists(KalturaGlobalData::$dataFilePath))
			KalturaGlobalData::$dataFile = new KalturaTestConfig(KalturaGlobalData::$dataFilePath);
		else
		{
			print("Global file no found at: " . KalturaGlobalData::$dataFilePath . "\n");
			return null;
		}
			
		return true;
	}
	
	/**
	 * 
	 * Sets the global file path
	 * @param string $dataFilePath
	 */
	public static function setDataFilePath($dataFilePath)
	{
		KalturaGlobalData::$dataFilePath = $dataFilePath;
	}
	
	/**
	 * @return the $dataFilePath
	 */
	public static function getDataFilePath() {
		return KalturaGlobalData::$dataFilePath;
	}

	/**
	 * 
	 * Checks if the given name is in the global data
	 * @param string $name
	 */
	public static function isGlobalData($name)
	{
		if(KalturaGlobalData::$dataFile == null)
		{
			$isInit = KalturaGlobalData::initDataFile();
			if(!$isInit)
				return null;
		}
		
		$value = null;
		
		if(is_string($name) || is_integer($name))
		{
			$value = KalturaGlobalData::getData($name);
			KalturaLog::debug("Name [" . print_r($name,true). "] Value [" . print_r($value, true) . "]");
		}
		
		if(is_null($value))
			return false;
		
		return true;
	}
}