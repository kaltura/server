<?php

/**
 * 
 * Represents Kaltura's Tests General Data 
 * @author Roni
 *
 */
class KalturaTestsGeneralData
{
	/**
	 * 
	 * The general data file
	 * @var unknown_type
	 */
	public $generalDataFile = null;

	static public function get($dataId)
	{
		if($this->generalDataFile == NULL)
		{
			$this->generalDataFile = new KalturaTestConfig(dirname(__FILE__) . DIRECTORY_SEPARATOR . "generalData.ini");
		}
		
		if(isset($this->generalDataFile->$dataId))
		{
			return $this->generalDataFile->$dataId;
		}
		
		return ''; 
	}
	
	static public function set($dataId, $dataValue)
	{
		if($this->generalDataFile == NULL)
		{
			$this->generalDataFile = new KalturaTestConfig(dirname(__FILE__) . DIRECTORY_SEPARATOR . "generalData.ini");
		}
		
		$this->generalDataFile->$dataId = $dataValue;
		$this->generalDataFile->saveToIni();
	}
}