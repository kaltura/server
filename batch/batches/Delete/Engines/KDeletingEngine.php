<?php
/**
 * @package Scheduler
 * @subpackage Delete
 */
abstract class KDeletingEngine
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
	 * @param int $objectType of enum KalturaDeleteObjectType
	 * @return KDeletingEngine
	 */
	public static function getInstance($objectType)
	{
		switch($objectType)
		{
			case KalturaDeleteObjectType::CATEGORY_ENTRY:
				return new KDeletingCategoryEntryEngine();
				
			case KalturaDeleteObjectType::CATEGORY_USER:
				return new KDeletingCategoryUserEngine();

			case KalturaDeleteObjectType::GROUP_USER:
				return new KDeletingGroupUserEngine();

			case KalturaDeleteObjectType::CATEGORY_ENTRY_AGGREGATION:
 				return new KDeletingAggregationChannelEngine();
				
			case KalturaDeleteObjectType::USER_ENTRY :
 				return new KDeletingUserEntryEngine();
			
			default:
				return KalturaPluginManager::loadObject('KDeletingEngine', $objectType);
		}
	}
	
	/**
	 * @param int $partnerId
	 * @param KalturaDeleteJobData $jobData
  	 * @param KalturaClient $client
  	 */
	public function configure($partnerId, $jobData)
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
	public function run(KalturaFilter $filter)
	{
		KBatchBase::impersonate($this->partnerId);
		$ret = $this->delete($filter);
		KBatchBase::unimpersonate();
		
		return $ret;
	}
	
	/**
	 * @param KalturaFilter $filter The filter should return the list of objects that need to be deleted
	 * @return int the number of deleted objects
	 */
	abstract protected function delete(KalturaFilter $filter);
}