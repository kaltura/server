<?php
/**
 * @package Scheduler
 * @subpackage Index
 */
abstract class KIndexingEngine
{
	/**
	 * @var KalturaClient
	 */
	protected $client;
	
	/**
	 * @var KalturaFilterPager
	 */
	protected $pager;
	
	/**
	 * @var int
	 */
	private $lastIndexId;
	
	/**
	 * @param int $objectType of enum KalturaIndexObjectType
	 * @return KIndexingEngine
	 */
	public static function getInstance($objectType)
	{
		switch($objectType)
		{
			case KalturaIndexObjectType::ENTRY:
				return new KIndexingEntryEngine();
				
			case KalturaIndexObjectType::CATEGORY:
				return new KIndexingCategoryEngine();
				
			default:
				return KalturaPluginManager::loadObject('KIndexingEngine', $objectType);
		}
	}
	
	/**
	 * @param KalturaClient $client
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function configure(KalturaClient $client, KSchedularTaskConfig $taskConfig)
	{
		$this->client = $client;
		$this->pager = new KalturaFilterPager();
		$this->pager->pageSize = 100;
		
		if($taskConfig->params->pageSize)
			$this->pager->pageSize = $taskConfig->params->pageSize;
	}
	
	/**
	 * @param KalturaFilter $filter The filter should return the list of objects that need to be reindexed
	 * @param bool $shouldUpdate Indicates that the object columns and attributes values should be recalculated before reindexed
	 * @return int the number of indexed objects
	 */
	abstract public function index(KalturaFilter $filter, $shouldUpdate);
	
	/**
	 * @return int $lastIndexId
	 */
	public function getLastIndexId()
	{
		return $this->lastIndexId;
	}

	/**
	 * @param int $lastIndexId
	 */
	protected function setLastIndexId($lastIndexId)
	{
		$this->lastIndexId = $lastIndexId;
	}

	
	
}
