<?php
/**
 * A representation of a range
 * 
 * @see KalturaRangeArray
 * @package api
 * @subpackage objects
 */
class KalturaRange extends KalturaObject
{
	/**
	 * @var int
	 */
	public $start;
    
	/**
	 * @var int
	 */
	public $end;
    
	private static $mapBetweenObjects = array
	(
		"start", "end",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}