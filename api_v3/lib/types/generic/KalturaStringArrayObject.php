<?php
/**
 * A wrapper object for KalturaStringArray to be used in arrays
 * 
 * @package api
 * @subpackage objects
 */
class KalturaStringArrayObject extends KalturaObject
{
	/**
	 * @var KalturaStringArray
	 */
	public $value;
	
	private static $mapBetweenObjects = array
	(
		"value",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if($this->value)
		{
			return $this->value->toObjectsArray();
		}
		return array();
	}
}
