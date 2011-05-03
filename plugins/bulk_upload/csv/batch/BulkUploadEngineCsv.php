<?php
/**
 * Class for the handling Bulk upload using SCV in the system 
 * 
 * @package plugins.bulkUploadCsv
 * @subpackage batch
 */
class BulkUploadEngineCsv extends KBulkUploadEngine
{
	/**
	 * The column count (values) for the V1 CSV format
	 * @var int
	 */
	const VALUES_COUNT_V1 = 5;
	
	/**
	 * The column count (values) for the V1 CSV format
	 * @var int
	 */
	const VALUES_COUNT_V2 = 12;
	
	/**
	 * The bulk upload results
	 * @var array
	 */
	private $bulkUploadResults = array();
		
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
			if($this->lineNumber < $startLineNumber)
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
		$this->createEntries();
	}
	
	/**
	 * 
	 * Create the entries from the given bulk upload results
	 */
	protected function createEntries()
	{
		// start a multi request for add entries
		$this->startMultiRequest(true);
		
		KalturaLog::info("job[{$this->job->id}] start creating entries");
		$bulkUploadResultChunk = array(); // store the results of the created entries
				
		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			$mediaEntry = $this->createMediaEntryFromResultAndJobData($bulkUploadResult);
					
			$bulkUploadResultChunk[] = $bulkUploadResult;
			
			$resource = new KalturaUrlResource();
			$resource->url = $bulkUploadResult->url;
			$this->kClient->media->add($mediaEntry, $resource);
			
			if($this->kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				$requestResults = $this->kClient->doMultiRequest();
				$this->impersonate();
				$this->updateEntriesResults($requestResults, $bulkUploadResultChunk);
				$this->checkAborted();
				$this->startMultiRequest(true);
				$bulkUploadResultChunk = array();
			}
		}
		
		// commit the multi request entries
		$requestResults = $this->kClient->doMultiRequest();
		$this->impersonate();
		if(count($requestResults))
			$this->updateEntriesResults($requestResults, $bulkUploadResultChunk);

		KalturaLog::info("job[{$this->job->id}] finish creating entries");
	}
	
	/**
	 * 
	 * Creates and returns a new media entry for the given job data and bulk upload result object
	 * @param unknown_type $bulkUploadResult
	 */
	protected function createMediaEntryFromResultAndJobData($bulkUploadResult)
	{
		//Create the new media entry and set basic values
		$mediaEntry = new KalturaMediaEntry();
		$mediaEntry->name = $bulkUploadResult->title;
		$mediaEntry->description = $bulkUploadResult->description;
		$mediaEntry->tags = $bulkUploadResult->tags;
		$mediaEntry->userId = $this->data->userId;
		$mediaEntry->ingestionProfileId = $this->data->conversionProfileId;
		
		//Set values for V1 csv
		if($this->csvVersion > KalturaBulkUploadCsvVersion::V1)
		{
			if($bulkUploadResult->conversionProfileId)
		    	$mediaEntry->ingestionProfileId = $bulkUploadResult->conversionProfileId;
		    	
			if($bulkUploadResult->accessControlProfileId)
		    	$mediaEntry->accessControlId = $bulkUploadResult->accessControlProfileId;
		    	
		    if($bulkUploadResult->category)
		    	$mediaEntry->categories = $bulkUploadResult->category;
		    	
		    if($bulkUploadResult->scheduleStartDate)
		    	$mediaEntry->startDate = $bulkUploadResult->scheduleStartDate;
		    	
		    if($bulkUploadResult->scheduleEndDate)
		    	$mediaEntry->endDate = $bulkUploadResult->scheduleEndDate;
		    	
		    if($bulkUploadResult->thumbnailUrl)
		    	$mediaEntry->thumbnailUrl = $bulkUploadResult->thumbnailUrl;
		    	
		    if($bulkUploadResult->partnerData)
		    	$mediaEntry->partnerData = $bulkUploadResult->partnerData;
		}
			
		//Set the content type
		switch(strtolower($bulkUploadResult->contentType))
		{
			case 'image':
				$mediaEntry->mediaType = KalturaMediaType::IMAGE;
				break;
			
			case 'audio':
				$mediaEntry->mediaType = KalturaMediaType::AUDIO;
				break;
			
			default:
				$mediaEntry->mediaType = KalturaMediaType::VIDEO;
				break;
		}	
		
		return $mediaEntry;
	}

	/**
	 * 
	 * Creates a new upload result object from the given parameters
	 * @param array $values
	 * @param array $columns
	 */
	protected function createUploadResult($values, $columns)
	{
		if($this->handledRecordsThisRun > $this->maxRecordsEachRun)
		{
			$this->exceededMaxRecordsEachRun = true;
			return;
		}
		$this->handledRecordsThisRun++;
		
		$bulkUploadResult = new KalturaBulkUploadResult();
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		$bulkUploadResult->lineIndex = $this->lineNumber;
		$bulkUploadResult->partnerId = $this->job->partnerId;
		$bulkUploadResult->rowData = join(',', $values);
				
		// Check variables count
		if($this->csvVersion != KalturaBulkUploadCsvVersion::V3)
		{
			if(count($values) == self::VALUES_COUNT_V1)
			{
				$this->csvVersion = KalturaBulkUploadCsvVersion::V1;
				$columns = $this->getV1Columns();
			}
			elseif(count($values) == self::VALUES_COUNT_V2)
			{
				$this->csvVersion = KalturaBulkUploadCsvVersion::V2;
				$columns = $this->getV2Columns();
			}
			else
			{
				// fail and continue with next line
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->errorDescription = "Wrong number of values on line $this->lineNumber";
				$this->addBulkUploadResult($bulkUploadResult);
				return;
			}
			KalturaLog::info("Columns:\n" . print_r($columns, true));
		}
				
		// trim the values
		array_walk($values, array('BulkUploadEngineCsv', 'trimArray'));
		
	    $scheduleStartDate = null;
	    $scheduleEndDate = null;
	    
		// sets the result values
		foreach($columns as $index => $column)
		{
			if(!is_numeric($index))
				continue;
				
			if($column == 'scheduleStartDate' || $column == 'scheduleEndDate')
			{
				$$column = strlen($values[$index]) ? $values[$index] : null;
				KalturaLog::info("Set value \${$column} [{$$column}]");
			}
			else
			{
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
		
		if(isset($columns['plugins']))
		{
			$bulkUploadPlugins = array();
			
			foreach($columns['plugins'] as $index => $column)
			{
				$bulkUploadPlugin = new KalturaBulkUploadPluginData();
				$bulkUploadPlugin->field = $column;
				$bulkUploadPlugin->value = iconv_strlen($values[$index], 'UTF-8') ? $values[$index] : null;
				$bulkUploadPlugins[] = $bulkUploadPlugin;
				
				KalturaLog::info("Set plugin value $column [{$bulkUploadPlugin->value}]");
			}
			
			$bulkUploadResult->pluginsData = $bulkUploadPlugins;
		}
		
		$bulkUploadResult->entryStatus = KalturaEntryStatus::IMPORT;
		
		if(!is_numeric($bulkUploadResult->conversionProfileId))
			$bulkUploadResult->conversionProfileId = null;
			
		if(!is_numeric($bulkUploadResult->accessControlProfileId))
			$bulkUploadResult->accessControlProfileId = null;	

		if($this->lineNumber > $this->maxRecords) // check max records
		{
			$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
			$bulkUploadResult->errorDescription = "Exeeded max records count per bulk";
		}
		
		if(! $this->isUrl($bulkUploadResult->url)) // validates the url
		{
			$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
			$bulkUploadResult->errorDescription = "Invalid url '$bulkUploadResult->url' on line $this->lineNumber";
		}
		
		if($scheduleStartDate && !self::isFormatedDate($scheduleStartDate))
		{
			$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
			$bulkUploadResult->errorDescription = "Invalid schedule start date '$scheduleStartDate' on line $this->lineNumber";
		}
		
		if($scheduleEndDate && !self::isFormatedDate($scheduleEndDate))
		{
			$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
			$bulkUploadResult->errorDescription = "Invalid schedule end date '$scheduleEndDate' on line $this->lineNumber";
		}
		
		if($bulkUploadResult->entryStatus == KalturaEntryStatus::ERROR_IMPORTING)
		{
			$this->addBulkUploadResult($bulkUploadResult);
			return;
		}
		
		$bulkUploadResult->scheduleStartDate = self::parseFormatedDate($scheduleStartDate);
		$bulkUploadResult->scheduleEndDate = self::parseFormatedDate($scheduleEndDate);
			
		$this->bulkUploadResults[] = $bulkUploadResult;
	}

	/**
	 * 
	 * Gets the columns for V1 csv file
	 */
	protected function getV1Columns()
	{
		return array(
			'title',
			'description',
			'tags',
			'url',
			'contentType',
		);
	}
	
	/**
	 * 
	 * Gets the columns for V2 csv file
	 */
	protected function getV2Columns()
	{
		$ret = $this->getV1Columns();
		
		$ret[] = 'conversionProfileId';
	    $ret[] = 'accessControlProfileId';
	    $ret[] = 'category';
		$ret[] = 'scheduleStartDate';
		$ret[] = 'scheduleEndDate';
	    $ret[] = 'thumbnailUrl';
	    $ret[] = 'partnerData';
			
	    return $ret;
	}
	
	/**
	 * 
	 * Gets the columns for V3 csv file (parses the header)
	 */
	protected function parseColumns($headers)
	{
		$validColumns = $this->getV2Columns();
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
