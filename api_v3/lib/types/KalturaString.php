<?php
/**
 * A string representation to return an array of strings
 * 
 * @see KalturaStringArray
 * @package api
 * @subpackage objects
 */
class KalturaString extends KalturaObject
{
	/**
	 * @var string
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
}