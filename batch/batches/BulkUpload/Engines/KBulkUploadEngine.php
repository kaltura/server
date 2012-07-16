<?php
/**
 * base class for the real KBulkUploadEngine in the system 
 * 
 * @package Scheduler
 * @subpackage BulkUpload
 * @abstract
 */
abstract class KBulkUploadEngine
{
	public static $actionsMap = array(
		KalturaBulkUploadAction::ADD => 'add',
		KalturaBulkUploadAction::UPDATE => 'update',
		KalturaBulkUploadAction::DELETE => 'delete',
		KalturaBulkUploadAction::REPLACE => 'replace',
		KalturaBulkUploadAction::TRANSFORM_XSLT => 'transformxslt'
	);
	
	const BULK_UPLOAD_DATE_FORMAT = '%Y-%m-%d';
	const BULK_UPLOAD_TIME_FORMAT = 'T%H:%i:%s';

	/**
	 * @var KSchedularTaskConfig
	 */
	protected $taskConfig;
	
	/**
	 * 
	 * The batch current partner id
	 * @var int
	 */
	protected $currentPartnerId;
	
	/**
	 * @var KalturaConfiguration
	 */
	protected $kClientConfig = null;
		
	/**
	 * @var int
	 */
	protected $multiRequestSize = 5;
	
	/**
	 * @var int
	 */
	protected $maxRecords = false;
	
	/**
	 * @var int
	 */
	protected $maxRecordsEachRun = 100;
	
	/**
	 * @var int
	 */
	protected $handledRecordsThisRun = 0;
	
	/**
	 * @var bool
	 */
	protected $exceededMaxRecordsEachRun = false;

	/**
	 * 
	 * The Engine client
	 * @var KalturaClient
	 */
	protected $kClient; 
	
	/**
	 * 
	 * @var KalturaBatchJob
	 */
	protected $job = null;
	
	/**
	 * 
	 * @var KalturaBulkUploadJobData
	 */
	protected $data = null;

	/**
	 * @param string $class enum class name
	 * @param string $value
	 * @return bool
	 */
	protected function isValidEnumValue($class, $value)
	{
		if(!class_exists($class))
			return false;
			
		$reflect = new ReflectionClass($class);
		$constants = $reflect->getConstants();
		foreach ($constants as $constant => $val)
		{
		    $constants[$constant] = strval($val);
		}
		
		if(!in_array($value, $constants))
		{
			KalturaLog::debug("Value [$value] not found in class [$class] constants [" . print_r($constants, true) . "]");
			return false;
		}
		
		return true;
	}
	
	/**
	 * @param string $str
	 * @return int
	 */
	public static function parseFormatedDate($str, $dateOnly = false)
	{
//		if(function_exists('strptime'))
//		{
//			$ret = strptime($str, self::BULK_UPLOAD_DATE_FORMAT . ($dateOnly ? '' : self::BULK_UPLOAD_TIME_FORMAT));
//			if($ret)
//			{
//			    $date = gmmktime($ret["tm_hour"], $ret["tm_min"], $ret["tm_sec"], $ret["tm_mon"], $ret["tm_mday"], $ret["tm_year"]);
//			    KalturaLog::debug("Formated Date [$date] " . date('Y-m-d\TH:i:s', $date));
//				return $date;
//			}
//		}
			
		$fields = null;
		$regex = self::getDateFormatRegex($fields, $dateOnly);
		
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
			KalturaLog::debug("Formated Date [$ret] " . gmdate('Y-m-d\TH:i:s', $ret));
			return $ret;
		}
		return null;
	}
		
	/**
	 * @param string $str
	 * @return boolean
	 */
	protected function isUrl($str)
	{
		$str = KCurlWrapper::encodeUrl($str);
		
		$strRegex = "^(?P<protocol>(https?)|(ftp)|(sftp)|(scp)):\\/\\/?" .
					"(?P<credentials>([ 0-9a-zA-Z_!~*'().&=+$%-\\[\\]]+:)?[ 0-9a-zA-Z_!~*'().&=+$%-\\[\\]]+@)?" . //user@
					"(?P<domain>([0-9]{1,3}\\.){3}[0-9]{1,3}" . // IP- 199.194.52.184
					"|" . // allows either IP or domain
					"(?P<tertiary_domain>[0-9a-zA-Z_!~*'()-]+[.])*" . // tertiary domain(s)- www.
					"(?P<second_level_domain>([0-9a-zA-Z][0-9a-zA-Z-]{0,61})?[0-9a-zA-Z])\\." . // second level domain
					"(?P<first_level_domain>[a-zA-Z]{2,6}))" . // first level domain- .com or .museum
					"([.](?P<additional_level_domain>[a-zA-Z]{2,6}))*" . // additional domain level .il
					"(?P<port>:[0-9]{1,4})?" . // port number- :80
					"(?P<path>(\\/?)|" . // a slash isn't required if there is no file name
					"(\\/[ 0-9a-zA-Z_!~*'().;?:@&=+$,%#-\\[\\]]+)+)$";
		
		return preg_match("/$strRegex/i", $str);
	}
		
	/**
	 * @param array $fields
	 * @return string
	 */
	private static function getDateFormatRegex(&$fields = null, $dateOnly = false)
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
	
		$format = self::BULK_UPLOAD_DATE_FORMAT . ($dateOnly ? '' : self::BULK_UPLOAD_TIME_FORMAT);
		
		$fields = array();
		$arr = null;
		if(!preg_match_all('/%([YmdTHis])/', $format, $arr))
			return false;
	
		$fields = $arr[1];
		
		return '/' . str_replace(array_keys($replace), $replace, $format) . '/';
	}
	
	/**
	 * @param string $str
	 * @param bool $dateOnly
	 * @return bool
	 */
	public static function isFormatedDate($str, $dateOnly = false)
	{
	    $fields = null;
		$regex = self::getDateFormatRegex($fields, $dateOnly);
		return preg_match($regex, $str);
	}
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct( KSchedularTaskConfig $taskConfig, KalturaClient $kClient, KalturaBatchJob $job)
	{
		if($taskConfig->params->multiRequestSize)
			$this->multiRequestSize = $taskConfig->params->multiRequestSize;
		if($taskConfig->params->maxRecords)
			$this->maxRecords = $taskConfig->params->maxRecords;
		if($taskConfig->params->maxRecordsEachRun)
			$this->maxRecordsEachRun = $taskConfig->params->maxRecordsEachRun;
		
		$this->kClient = $kClient;
		$this->kClientConfig = $kClient->getConfig();
		$this->taskConfig = $taskConfig;
		
		$this->job = $job;
		$this->data = $job->data;
		
		$this->currentPartnerId = $this->job->partnerId;
	}
	
	/**
	 * Will return the proper engine depending on the type (KalturaBulkUploadType)
	 *
	 * @param int $provider
	 * @param KSchedularTaskConfig $taskConfig - for the engine
	 * @param KalturaClient kClient - the client for the engine to use
	 * @return KBulkUploadEngine
	 */
	public static function getEngine($batchJobSubType, KSchedularTaskConfig $taskConfig, $kClient, KalturaBatchJob $job)
	{
		//Gets the engine from the plugin (as we moved all engines to the plugin)
		return KalturaPluginManager::loadObject('KBulkUploadEngine', $batchJobSubType, array($taskConfig, $kClient, $job));
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return get_class($this);
	}
	
	/**
	 * @return KalturaClient
	 */
	public function getClient()
	{
		return $this->kClient;
	}
	
	/**
	 * @return KalturaBatchJob
	 */
	public function getJob()
	{
		return $this->job;
	}

	/**
	 * @return KalturaBulkUploadJobData
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return bool
	 */
	public function shouldRetry()
	{
		return $this->exceededMaxRecordsEachRun;
	}

	/**
	 * 
	 * Impersonates into the current partner (overrides the batch partner) 
	 */
	public function impersonate()
	{
		$this->kClientConfig->partnerId = $this->currentPartnerId;
		$this->kClient->setConfig($this->kClientConfig);
	}
	
	public function unimpersonate()
	{
		$this->kClientConfig->partnerId = $this->taskConfig->getPartnerId();
		$this->kClient->setConfig($this->kClientConfig);
	}
		
	/**
	 * 
	 * Handles the bulk upload
	 */
	abstract public function handleBulkUpload();
			
	/**
	 * 
	 * Adds a bulk upload result
	 * @param KalturaBulkUploadResult $bulkUploadResult
	 */
	protected function addBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult)
	{
		$pluginsData = $bulkUploadResult->pluginsData;
		$bulkUploadResult->pluginsData = null;
		$this->kClient->batch->addBulkUploadResult($bulkUploadResult, $pluginsData);
	}

	/**
	 * 
	 * Gets the start line number for the given job id
	 * @return int - the start line for the job id
	 */
	protected function getStartIndex()
	{
		try{
			$result = $this->kClient->batch->getBulkUploadLastResult($this->job->id);
			if($result)
				return $result->lineIndex;
		}
		catch(Exception $e){
			KalturaLog::notice("getBulkUploadLastResult: " . $e->getMessage());
		}
		return 0;
	}
	
	/**
	 * save the results for returned created entries
	 * 
	 * @param array $requestResults
	 * @param array $bulkUploadResults
	 */
	protected function updateObjectsResults(array $requestResults, array $bulkUploadResults)
	{
		
	}
	
	/**
	 * 
	 * Checks if the job was aborted (throws exception if so)
	 * @throws KalturaBulkUploadAbortedException
	 */
	protected function checkAborted()
	{
		if($this->kClient->isMultiRequest())
			return false;
			
		$batchJobResponse = $this->kClient->jobs->getBulkUploadStatus($this->job->id);
		$updatedJob = $batchJobResponse->batchJob;
		if($updatedJob->abort)
		{
			KalturaLog::info("job[{$this->job->id}] aborted");
				
			//Throw exception and close the job from the outside 
			throw new KalturaBulkUploadAbortedException("Job was aborted", KalturaBulkUploadAbortedException::JOB_ABORTED);
		}
		return false;
	}
}