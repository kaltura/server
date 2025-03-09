<?php
/**
 * @package Scheduler
 * @subpackage Delete
 */
abstract class KDeletingEngine
{
	const DELETE_BACKOFF_BULK_SIZE = 100;
	const DELETE_BACKOFF_INTERVAL = 2;
	
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
	 * The batch delete amount allowed before backing off for the time defined in $deleteOperationBackOffInterval
	 * @var int
	 */
	private $deleteOperationBulkSize;
	
	/**
	 * Backoff time for the process to not overload the service with repetitive updates
	 * @var int
	 */
	private $deleteOperationBackOffInterval;
	
	/**
	 * Amount of operations executed by the process
	 * @var int
	 */
	private static $deletedRecordCount = 0;
	
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
				
			case KalturaDeleteObjectType::USER_ENTRY:
 				return new KDeletingUserEntryEngine();
			
			case KalturaDeleteObjectType::ENTRY:
				return new KDeletingEntryEngine();
			
			case KalturaDeleteObjectType::CATEGORY_USER_SUBSCRIBER:
				return new KDeletingCategoryUserSubscriberEngine();

			case KalturaDeleteObjectType::USER_GROUP_SUBSCRIPTION:
				return new KDeletingUserGroupSubscribtionEngine();
			
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
		
		$this->deleteOperationBulkSize = self::DELETE_BACKOFF_BULK_SIZE;
		if(KBatchBase::$taskConfig->params && KBatchBase::$taskConfig->params->bulkSize)
		{
			$this->deleteOperationBulkSize = KBatchBase::$taskConfig->params->bulkSize;
		}
		
		$this->deleteOperationBackOffInterval = self::DELETE_BACKOFF_INTERVAL;
		if(KBatchBase::$taskConfig->params && KBatchBase::$taskConfig->params->backOffInterval)
		{
			$this->deleteOperationBackOffInterval = KBatchBase::$taskConfig->params->backOffInterval;
		}
		
		if(KBatchBase::$taskConfig->params && KBatchBase::$taskConfig->params->pageSize)
		{
			$this->pager->pageSize = KBatchBase::$taskConfig->params->pageSize;
		}
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
		self::$deletedRecordCount += $ret;
		if(self::$deletedRecordCount >= $this->deleteOperationBulkSize)
		{
			KalturaLog::debug("Handled [" . self::$deletedRecordCount . "] at this cycle, will now backoff for [{$this->deleteOperationBackOffInterval}]");
			self::$deletedRecordCount = 0;
			sleep($this->deleteOperationBackOffInterval);
		}
		
		KBatchBase::unimpersonate();
		
		return $ret;
	}
	
	/**
	 * @param KalturaFilter $filter The filter should return the list of objects that need to be deleted
	 * @return int the number of deleted objects
	 */
	abstract protected function delete(KalturaFilter $filter);
}