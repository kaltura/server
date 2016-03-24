<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaEntryScheduleEvent extends KalturaScheduleEvent
{
	/**
	 * Entries that associated with this event
	 * @var string
	 */
	public $entryIds;
	
	/**
	 * Categories that associated with this event
	 * @var string
	 */
	public $categoryIds;
	
	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array 
	 (	
		'entryIds',
		'categoryIds',
	 );
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}