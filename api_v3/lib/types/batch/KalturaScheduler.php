<?php
class KalturaScheduler extends KalturaObject 
{
	/**
	 * The id of the Scheduler
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
	 * The scheduler name
	 * 
	 * @var string
	 */
	public $name;

	
	/**
	 * The host name
	 * 
	 * @var string
	 */
	public $host;


	
	/**
	 * Array of the last statuses
	 *  
	 * @var KalturaSchedulerStatusArray
	 * @readonly
	 */
	public $statuses;


	
	/**
	 * Array of the last configs
	 *  
	 * @var KalturaSchedulerConfigArray
	 * @readonly
	 */
	public $configs;


	
	/**
	 * Array of the workers
	 *  
	 * @var KalturaSchedulerWorkerArray
	 * @readonly
	 */
	public $workers;


	
	/**
	 * creation time
	 *  
	 * @var int
	 * @readonly
	 */
	public $createdAt;


	
	/**
	 * last status time
	 *  
	 * @var int
	 * @readonly
	 */
	public $lastStatus;


	
	/**
	 * last status formated
	 *  
	 * @var string
	 * @readonly
	 */
	public $lastStatusStr;


	
	private static $mapBetweenObjects = array
	(
		"id",
		"configuredId",
		"name",
		"host",
		"createdAt",
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
	 * @param Scheduler $dbData
	 * @return KalturaScheduler
	 */
	public function fromObject($dbData)
	{
		parent::fromObject($dbData);
		
		$statusesArray = $dbData->getStatuses();
		if(is_array($statusesArray))
			$this->statuses = KalturaSchedulerStatusArray::fromValuesArray($statusesArray, $this->id, $this->configuredId);
		
		$this->lastStatus = $dbData->getLastStatus(null);
		$this->lastStatusStr = date('d-m-Y H:i:s', $this->lastStatus);
		
		return $this;
	}
	    
	/**
	 * @param Scheduler $dbData
	 * @return KalturaScheduler
	 */
	public function statusFromObject($dbData)
	{
		$this->fromObject($dbData);
		
		$this->workers = KalturaSchedulerWorkerArray::statusFromSchedulerWorkerArray($dbData->getWorkers());
		$this->configs = KalturaSchedulerConfigArray::fromSchedulerConfigArray($dbData->getConfigs());
		
		return $this;
	}

	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new Scheduler();
			
		if(!is_null($this->statuses) && $this->statuses instanceof KalturaSchedulerStatusArray)
			$dbData->setStatuses($this->statuses->toValuesArray());
			
		return parent::toObject($dbData, $props_to_skip);
	}
}