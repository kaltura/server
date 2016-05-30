<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 * @abstract
 */
class KalturaScheduleEventResource extends KalturaObject implements IRelatedFilterable
{
	/**
	 * @var int
	 * @filter eq,in
	 * @insertonly
	 */
	public $eventId;

	/**
	 * @var int
	 * @filter eq,in
	 * @insertonly
	 */
	public $resourceId;

	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Last update as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array 
	 (	
		'eventId',
		'resourceId',
		'partnerId',
		'createdAt',
		'updatedAt',
	 );
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('eventId');
		$this->validatePropertyNotNull('resourceId');
		return parent::validateForInsert($propertiesToSkip);
	}
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(!$sourceObject)
		{
			$sourceObject = new ScheduleEventResource();
		}
		
		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}