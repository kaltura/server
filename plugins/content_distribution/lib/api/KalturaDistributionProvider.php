<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaDistributionProvider extends KalturaObject implements IFilterable
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
	public $availabilityUpdateEnabled;
	
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

	public function fromObject($sourceObject, KalturaResponseProfileBase $responseProfile = null)
	{
		parent::fromObject($sourceObject, $responseProfile);
		
		if($this->shouldGet('scheduleUpdateEnabled', $responseProfile))
			$this->scheduleUpdateEnabled = $sourceObject->isScheduleUpdateEnabled();
		if($this->shouldGet('availabilityUpdateEnabled', $responseProfile))
			$this->availabilityUpdateEnabled = $sourceObject->isAvailabilityUpdateEnabled();
		if($this->shouldGet('deleteInsteadUpdate', $responseProfile))
			$this->deleteInsteadUpdate = $sourceObject->useDeleteInsteadOfUpdate();
		if($this->shouldGet('intervalBeforeSunrise', $responseProfile))
			$this->intervalBeforeSunrise = $sourceObject->getJobIntervalBeforeSunrise();
		if($this->shouldGet('intervalBeforeSunset', $responseProfile))
			$this->intervalBeforeSunset = $sourceObject->getJobIntervalBeforeSunset();
		if($this->shouldGet('updateRequiredEntryFields', $responseProfile))
			$this->updateRequiredEntryFields = $sourceObject->getUpdateRequiredEntryFields();
		if($this->shouldGet('updateRequiredMetadataXPaths', $responseProfile))
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