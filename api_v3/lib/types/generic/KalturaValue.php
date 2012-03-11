<?php
/**
 * A representation to return an array of values
 * 
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaValue extends KalturaObject
{
	private static $mapBetweenObjects = array
	(
		"value",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}