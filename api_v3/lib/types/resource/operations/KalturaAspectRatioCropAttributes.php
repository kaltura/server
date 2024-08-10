<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAspectRatioCropAttributes extends KalturaDimensionsAttributes
{
	/**
	 * @var float
	 */
	public $aspectRatio;

	private static $map_between_objects = array("aspectRatio");

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new kAspectRatioCropAttributes();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
