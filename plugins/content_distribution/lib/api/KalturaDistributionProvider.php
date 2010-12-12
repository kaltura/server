<?php
/**
 * @abstract
 */
class KalturaDistributionProvider extends KalturaObject implements IFilterable
{
	/**
	 * @readonly
	 * @var KalturaDistributionProviderType
	 * @filter eq,in
	 */
	public $type;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var bool
	 */
	public $scheduleUpdateEnabled;
	
	/**
	 * @var bool
	 */
	public $deleteInsteadUpdate;
	
	/**
	 * @var int
	 */
	public $intervalBeforeSunrise;
	
	/**
	 * @var int
	 */
	public $intervalBeforeSunset;
	
	/**
	 * @var string
	 */
	public $updateRequiredEntryFields;
	
	/**
	 * @var string
	 */
	public $updateRequiredMetadataXPaths;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'type',
		'name',
	);

	public function fromObject($sourceObject)
	{
		parent::fromObject($sourceObject);
		
		$this->scheduleUpdateEnabled = $sourceObject->isScheduleUpdateEnabled();
		$this->deleteInsteadUpdate = $sourceObject->useDeleteInsteadOfUpdate();
		$this->intervalBeforeSunrise = $sourceObject->getJobIntervalBeforeSunrise();
		$this->intervalBeforeSunset = $sourceObject->getJobIntervalBeforeSunset();
		$this->updateRequiredEntryFields = $sourceObject->getUpdateRequiredEntryFields();
		$this->updateRequiredMetadataXPaths = $sourceObject->getUpdateRequiredMetadataXPaths();
	}
	 
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array(
		);
	}
	
	public function getFilterDocs()
	{
		return array(
		);
	}
}