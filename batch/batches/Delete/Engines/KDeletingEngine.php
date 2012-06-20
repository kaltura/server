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
				
			default:
				return KalturaPluginManager::loadObject('KDeletingEngine', $objectType);
		}
	}
	
	/**
	 * @param int $partnerId
	 * @param KalturaClient $client
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function configure($partnerId, KalturaClient $client, KSchedularTaskConfig $taskConfig)
	{
		$this->partnerId = $partnerId;
		$this->batchPartnerId = $taskConfig->getPartnerId();
		$this->client = $client;

		$this->pager = new KalturaFilterPager();
		$this->pager->pageSize = 100;
		
		if($taskConfig->params && $taskConfig->params->pageSize)
			$this->pager->pageSize = $taskConfig->params->pageSize;
	}
	
	protected function impersonate()
	{
		$clientConfig = $this->client->getConfig();
		$clientConfig->partnerId = $this->partnerId;
		$this->client->setConfig($clientConfig);
	}
	
	protected function unimpersonate()
	{
		$clientConfig = $this->client->getConfig();
		$clientConfig->partnerId = $this->batchPartnerId;
		$this->client->setConfig($clientConfig);
	}
	
	/**
	 * @param KalturaFilter $filter The filter should return the list of objects that need to be reindexed
	 * @param bool $shouldUpdate Indicates that the object columns and attributes values should be recalculated before reindexed
	 * @return int the number of indexed objects
	 */
	public function run(KalturaFilter $filter)
	{
		$this->impersonate();
		$ret = $this->delete($filter);
		$this->unimpersonate();
		
		return $ret;
	}
	
	/**
	 * @param KalturaFilter $filter The filter should return the list of objects that need to be deleted
	 * @return int the number of deleted objects
	 */
	abstract protected function delete(KalturaFilter $filter);
}
