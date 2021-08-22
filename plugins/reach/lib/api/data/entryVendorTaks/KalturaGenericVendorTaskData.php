<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaGenericVendorTaskData extends KalturaVendorTaskData
{
	
	/**
	 * The duration of the entry for which the task was created for in milliseconds
	 * @var int
	 * @readonly
	 */
	public $entryDuration;
	
	private static $map_between_objects = array
	(
		'entryDuration',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kGenericVendorTaskData();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}
