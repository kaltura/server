<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaBurnCaption extends KalturaObject
{
	/**
	 * @var int
	 */
	public $fontSize;

	/**
	 * @var int
	 */
	public $alignment;


	private static $map_between_objects = array
	(
		"fontSize" ,
		"alignment"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects() , self::$map_between_objects);
	}

	public function toObject($object_to_fill = null , $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new kBurnCaptions();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
