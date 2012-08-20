<?php
/**
 * Class which parses the bulk upload CSV and creates the objects listed in it. 
 * 
 * @package plugins.bulkUploadCsv
 * @subpackage batch
 */
abstract class BulkUploadEngineCsv extends KBulkUploadEngine
{
	/**
	 * The bulk upload results
	 * @var array
	 */
	protected $bulkUploadResults = array();
		
	/**
	 * @var int
	 */
	protected $lineNumber = 0;
	
	/**
	 * @var KalturaBulkUploadCsvVersion
	 */
	protected $csvVersion = KalturaBulkUploadCsvVersion::V1;

	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::handleBulkUpload()
	 */
	public function handleBulkUpload()
	{
		$startLineNumber = $this->getStartIndex($this->job->id);
	
		$filePath = $this->data->filePath;
		$fileHandle = fopen($filePath, "r");
		if(!$fileHandle)
			throw new KalturaBatchException("Unable to open file: {$filePath}", KalturaBatchJobAppErrors::BULK_FILE_NOT_FOUND); //The job was aborted
					
		KalturaLog::info("Opened file: $filePath");
		
		$columns = $this->getV1Columns();
		$values = fgetcsv($fileHandle);
		while($values)
		{
			//removing UTF-8 BOM if exists
			if(substr($values[0], 0,3) == pack('CCC',0xef,0xbb,0xbf)) { 
       			 $values[0]=substr($values[0], 3); 
    		} 
			// use version 3 (dynamic columns cassiopeia) identified by * in first char
			if(substr(trim($values[0]), 0, 1) == '*') // is a remark
			{
				$columns = $this->parseColumns($values);
				KalturaLog::info("Columns V3:\n" . print_r($columns, true));
				$this->csvVersion = KalturaBulkUploadCsvVersion::V3;
			}
			
			// ignore and continue - identified by # or *
			if(	(substr(trim($values[0]), 0, 1) == '#') || // is a remark OR
				(substr(trim($values[0]), 0, 1) == '*'))  //is version identifier
			{
				$values = fgetcsv($fileHandle);
				continue;
			}
			
			$this->lineNumber ++;
			if($this->lineNumber <= $startLineNumber)
			{
				$values = fgetcsv($fileHandle);
				continue;
			}
			
			// creates a result object
			$this->createUploadResult($values, $columns);
			if($this->exceededMaxRecordsEachRun)
				break;
				    		    
			if($this->kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				$this->kClient->doMultiRequest();
				$this->checkAborted();
				$this->kClient->startMultiRequest();
			}
			
			$values = fgetcsv($fileHandle);
		}
		
		fclose($fileHandle);
		
		// send all invalid results
		$this->kClient->doMultiRequest();
		
		KalturaLog::info("CSV file parsed, $this->lineNumber lines with " . ($this->lineNumber - count($this->bulkUploadResults)) . ' invalid records');
		
		// update csv verision on the job
		$this->data->csvVersion = $this->csvVersion;
				
		//Check if job aborted
		$this->checkAborted();

		//Create the entries from the bulk upload results
		$this->createObjects();
	}
		
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::addBulkUploadResult()
	 */
	protected function addBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult)
	{
		parent::addBulkUploadResult($bulkUploadResult);
			
	}
	
	/**
	 * 
	 * Create the entries from the given bulk upload results
	 */
	abstract protected function createObjects();
	
	/**
	 * 
	 * Creates a new upload result object from the given parameters
	 * @param array $values
	 * @param array $columns
	 */
	abstract protected function createUploadResult($values, $columns);


	/**
	 * 
	 * Gets the columns for V1 csv file
	 */
	protected function getV1Columns()
	{
		
	}
	
	/**
	 * 
	 * Gets the columns for V2 csv file
	 */
	protected function getV2Columns()
	{
		
	}
	
	abstract protected function getColumns ();

	
	/**
	 * 
	 * Gets the columns for V3 csv file (parses the header)
	 */
	protected function parseColumns($headers)
	{
		$validColumns = $this->getColumns();
		$ret = array();
		$plugins = array();
		
		foreach($headers as $index => $header)
		{
			$header = trim($header, '* ');
			if(in_array($header, $validColumns))
			{
				$ret[$index] = $header;
			}
			else
			{
				$plugins[$index] = $header;
			}
		}
		
		if(count($plugins))
			$ret['plugins'] = $plugins;
			
		return $ret;
	}

	/**
	 * @param string $item
	 */
	protected function trimArray(&$item)
	{
		$item = trim($item);
	}
}
