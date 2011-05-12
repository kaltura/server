<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSchedulerWorker extends KalturaObject 
{
	/**
	 * The id of the Worker
	 * 
	 * @var int
	 * @readonly
	 */
	public $id;

	
	/**
	 * The id as configured in the batch config
	 *  
	 * @var int
	 */
	public $configuredId;

	
	
	/**
	 * The id of the Scheduler
	 * 
	 * @var int
	 */
	public $schedulerId;

	
	/**
	 * The id of the scheduler as configured in the batch config
	 *  
	 * @var int
	 */
	public $schedulerConfiguredId;
	
	
	/**
	 * The worker type
	 * 
	 * @var KalturaBatchJobType
	 */
	public $type;
	
	/**
	 * The friendly name of the type
	 * 
	 * @var string
	 */
	public $typeName;
	
	
	/**
	 * The scheduler name
	 * 
	 * @var string
	 */
	public $name;


	
	/**
	 * Array of the last statuses
	 *  
	 * @var KalturaSchedulerStatusArray
	 */
	public $statuses;


	
	/**
	 * Array of the last configs
	 *  
	 * @var KalturaSchedulerConfigArray
	 */
	public $configs;


	
	/**
	 * Array of jobs that locked to this worker
	 *  
	 * @var KalturaBatchJobArray
	 */
	public $lockedJobs;


	
	/**
	 * Avarage time between creation and queue time
	 *  
	 * @var int
	 */
	public $avgWait;


	
	/**
	 * Avarage time between queue time end finish time
	 *  
	 * @var int
	 */
	public $avgWork;


	
	/**
	 * last status time
	 *  
	 * @var int
	 */
	public $lastStatus;


	
	/**
	 * last status formated
	 *  
	 * @var string
	 */
	public $lastStatusStr;

	
	private static $mapBetweenObjects = array
	(
		"id",
		"configuredId",
		"schedulerId",
		"schedulerConfiguredId",
		"type",
		"name",
		"name",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	    
	/**
	 * @param SchedulerWorker $dbData
	 * @return KalturaScheduler
	 */
	public function fromObject($dbData)
	{
		parent::fromObject($dbData);
		
		$this->typeName = BatchJob::getTypeName($this->type);
		
		$statusesArray = $dbData->getStatuses();
		if(is_array($statusesArray))
			$this->statuses = KalturaSchedulerStatusArray::fromValuesArray($statusesArray, $this->schedulerId, $this->schedulerConfiguredId, $this->id, $this->configuredId, $this->type);
		
		$this->lastStatus = $dbData->getLastStatus(null);
		$this->lastStatusStr = date('d-m-Y H:i:s', $this->lastStatus);
		
		$this->configs = KalturaSchedulerConfigArray::fromSchedulerConfigArray($dbData->getConfigs());
		
		return $this;
	}
	    
	/**
	 * @param SchedulerWorker $dbData
	 * @return KalturaScheduler
	 */
	public function statusFromObject($dbData)
	{
		$this->fromObject($dbData);
		
		$this->configs = KalturaSchedulerConfigArray::fromSchedulerConfigArray($dbData->getConfigs());
		$this->lockedJobs = KalturaBatchJobArray::fromBatchJobArray($dbData->getLockedJobs());
		
		$this->avgWait = BatchJobPeer::doAvgTimeDiff($this->type, BatchJobPeer::CREATED_AT, BatchJobPeer::QUEUE_TIME, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$this->avgWork = BatchJobPeer::doAvgTimeDiff($this->type, BatchJobPeer::QUEUE_TIME, BatchJobPeer::FINISH_TIME, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		
		return $this;
	}

	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new SchedulerWorker();
			
		if(!is_null($this->statuses) && $this->statuses instanceof KalturaSchedulerStatusArray)
			$dbData->setStatuses($this->statuses->toValuesArray());
			
		return parent::toObject($dbData, $props_to_skip);
	}
}