<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPosition extends KalturaObject
{
	/**
	 * @var float
	 */
	public $x;

	/**
	 * @var float
	 */
	public $y;

	private static $map_between_objects = array("x" , "y");

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new kPosition();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
