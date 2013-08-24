<?php
/**
 * @package Scheduler
 * @subpackage Index
 */
abstract class KIndexingEngine
{
	/**
	 * @var KalturaFilterPager
	 */
	protected $pager;
	
	/**
	 * @var int
	 */
	private $lastIndexId;
	
	/**
	 * The partner that owns the objects
	 * @var int
	 */
	private $partnerId;
	
	/**
	 * The batch system partner id
	 * @var int
	 */
	private $batchPartnerId;
	
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
				
			case KalturaIndexObjectType::LOCK_CATEGORY:
				return new KIndexingCategoryEngine();
				
			case KalturaIndexObjectType::CATEGORY_ENTRY:
				return new KIndexingCategoryEntryEngine();
				
			case KalturaIndexObjectType::CATEGORY_USER:
				return new KIndexingCategoryUserEngine();
				
			case KalturaIndexObjectType::USER:
				return new KIndexingKuserPermissionsEngine();
				
			default:
				return KalturaPluginManager::loadObject('KIndexingEngine', $objectType);
		}
	}
	
	/**
	 * @param int $partnerId
	 */
	public function configure($partnerId)
	{
		$this->partnerId = $partnerId;
		$this->batchPartnerId = KBatchBase::$taskConfig->getPartnerId();

		$this->pager = new KalturaFilterPager();
		$this->pager->pageSize = 100;
		
		if(KBatchBase::$taskConfig->params && KBatchBase::$taskConfig->params->pageSize)
			$this->pager->pageSize = KBatchBase::$taskConfig->params->pageSize;
	}
	
	/**
	 * @param KalturaFilter $filter The filter should return the list of objects that need to be reindexed
	 * @param bool $shouldUpdate Indicates that the object columns and attributes values should be recalculated before reindexed
	 * @return int the number of indexed objects
	 */
	public function run(KalturaFilter $filter, $shouldUpdate)
	{
		KBatchBase::impersonate($this->partnerId);
		$ret = $this->index($filter, $shouldUpdate);
		KBatchBase::unimpersonate();
		
		return $ret;
	}
	
	/**
	 * @param KalturaFilter $filter The filter should return the list of objects that need to be reindexed
	 * @param bool $shouldUpdate Indicates that the object columns and attributes values should be recalculated before reindexed
	 * @return int the number of indexed objects
	 */
	abstract protected function index(KalturaFilter $filter, $shouldUpdate);
	
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
