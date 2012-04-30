<?php
/**
 * @package Scheduler
 * @subpackage Copy
 */
abstract class KCopyingEngine
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
	private $lastCopyId;
	
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
	 * @param int $objectType of enum KalturaCopyObjectType
	 * @return KCopyingEngine
	 */
	public static function getInstance($objectType)
	{
		switch($objectType)
		{
			case KalturaCopyObjectType::CATEGORY_USER:
				return new KCopyingCategoryUserEngine();
				
			default:
				return KalturaPluginManager::loadObject('KCopyingEngine', $objectType);
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
		
		if($taskConfig->params->pageSize)
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
	 * @param KalturaFilter $filter The filter should return the list of objects that need to be copied
	 * @param KalturaObjectBase $templateObject Template object to overwrite attributes on the copied object
	 * @return int the number of copied objects
	 */
	public function run(KalturaFilter $filter, KalturaObjectBase $templateObject)
	{
		$this->impersonate();
		$ret = $this->copy($filter, $templateObject);
		$this->unimpersonate();
		
		return $ret;
	}
	
	/**
	 * @param KalturaFilter $filter The filter should return the list of objects that need to be copied
	 * @param KalturaObjectBase $templateObject Template object to overwrite attributes on the copied object
	 * @return int the number of copied objects
	 */
	abstract protected function copy(KalturaFilter $filter, KalturaObjectBase $templateObject);
	
	/**
	 * Creates a new object instance, based on source object and copied attribute from the template object
	 * @param KalturaObjectBase $sourceObject
	 * @param KalturaObjectBase $templateObject
	 * @return KalturaObjectBase
	 */
	abstract protected function getNewObject(KalturaObjectBase $sourceObject, KalturaObjectBase $templateObject);
	
	/**
	 * @return int $lastCopyId
	 */
	public function getLastCopyId()
	{
		return $this->lastCopyId;
	}

	/**
	 * @param int $lastCopyId
	 */
	protected function setLastCopyId($lastCopyId)
	{
		$this->lastCopyId = $lastCopyId;
	}

	
	
}
