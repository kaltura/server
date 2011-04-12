<?php
/**
 * Class for the handling Bulk upload using SCV in the system 
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class BulkUploadEngineCsv extends KBulkUploadEngine
{
	/**
	 * 
	 * The column count (values) for the V1 CSV format
	 * @var int
	 */
	const VALUES_COUNT_V1 = 5;
	
	/**
	 * 
	 * The column count (values) for the V1 CSV format
	 * @var int
	 */
	const VALUES_COUNT_V2 = 12;
	
	protected $lineNumber = 0;
	protected $startLineNumber = 0;
	protected $csvVersion = KalturaBulkUploadCsvVersion::V1;

	/**
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaClient $kClient - the client for the engine to use
	 */
	public function __construct( KSchedularTaskConfig $taskConfig, KalturaClient $kClient )
	{
		parent::__construct($taskConfig, $kClient);
		KalturaLog::debug("Created new KBulkUploadEngineCsv");
	}
	
	
	/**
	 * @param array $fields
	 * @return string
	 */
	protected function getDateFormatRegex(&$fields = null)
	{
		$replace = array(
			'%Y' => '([1-2][0-9]{3})',
			'%m' => '([0-1][0-9])',
			'%d' => '([0-3][0-9])',
			'%H' => '([0-2][0-9])',
			'%i' => '([0-5][0-9])',
			'%s' => '([0-5][0-9])',
//			'%T' => '([A-Z]{3})',
		);
	
		$fields = array();
		$arr = null;
		if(!preg_match_all('/%([YmdTHis])/', self::BULK_UPLOAD_DATE_FORMAT, $arr))
			return false;
	
		$fields = $arr[1];
		
		return '/' . str_replace(array_keys($replace), $replace, self::BULK_UPLOAD_DATE_FORMAT) . '/';
	}
	
	/**
	 * @param string $str
	 * @return boolean
	 */
	protected function isFormatedDate($str)
	{
		$regex = $this->getDateFormatRegex();
		
		return preg_match($regex, $str);
	}
	
	/**
	 * @param string $str
	 * @return int
	 */
	protected function parseFormatedDate($str)
	{
		KalturaLog::debug("parseFormatedDate($str)");
		
		if(function_exists('strptime'))
		{
			$ret = strptime($str, self::BULK_UPLOAD_DATE_FORMAT);
			if($ret)
			{
				KalturaLog::debug("Formated Date [$ret] " . date('Y-m-d\TH:i:s', $ret));
				return $ret;
			}
		}
			
		$fields = null;
		$regex = $this->getDateFormatRegex($fields);
		
		$values = null;
		if(!preg_match($regex, $str, $values))
			return null;
			
		$hour = 0;
		$minute = 0;
		$second = 0;
		$month = 0;
		$day = 0;
		$year = 0;
		$is_dst = 0;
		
		foreach($fields as $index => $field)
		{
			$value = $values[$index + 1];
			
			switch($field)
			{
				case 'Y':
					$year = intval($value);
					break;
					
				case 'm':
					$month = intval($value);
					break;
					
				case 'd':
					$day = intval($value);
					break;
					
				case 'H':
					$hour = intval($value);
					break;
					
				case 'i':
					$minute = intval($value);
					break;
					
				case 's':
					$second = intval($value);
					break;
					
//				case 'T':
//					$date = date_parse($value);
//					$hour -= ($date['zone'] / 60);
//					break;
					
			}
		}
		
		KalturaLog::debug("gmmktime($hour, $minute, $second, $month, $day, $year)");
		$ret = gmmktime($hour, $minute, $second, $month, $day, $year);
		if($ret)
		{
			KalturaLog::debug("Formated Date [$ret] " . date('Y-m-d\TH:i:s', $ret));
			return $ret;
		}
			
		KalturaLog::debug("Formated Date [null]");
		return null;
	}
		
	/**
	 * @param string $str
	 * @return boolean
	 */
	protected function isUrl($str)
	{
		KalturaLog::debug("isUrl($str)");
		
		$str = KCurlWrapper::encodeUrl($str);
		
		$strRegex = "^((https?)|(ftp)):\\/\\/" . "?(([0-9a-z_!~*'().&=+$%-]+:)?[0-9a-z_!~*'().&=+$%-]+@)?" . //user@
					"(([0-9]{1,3}\\.){3}[0-9]{1,3}" . // IP- 199.194.52.184
					"|" . // allows either IP or domain
					"([0-9a-z_!~*'()-]+\\.)*" . // tertiary domain(s)- www.
					"([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\\." . // second level domain
					"[a-z]{2,6})" . // first level domain- .com or .museum
					"(:[0-9]{1,4})?" . // port number- :80
					"((\\/?)|" . // a slash isn't required if there is no file name
					"(\\/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+)$";
		
		return preg_match("/$strRegex/i", $str);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KBulkUploadEngine::validate()
	 */
	public function validate(KalturaBatchJob $job, KalturaBulkUploadCsvJobData $data )
	{
		//TODO: add validation for CSV - take from the parse
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KBulkUploadEngine::parse()
	 */
	public function parse(KalturaBatchJob $job, KalturaBulkUploadCsvJobData $data )
	{
		$fileHandle = $this->getFileHandle($job, $data);
		$values = fgetcsv($fileHandle);
		
		while($values)
		{
			$this->sendChunkedData($job);
					
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
			
			// creates a result object
			$bulkUploadResult =  $this->createUploadResult($job, $this->lineNumber, $values, $columns);
				    		    
		    if(is_null($bulkUploadResult))
		    {
		    	$this->multiRequestCounter++; //If the result is not valid we add another item to the multiRequest 
		    }
			else // store the valid results in the $bulkUploadResults 
			{
				$bulkUploadResults[] = $bulkUploadResult;
			}
			
			$values = fgetcsv($fileHandle);
		}
		
		fclose($fileHandle);
		
		// send all invalid results
		$this->kClient->doMultiRequest();
		
		KalturaLog::info("Sent $this->multiRequestCounter invalid lines results");
		KalturaLog::info("CSV file parsed, $this->lineNumber lines with " . ($this->lineNumber - count($bulkUploadResults)) . ' invalid records');
		$bulkUploadJobData->csvVersion = $this->csvVersion;
		
		// reports that the parsing done
		$msg = "CSV file parsed, $this->lineNumber lines with " . ($this->lineNumber - count($bulkUploadResults)) . ' invalid records';
		$updateData = new KalturaBulkUploadCsvJobData();
		$updateData->csvVersion = $this->csvVersion;
				
		// check if job aborted
		if($this->isAborted($job))
		{
			ini_set('auto_detect_line_endings', false);
			throw new KalturaBulkUploadAbortedException("Job was aborted", KalturaBulkUploadAbortedException::ABORTED); //The job swas aborted
		}

		//Create the entries from the bulk upload results
		$isValid = $this->createEntries($bulkUploadResults, $job, $data, $this->multiRequestCounter);
		
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KBulkUploadEngine::init()
	 */
	public function init(KalturaBatchJob $job, KalturaBulkUploadCsvJobData $data )
	{
		//to support EOF of mac files
		ini_set('auto_detect_line_endings', true);
		$this->currentPartnerId = $job->partnerId;
		$this->multiRequestCounter = 0;
				
		// opens the csv file
		$fileHandle = $this->getFileHandle($job, $data);
		$this->startLineNumber = $this->getStartLineNumber($job->id);
		$this->lineNumber = 0;
		
		$this->bulkUploadResults = array();
	 
		return true;
	}
	
	/**
	 * 
	 * Closes the engine with the needed params of the batch job 
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadCsvJobData $data
	 */
	public function close(KalturaBatchJob $job, KalturaBulkUploadCsvJobData $data )
	{
		return true;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return get_class($this);
	}

	/**
	 * (non-PHPdoc)
	 * @see KBulkUploadEngine::HandleBulkUpload()
	 */
	public function handleBulkUpload( KalturaBatchJob $job, KalturaBulkUploadCsvJobData $data )
	{
		//Add logic here from old bulk upload
		parent::handleBulkUpload($job, $data);
	}

	/**
	 * 
	 * Create the entries from the given bulk upload results
	 * @param array $bulkUploadResults
	 * @param array $bulkUploadResultChunk
	 * @param KalturaBatchJob $job
	 */
	protected function createEntries(array $bulkUploadResults, KalturaBatchJob $job, KalturaBulkUploadCsvJobData $bulkUploadJobData)
	{
		// start a multi request for add entries
		$this->startMultiRequest(true);
		$multiRequestCounter = 0;
		
		KalturaLog::info("job[$job->id] start creating entries");
		$bulkUploadResultChunk = array(); // store the results of the created entries
				
		foreach($bulkUploadResults as $bulkUploadResult)
		{
			$this->sendChunkedDataForPartner($job, &$bulkUploadResultChunk);
						
			$mediaEntry = $this->createMediaEntryFromResultAndJobData($bulkUploadResult, $bulkUploadJobData);
					
			$bulkUploadResultChunk[] = $bulkUploadResult;
			
			$resource = new KalturaBulkResource();
			$resource->url = $bulkUploadResult->url;
			$resource->bulkUploadId = $job->id;
			$this->kClient->media->add($mediaEntry, $resource);
			$this->multiRequestCounter ++;
		}
		
		// commit the multi request entries
		$requestResults = $this->doMultiRequestForPartnerId();
		KalturaLog::info("job[$job->id] finish creating entries");
	
		if(count($requestResults) != count($bulkUploadResultChunk))
		{
			$err = __FILE__ . ', line: ' . __LINE__ . ' $requestResults and $$bulkUploadResultChunk must have the same size';
			throw new KalturaException($err, KalturaBatchJobAppErrors::INVLAID_BULK_REQUEST_COUNT);		
		}
		
		// saving the results with the created enrty ids
		if(count($requestResults))
		{
			$this->updateEntriesResults($requestResults, $bulkUploadResultChunk);
		}
		
		return true;
	}
	
	/**
	 * 
	 * Creates and returns a new media entry for the given job data and bulk upload result object
	 * @param unknown_type $bulkUploadResult
	 * @param KalturaBulkUploadCsvJobData $bulkUploadJobData
	 */
	protected function createMediaEntryFromResultAndJobData($bulkUploadResult, KalturaBulkUploadCsvJobData $bulkUploadJobData)
	{
		//Create the new media entry and set basic values
		$mediaEntry = new KalturaMediaEntry();
		$mediaEntry->name = $bulkUploadResult->title;
		$mediaEntry->description = $bulkUploadResult->description;
		$mediaEntry->tags = $bulkUploadResult->tags;
		$mediaEntry->userId = $bulkUploadJobData->userId;
		$mediaEntry->conversionQuality = $bulkUploadJobData->conversionProfileId;
		
		//Set values for V1 csv
		if($bulkUploadJobData->csvVersion> KalturaBulkUploadCsvVersion::V1)
		{
			if($bulkUploadResult->conversionProfileId)
		    	$mediaEntry->conversionQuality = $bulkUploadResult->conversionProfileId;
		    	
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
	 * @param KalturaBatchJob $job
	 * @param int $lineNumber
	 * @param array $values
	 * @param array $columns
	 */
	protected function createUploadResult(KalturaBatchJob $job, $lineNumber, $values, $columns)
	{
		$bulkUploadResult = new KalturaBulkUploadResult();
		$bulkUploadResult->bulkUploadJobId = $job->id;
		$bulkUploadResult->lineIndex = $lineNumber;
		$bulkUploadResult->partnerId = $job->partnerId;
		$bulkUploadResult->rowData = join(',', $values);
				
		// check variables count
		if($this->csvVersion != KalturaBulkUploadCsvVersion::V3)
		{
			if(count($values) == self::VALUES_COUNT_V2)
			{
				$this->csvVersion = KalturaBulkUploadCsvVersion::V2;
				$columns = $this->getV2Columns();
				KalturaLog::info("Columns V2:\n" . print_r($columns, true));
			}
			elseif(count($values) != self::VALUES_COUNT_V1)
			{
				// fail and continue with next line
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->errorDescription = "Wrong number of values on line $lineNumber";
				$this->addBulkUploadResult($bulkUploadResult);
				return null;
			}
		}
			
		// trim the values
		array_walk($values, array('KBulkUploadEngine', 'trimArray'));
		
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
		{	$bulkUploadResult->conversionProfileId = null;	}
			
		if(!is_numeric($bulkUploadResult->accessControlProfileId))
		{  	$bulkUploadResult->accessControlProfileId = null;	}	

		$isValid = $this->checkErrors($lineNumber, $bulkUploadResult, $scheduleStartDate, $scheduleEndDate);
		   
		if(!$isValid)
		{
			return null;  //return null for invalid object
		}
		else // store the valid results in the $bulkUploadResults 
		{
			$bulkUploadResult->scheduleStartDate = $this->parseFormatedDate($scheduleStartDate);
			$bulkUploadResult->scheduleEndDate = $this->parseFormatedDate($scheduleEndDate);
		}
			
		return $bulkUploadResult;
	}

	/**
	 * 
	 * Checks the possible errors on the upload result 
	 * @param int $lineNumber
	 * @param unknown_type $bulkUploadResult
	 * @param unknown_type $scheduleStartDate
	 * @param unknown_type $scheduleEndDate
	 */
	protected function checkErrors($lineNumber, $bulkUploadResult, $scheduleStartDate, $scheduleEndDate)
	{
		$isValid = false;
		$maxRecords = $this->taskConfig->params->maxRecords;
		
		if($lineNumber > $maxRecords) // check max records
		{
			$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
			$bulkUploadResult->errorDescription = "Exeeded max records count per bulk";
			$this->addBulkUploadResult($bulkUploadResult);
		}
		elseif(! $this->isUrl($bulkUploadResult->url)) // validates the url
		{
			$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
			$bulkUploadResult->errorDescription = "Invalid url '$bulkUploadResult->url' on line $lineNumber";
			$this->addBulkUploadResult($bulkUploadResult);
		}
		elseif($scheduleStartDate && !$this->isFormatedDate($scheduleStartDate))
		{
			$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
			$bulkUploadResult->errorDescription = "Invalid schedule start date '$scheduleStartDate' on line $lineNumber";
			$this->addBulkUploadResult($bulkUploadResult);
		}
		elseif($scheduleEndDate && !$this->isFormatedDate($scheduleEndDate))
		{
			$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
			$bulkUploadResult->errorDescription = "Invalid schedule end date '$scheduleEndDate' on line $lineNumber";
			$this->addBulkUploadResult($bulkUploadResult);
		}
		else // No errors found
		{
			$isValid = true;
		}
		
		return $isValid;
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
}

