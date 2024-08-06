<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCropAspectRatio extends KalturaObject
{

	/**
	 * @var bool
	 */
	public $crop;

	/**
	 * @var float
	 */
	public $aspectRatio;

	private static $map_between_objects = array("crop" , "aspectRatio");

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new kCropAspectRatio();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
