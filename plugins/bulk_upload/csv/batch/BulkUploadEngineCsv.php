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


	/**
	 *
	 * return true is CSV line has not valid info but ',' or tabs/spaces
	 */
	private static function isCsvLineEmpty($csvLine)
	{
		if(strlen(trim(str_replace(',','',$csvLine))))
		{
			return false;
		}
		return true;
	}

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

		//removing UTF-8 BOM if exists
		if (fread($fileHandle,3) != pack('CCC',0xef,0xbb,0xbf))
		{
			fseek($fileHandle,0);
		}

		$values = fgetcsv($fileHandle);
		while($values)
		{
            if(is_null(reset($values)) || self::isCsvLineEmpty(implode($values)))
            {
                $values = fgetcsv($fileHandle);
                continue;
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
				    		    
			if(KBatchBase::$kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				KBatchBase::$kClient->doMultiRequest();
				$this->checkAborted();
				KBatchBase::$kClient->startMultiRequest();
			}
			
			$values = fgetcsv($fileHandle);
		}
		
		if(!is_array($this->data->columns) || !count($this->data->columns))
		{
			foreach ($columns as $columnName)
			{
			    $columnNameObj = new KalturaString();
			    $columnNameObj->value = $columnName;
			    $this->data->columns [] = $columnNameObj;
			}
		}
		
		fclose($fileHandle);
		
		// send all invalid results
		KBatchBase::$kClient->doMultiRequest();
		
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
	 * @return KalturaBulkUploadResult
	 */
	protected function createUploadResult($values, $columns)
	{
	    if($this->handledRecordsThisRun > $this->maxRecordsEachRun)
		{
			$this->exceededMaxRecordsEachRun = true;
			return null;
		}
		$this->handledRecordsThisRun++;
		
	    $bulkUploadResult = $this->getUploadResultInstance();
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		$bulkUploadResult->lineIndex = $this->lineNumber;
		$bulkUploadResult->partnerId = $this->job->partnerId;
		//CSV files allow values to contain the "," character, on the condition that the value is surrounded by "".
		for ($index = 0; $index < count($values); $index++)
		{
		    if (strpos($values[$index], ",") !== false)
		    {
		        $values[$index] = '"'.$values[$index].'"';
		    }
		}
		$bulkUploadResult->rowData = implode(",", $values);
		
		return $bulkUploadResult;
	}


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
		$item = trim($item, " \t\n\r\0\x0B\xC2\xA0");
	}
	
	abstract protected function getUploadResultInstance ();

	protected function setResultValues($columns, $values, &$bulkUploadResult)
	{
		foreach($columns as $index => $column)
		{
			if(!is_numeric($index))
			{
				continue;
			}
			if(iconv_strlen($values[$index], 'UTF-8'))
			{
				$bulkUploadResult->$column = $values[$index];
				KalturaLog::info("Set value $column [{$bulkUploadResult->$column}]");
			}
			else
			{
				KalturaLog::info("Value $column is empty");
			}
		}
	}

	protected function handleResultError(&$bulkUploadResult, $type, $description)
	{
		$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
		$bulkUploadResult->errorType = $type;
		$bulkUploadResult->errorDescription = $description;
	}

}
